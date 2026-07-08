<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Enums\UserRole;
use App\Models\AppNotification;
use App\Models\ApprovalRequest;
use App\Models\CommissionSetting;
use App\Models\Conversation;
use App\Models\DoctorProfile;
use App\Models\Lab;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\OrderStage;
use App\Models\PaymentMethod;
use App\Models\Rating;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DentaLinkSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $admin = User::create([
            'name' => 'Platform Admin',
            'email' => 'admin@dentalink.com',
            'password' => $password,
            'role' => UserRole::Admin,
            'is_verified' => true,
            'locale' => 'en',
        ]);

        $doctor = User::create([
            'name' => 'Dr. Ahmed Al-Saeed',
            'email' => 'doctor@dentalink.com',
            'password' => $password,
            'role' => UserRole::Doctor,
            'phone' => '+974 5555 1234',
            'country' => 'Qatar',
            'is_verified' => true,
            'locale' => 'en',
        ]);

        DoctorProfile::create([
            'user_id' => $doctor->id,
            'license_number' => 'QDA-2019-4521',
            'specialization' => 'Prosthodontics',
            'clinic_name' => 'Al-Saeed Dental Clinic',
            'bio' => 'Specialist in crowns, bridges, and implant restorations.',
        ]);

        $labsData = [
            [
                'name' => 'Doha Specialized Lab',
                'country' => 'Qatar',
                'city' => 'Doha',
                'rating' => 4.9,
                'avg_turnaround_days' => 5,
                'starting_price' => 240,
                'is_featured' => true,
                'specialties' => ['Crown', 'Bridge', 'Implant'],
                'email' => 'lab.doha@dentalink.com',
            ],
            [
                'name' => 'Gulf Dental Lab',
                'country' => 'UAE',
                'city' => 'Dubai',
                'rating' => 4.7,
                'avg_turnaround_days' => 7,
                'starting_price' => 200,
                'specialties' => ['Crown', 'Veneer'],
                'email' => 'lab.gulf@dentalink.com',
            ],
            [
                'name' => 'ProDental Qatar',
                'country' => 'Qatar',
                'city' => 'Al Rayyan',
                'rating' => 4.8,
                'avg_turnaround_days' => 6,
                'starting_price' => 260,
                'specialties' => ['Crown', 'Bridge', 'Denture'],
                'email' => 'lab.prodental@dentalink.com',
            ],
            [
                'name' => 'Gulf Region Lab',
                'country' => 'Saudi Arabia',
                'city' => 'Riyadh',
                'rating' => 4.5,
                'avg_turnaround_days' => 8,
                'starting_price' => 180,
                'specialties' => ['Implant', 'Bridge'],
                'email' => 'lab.gulfregion@dentalink.com',
            ],
            [
                'name' => 'European Dental Studio',
                'country' => 'Germany',
                'city' => 'Berlin',
                'rating' => 5.0,
                'avg_turnaround_days' => 14,
                'starting_price' => 400,
                'specialties' => ['Veneer', 'Crown', 'E-Max'],
                'email' => 'lab.european@dentalink.com',
            ],
            [
                'name' => 'Jordan Advanced Lab',
                'country' => 'Jordan',
                'city' => 'Amman',
                'rating' => 4.3,
                'avg_turnaround_days' => 9,
                'starting_price' => 150,
                'specialties' => ['Denture', 'Crown'],
                'email' => 'lab.jordan@dentalink.com',
            ],
        ];

        $labs = [];
        foreach ($labsData as $index => $data) {
            $labUser = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $password,
                'role' => UserRole::Lab,
                'country' => $data['country'],
                'is_verified' => true,
                'locale' => 'en',
            ]);

            $specialties = $data['specialties'];
            unset($data['specialties'], $data['email']);

            $labs[] = Lab::create([
                ...$data,
                'user_id' => $labUser->id,
                'approval_status' => ApprovalStatus::Approved,
                'is_active' => true,
                'specialties' => $specialties,
                'description' => 'Certified dental laboratory specializing in high-quality restorations.',
            ]);
        }

        $ordersData = [
            ['ORD-2847', 0, 'Full Zirconia', OrderStatus::InProgress, 200, 4, true, '2025-07-25'],
            ['ORD-2845', 1, 'Acrylic Partial Denture 3 to 6 Units', OrderStatus::QualityReview, 250, 4, false, '2025-07-22'],
            ['ORD-2841', 3, 'Hybrid Denture Over Implant', OrderStatus::Shipping, 600, 4, false, '2025-07-20'],
            ['ORD-2838', 2, 'Snap On (Upper and Lower)', OrderStatus::Completed, 500, 4, false, '2025-07-17'],
            ['ORD-2831', 0, 'PFM Crown', OrderStatus::Completed, 150, 4, false, '2025-07-10'],
        ];

        $orders = [];
        foreach ($ordersData as [$number, $labIndex, $service, $status, $cost, $days, $express, $delivery]) {
            $commission = round($cost * 0.05, 2);
            $order = Order::create([
                'order_number' => $number,
                'doctor_id' => $doctor->id,
                'lab_id' => $labs[$labIndex]->id,
                'service_name' => $service,
                'tooth_number' => '14 — Upper Left',
                'material' => 'Zirconia 3Y',
                'shade' => 'A2',
                'status' => $status,
                'cost' => $cost,
                'commission' => $commission,
                'total' => $cost + $commission,
                'is_express' => $express,
                'turnaround_days' => $days,
                'expected_delivery_at' => $delivery,
                'delivered_at' => $status === OrderStatus::Completed ? $delivery : null,
            ]);
            $orders[] = $order;

            $stages = [
                [OrderStatus::Received, 'Order Received', 1],
                [OrderStatus::InProgress, 'In Production', 2],
                [OrderStatus::QualityReview, 'Quality Review', 3],
                [OrderStatus::Shipping, 'Shipping', 4],
                [OrderStatus::Delivered, 'Final Delivery', 5],
            ];

            $currentIndex = match ($status) {
                OrderStatus::InProgress => 2,
                OrderStatus::QualityReview => 3,
                OrderStatus::Shipping => 4,
                OrderStatus::Completed => 5,
                default => 1,
            };

            foreach ($stages as $i => [$stageStatus, $label, $sort]) {
                OrderStage::create([
                    'order_id' => $order->id,
                    'status' => $stageStatus,
                    'label' => $label,
                    'sort_order' => $sort,
                    'completed_at' => $sort < $currentIndex ? now()->subDays(5 - $sort) : null,
                    'expected_at' => now()->addDays($sort * 2),
                    'is_current' => $sort === $currentIndex,
                ]);
            }
        }

        OrderLog::create(['order_id' => $orders[0]->id, 'icon' => '🔬', 'message' => 'Quality review started by the lab', 'created_at' => now()->subHours(2)]);
        OrderLog::create(['order_id' => $orders[0]->id, 'icon' => '⚙️', 'message' => 'Manufacturing completed — ready for review', 'created_at' => now()->subDay()]);
        OrderLog::create(['order_id' => $orders[0]->id, 'icon' => '📐', 'message' => 'Measurements and mold confirmed', 'created_at' => now()->subDays(2)]);
        OrderLog::create(['order_id' => $orders[0]->id, 'icon' => '📩', 'message' => 'Order received successfully', 'created_at' => now()->subDays(3)]);

        Rating::create(['order_id' => $orders[4]->id, 'doctor_id' => $doctor->id, 'lab_id' => $labs[0]->id, 'score' => 5, 'review' => 'Excellent quality and fast delivery.']);
        Rating::create(['order_id' => $orders[3]->id, 'doctor_id' => $doctor->id, 'lab_id' => $labs[2]->id, 'score' => 4, 'review' => 'Good veneers, minor shade adjustment needed.']);

        $wallet = Wallet::create(['user_id' => $doctor->id, 'balance' => 1240.00, 'currency' => 'USD']);

        Transaction::create(['wallet_id' => $wallet->id, 'order_id' => $orders[0]->id, 'type' => TransactionType::Payment, 'description' => 'Full Zirconia — Doha Specialized Lab', 'amount' => -210, 'status' => PaymentStatus::Completed, 'created_at' => now()->subDays(4)]);
        Transaction::create(['wallet_id' => $wallet->id, 'type' => TransactionType::Deposit, 'description' => 'Balance deposit', 'amount' => 500, 'status' => PaymentStatus::Completed, 'created_at' => now()->subDays(9)]);
        Transaction::create(['wallet_id' => $wallet->id, 'order_id' => $orders[3]->id, 'type' => TransactionType::Payment, 'description' => 'Snap On — ProDental Qatar', 'amount' => -500, 'status' => PaymentStatus::Completed, 'created_at' => now()->subDays(13)]);
        Transaction::create(['wallet_id' => $wallet->id, 'order_id' => $orders[1]->id, 'type' => TransactionType::Payment, 'description' => 'Partial Denture — Gulf Dental Lab', 'amount' => -262.50, 'status' => PaymentStatus::Pending, 'created_at' => now()->subDays(20)]);

        PaymentMethod::create(['user_id' => $doctor->id, 'type' => 'visa', 'label' => 'Visa •••• 4521', 'last_four' => '4521', 'is_default' => true]);
        PaymentMethod::create(['user_id' => $doctor->id, 'type' => 'mastercard', 'label' => 'Mastercard •••• 8834', 'last_four' => '8834']);
        PaymentMethod::create(['user_id' => $doctor->id, 'type' => 'apple_pay', 'label' => 'Apple Pay']);
        PaymentMethod::create(['user_id' => $doctor->id, 'type' => 'google_pay', 'label' => 'Google Pay']);

        $conversation = Conversation::create([
            'doctor_id' => $doctor->id,
            'lab_id' => $labs[0]->id,
            'order_id' => $orders[0]->id,
            'subject' => 'Order #ORD-2847',
            'last_message_at' => now(),
        ]);

        $chatMessages = [
            [false, 'Hello Dr. Ahmed, your order #ORD-2847 has been received and we will begin production immediately.'],
            [true, 'Thank you. Can you confirm shade A2 for the crown?'],
            [false, 'Of course, we will use shade A2 specifically. Any special instructions?'],
            [true, 'Yes, the patient has metal sensitivity — please use 100% pure zirconia.'],
            [false, 'Noted. We will use Zirconia 3Y completely metal-free. Regular updates will follow.'],
        ];

        foreach ($chatMessages as $i => [$fromDoctor, $body]) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $fromDoctor ? $doctor->id : $labs[0]->user_id,
                'body' => $body,
                'created_at' => now()->subHours(5 - $i),
            ]);
        }

        Conversation::create(['doctor_id' => $doctor->id, 'lab_id' => $labs[1]->id, 'subject' => 'Shade confirmation', 'last_message_at' => now()->subHours(2)]);
        Conversation::create(['doctor_id' => $doctor->id, 'lab_id' => $labs[2]->id, 'subject' => 'Order completed', 'last_message_at' => now()->subDay()]);

        $notifications = [
            ['🔬', 'Order #2847 — Quality review started at Doha Lab. Your approval is required.', false],
            ['📦', 'Order #2841 — Shipped from Gulf Region Lab. Expected delivery: Jul 20.', false],
            ['💬', 'Gulf Dental Lab sent a message: "Can you confirm the shade?"', false],
            ['🤖', 'AI: Order #2847 file is missing a lateral scan. Add it for better accuracy.', false],
            ['✅', 'Order #2838 — Completed successfully. Please rate ProDental Qatar.', true],
            ['💳', 'Invoice $340 due for Gulf Dental Lab. Due date: Jul 25.', true],
            ['📋', 'Welcome to DentaLink! Complete your profile to start placing orders.', true],
        ];

        foreach ($notifications as [$icon, $body, $read]) {
            AppNotification::create([
                'user_id' => $doctor->id,
                'title' => 'DentaLink',
                'body' => $body,
                'icon' => $icon,
                'is_read' => $read,
                'read_at' => $read ? now() : null,
            ]);
        }

        ApprovalRequest::create([
            'approvable_type' => Lab::class,
            'approvable_id' => $labs[5]->id,
            'requested_by' => $labs[5]->user_id,
            'status' => ApprovalStatus::Pending,
            'notes' => 'MedLab Jordan — dental lab registration pending review.',
        ]);
        ApprovalRequest::create([
            'approvable_type' => DoctorProfile::class,
            'approvable_id' => $doctor->doctorProfile->id,
            'requested_by' => $doctor->id,
            'status' => ApprovalStatus::Pending,
            'notes' => 'Dr. Mohammed Al-Rashid — dentist account verification.',
        ]);
        ApprovalRequest::create([
            'approvable_type' => Lab::class,
            'approvable_id' => $labs[1]->id,
            'requested_by' => $labs[1]->user_id,
            'status' => ApprovalStatus::Pending,
            'notes' => 'Smile Kings Lab — UAE lab registration.',
        ]);

        CommissionSetting::create(['name' => 'Platform Commission (Standard)', 'slug' => 'standard', 'rate' => 5.00, 'description' => 'Standard order commission']);
        CommissionSetting::create(['name' => 'Express Orders', 'slug' => 'express', 'rate' => 7.00, 'description' => 'Express order commission']);
        CommissionSetting::create(['name' => 'Premium Labs', 'slug' => 'premium', 'rate' => 3.00, 'description' => 'Premium lab commission rate']);
    }
}
