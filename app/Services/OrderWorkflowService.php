<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentGatewayProvider;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Models\ApprovalRequest;
use App\Models\Conversation;
use App\Models\DoctorProfile;
use App\Models\Invoice;
use App\Models\Lab;
use App\Models\Order;
use App\Models\OrderAttachment;
use App\Models\OrderLog;
use App\Models\OrderStage;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrderWorkflowService
{
    public function __construct(
        protected CommissionService $commissionService,
        protected NotificationService $notificationService,
    ) {}

    public function createOrder(
        User $doctor,
        array $data,
        PaymentGatewayProvider|string|null $paymentMethod = null,
        ?string $paymentReference = null,
    ): Order {
        $paymentMethod = $paymentMethod instanceof PaymentGatewayProvider
            ? $paymentMethod
            : PaymentGatewayProvider::tryFrom((string) ($paymentMethod ?? PaymentGatewayProvider::Wallet->value))
                ?? PaymentGatewayProvider::Wallet;

        return DB::transaction(function () use ($doctor, $data, $paymentMethod, $paymentReference) {
            $service = \App\Models\LabService::query()->findOrFail($data['lab_service_id']);
            $lab = Lab::query()->findOrFail($data['lab_id']);

            if ($service->lab_id !== $lab->id) {
                throw new \InvalidArgumentException('Selected service does not belong to the chosen lab.');
            }

            $cost = (float) $service->price + (($data['is_express'] ?? false) ? $this->commissionService->expressSurcharge() : 0);
            $commission = $this->commissionService->calculate($cost, (bool) ($data['is_express'] ?? false), (bool) $lab->is_featured);
            $total = $cost + $commission;

            if ($paymentMethod === PaymentGatewayProvider::Wallet) {
                $this->chargeWallet($doctor, $total, null);
            }

            $predictedDelivery = $data['expected_delivery_at']
                ?? now()->addDays($service->turnaround_days + (($data['is_express'] ?? false) ? -2 : 0));

            $order = Order::query()->create([
                'order_number' => 'ORD-'.random_int(1000, 9999),
                'doctor_id' => $doctor->id,
                'lab_id' => $lab->id,
                'lab_service_id' => $service->id,
                'service_name' => $service->name,
                'tooth_number' => $data['tooth_number'] ?? null,
                'material' => $data['material'] ?? null,
                'shade' => $data['shade'] ?? null,
                'status' => OrderStatus::Received,
                'cost' => $cost,
                'commission' => $commission,
                'total' => $total,
                'is_express' => $data['is_express'] ?? false,
                'turnaround_days' => $service->turnaround_days,
                'expected_delivery_at' => $predictedDelivery,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->createStages($order);
            $this->persistAttachments($order, $data['attachments'] ?? []);
            $this->createInvoice($order);
            $this->recordPayment($doctor, $order, $total, $paymentMethod, $paymentReference);
            $this->createConversation($order);

            $paymentLabel = $paymentMethod->label();
            OrderLog::query()->create([
                'order_id' => $order->id,
                'user_id' => $doctor->id,
                'icon' => '📩',
                'message' => "Order received and payment confirmed via {$paymentLabel}",
            ]);

            $this->notificationService->notifyOrderUpdate(
                $doctor->id,
                $order->order_number,
                'Your order has been submitted successfully.',
                '✅'
            );

            if ($lab->user_id) {
                $this->notificationService->notifyOrderUpdate(
                    $lab->user_id,
                    $order->order_number,
                    'New order received from Dr. '.$doctor->name,
                    '🔬'
                );
            }

            return $order;
        });
    }

    public function advanceStage(Order $order, ?int $userId = null): void
    {
        $next = match ($order->status) {
            OrderStatus::Received => OrderStatus::InProgress,
            OrderStatus::InProgress => OrderStatus::QualityReview,
            OrderStatus::QualityReview => OrderStatus::Shipping,
            OrderStatus::Shipping => OrderStatus::Delivered,
            OrderStatus::Delivered => OrderStatus::Completed,
            default => $order->status,
        };

        if ($next === $order->status) {
            return;
        }

        $order->update(['status' => $next, 'delivered_at' => $next === OrderStatus::Completed ? now() : $order->delivered_at]);

        $order->stages()->where('is_current', true)->update([
            'is_current' => false,
            'completed_at' => now(),
            'lab_approved_at' => now(),
        ]);

        $currentStage = $order->stages()->where('status', $next)->first();
        if ($currentStage) {
            $currentStage->update(['is_current' => true, 'lab_approved_at' => now()]);
        }

        OrderLog::query()->create([
            'order_id' => $order->id,
            'user_id' => $userId,
            'icon' => '⚙️',
            'message' => 'Status updated to '.$next->label(),
        ]);

        $this->notificationService->notifyOrderUpdate(
            $order->doctor_id,
            $order->order_number,
            'Order moved to '.$next->label().'.',
            '📋'
        );
    }

    public function approveQualityStage(Order $order, int $doctorId): void
    {
        if ($order->status !== OrderStatus::QualityReview) {
            throw new \InvalidArgumentException('Order is not in quality review stage.');
        }

        $stage = $order->stages()->where('status', OrderStatus::QualityReview)->first();
        $stage?->update(['doctor_approved_at' => now()]);

        OrderLog::query()->create([
            'order_id' => $order->id,
            'user_id' => $doctorId,
            'icon' => '✅',
            'message' => 'Doctor approved quality review stage',
        ]);

        $this->advanceStage($order, $doctorId);

        if ($order->lab?->user_id) {
            $this->notificationService->notifyOrderUpdate(
                $order->lab->user_id,
                $order->order_number,
                'Doctor approved quality stage. Proceed with shipping.',
                '✅'
            );
        }
    }

    protected function createStages(Order $order): void
    {
        $stages = [
            [OrderStatus::Received, 'Order Received', 1],
            [OrderStatus::InProgress, 'In Production', 2],
            [OrderStatus::QualityReview, 'Quality Review', 3],
            [OrderStatus::Shipping, 'Shipping', 4],
            [OrderStatus::Delivered, 'Final Delivery', 5],
        ];

        foreach ($stages as [$status, $label, $sort]) {
            OrderStage::query()->create([
                'order_id' => $order->id,
                'status' => $status,
                'label' => $label,
                'sort_order' => $sort,
                'is_current' => $sort === 1,
                'completed_at' => $sort === 1 ? now() : null,
                'expected_at' => $order->expected_delivery_at,
            ]);
        }
    }

    protected function persistAttachments(Order $order, array $paths): void
    {
        foreach ($paths as $path) {
            $type = str_contains(strtolower($path), '.mp4') ? 'video' : (str_contains(strtolower($path), '.stl') ? '3d' : 'image');

            OrderAttachment::query()->create([
                'order_id' => $order->id,
                'path' => $path,
                'type' => $type,
                'original_name' => basename($path),
            ]);
        }
    }

    protected function createInvoice(Order $order): Invoice
    {
        return Invoice::query()->create([
            'invoice_number' => 'INV-'.random_int(10000, 99999),
            'order_id' => $order->id,
            'doctor_id' => $order->doctor_id,
            'lab_id' => $order->lab_id,
            'amount' => $order->cost,
            'commission' => $order->commission,
            'total' => $order->total,
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    protected function recordPayment(
        User $doctor,
        Order $order,
        float $total,
        PaymentGatewayProvider $paymentMethod = PaymentGatewayProvider::Wallet,
        ?string $paymentReference = null,
    ): void {
        $wallet = Wallet::query()->firstOrCreate(
            ['user_id' => $doctor->id],
            ['balance' => 0, 'currency' => 'USD']
        );

        $via = $paymentMethod->label();

        Transaction::query()->create([
            'wallet_id' => $wallet->id,
            'order_id' => $order->id,
            'type' => TransactionType::Payment,
            'description' => "{$order->service_name} — {$order->lab?->name} ({$via})",
            'amount' => -$total,
            'status' => PaymentStatus::Completed,
            'reference' => $paymentReference,
        ]);
    }

    protected function chargeWallet(User $doctor, float $total, ?Order $order): void
    {
        $wallet = Wallet::query()->firstOrCreate(
            ['user_id' => $doctor->id],
            ['balance' => 0, 'currency' => 'USD']
        );

        if ($wallet->balance < $total) {
            throw new \InvalidArgumentException('Insufficient wallet balance. Please deposit funds first.');
        }

        $wallet->decrement('balance', $total);
    }

    protected function createConversation(Order $order): void
    {
        Conversation::query()->firstOrCreate(
            [
                'doctor_id' => $order->doctor_id,
                'lab_id' => $order->lab_id,
                'order_id' => $order->id,
            ],
            [
                'subject' => "Order #{$order->order_number}",
                'last_message_at' => now(),
            ]
        );
    }
}
