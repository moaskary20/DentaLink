<?php

namespace App\Services;

use App\Enums\PaymentGatewayProvider;
use App\Enums\PaymentStatus;
use App\Models\PaymentCheckout;
use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PaymentGatewayService
{
    /**
     * @return list<PaymentGatewayProvider>
     */
    public function availableMethods(): array
    {
        $methods = [PaymentGatewayProvider::Wallet];

        foreach ([PaymentGatewayProvider::Stripe, PaymentGatewayProvider::Paypal] as $provider) {
            if (PaymentGateway::for($provider)->isReady()) {
                $methods[] = $provider;
            }
        }

        return $methods;
    }

    /**
     * @return list<array{value: string, label: string, icon: string}>
     */
    public function availableMethodOptions(): array
    {
        return collect($this->availableMethods())
            ->map(fn (PaymentGatewayProvider $provider) => [
                'value' => $provider->value,
                'label' => $provider->label(),
                'icon' => $provider->icon(),
            ])
            ->values()
            ->all();
    }

    public function initiateCheckout(User $doctor, array $orderData, PaymentGatewayProvider $gateway, float $amount): string
    {
        if ($gateway === PaymentGatewayProvider::Wallet) {
            throw new RuntimeException('Wallet payments do not use checkout sessions.');
        }

        $settings = PaymentGateway::for($gateway);

        if (! $settings->isReady()) {
            throw new RuntimeException(__('dentalink.notifications.payment_gateway_disabled'));
        }

        $checkout = PaymentCheckout::query()->create([
            'doctor_id' => $doctor->id,
            'gateway' => $gateway,
            'amount' => $amount,
            'currency' => 'USD',
            'status' => PaymentStatus::Pending,
            'order_payload' => $orderData,
        ]);

        return match ($gateway) {
            PaymentGatewayProvider::Stripe => $this->createStripeSession($checkout, $settings),
            PaymentGatewayProvider::Paypal => $this->createPaypalOrder($checkout, $settings),
            default => throw new RuntimeException('Unsupported payment gateway.'),
        };
    }

    public function completeCheckout(PaymentCheckout $checkout, ?string $gatewayToken = null): \App\Models\Order
    {
        if ($checkout->status === PaymentStatus::Completed && $checkout->order_id) {
            return $checkout->order()->firstOrFail();
        }

        if (! $checkout->isPending()) {
            throw new RuntimeException(__('dentalink.notifications.payment_already_processed'));
        }

        $settings = PaymentGateway::for($checkout->gateway);

        match ($checkout->gateway) {
            PaymentGatewayProvider::Stripe => $this->verifyStripeSession($checkout, $settings),
            PaymentGatewayProvider::Paypal => $this->capturePaypalOrder($checkout, $settings, $gatewayToken),
            default => throw new RuntimeException('Unsupported payment gateway.'),
        };

        $order = app(OrderWorkflowService::class)->createOrder(
            $checkout->doctor,
            $checkout->order_payload,
            $checkout->gateway,
            $checkout->gateway_payment_id ?? $checkout->gateway_session_id,
        );

        $checkout->markCompleted($checkout->gateway_payment_id);
        $checkout->update(['order_id' => $order->id]);

        return $order;
    }

    protected function createStripeSession(PaymentCheckout $checkout, PaymentGateway $settings): string
    {
        $successUrl = route('payments.stripe.success', ['checkout' => $checkout->uuid]).'?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('payments.cancel', ['checkout' => $checkout->uuid]);

        $response = Http::asForm()
            ->withBasicAuth($settings->secret_key, '')
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'client_reference_id' => $checkout->uuid,
                'line_items[0][price_data][currency]' => strtolower($checkout->currency),
                'line_items[0][price_data][product_data][name]' => 'DentaLink Order',
                'line_items[0][price_data][unit_amount]' => (int) round(((float) $checkout->amount) * 100),
                'line_items[0][quantity]' => 1,
                'metadata[checkout_uuid]' => $checkout->uuid,
            ]);

        if (! $response->successful()) {
            Log::error('Stripe checkout session failed', ['body' => $response->json()]);
            $checkout->markFailed($response->json('error.message') ?? 'Stripe session creation failed');

            throw new RuntimeException(__('dentalink.notifications.payment_init_failed'));
        }

        $checkout->update(['gateway_session_id' => $response->json('id')]);

        return (string) $response->json('url');
    }

    protected function verifyStripeSession(PaymentCheckout $checkout, PaymentGateway $settings): void
    {
        $sessionId = $checkout->gateway_session_id;

        if (! $sessionId) {
            throw new RuntimeException(__('dentalink.notifications.payment_verification_failed'));
        }

        $response = Http::withBasicAuth($settings->secret_key, '')
            ->get("https://api.stripe.com/v1/checkout/sessions/{$sessionId}");

        if (! $response->successful()) {
            $checkout->markFailed('Unable to verify Stripe session');

            throw new RuntimeException(__('dentalink.notifications.payment_verification_failed'));
        }

        $paymentStatus = $response->json('payment_status');
        $status = $response->json('status');

        if ($paymentStatus !== 'paid' && $status !== 'complete') {
            $checkout->markFailed('Stripe payment not completed');

            throw new RuntimeException(__('dentalink.notifications.payment_not_completed'));
        }

        $checkout->update([
            'gateway_payment_id' => $response->json('payment_intent') ?? $sessionId,
        ]);
    }

    protected function createPaypalOrder(PaymentCheckout $checkout, PaymentGateway $settings): string
    {
        $accessToken = $this->paypalAccessToken($settings);
        $baseUrl = $this->paypalBaseUrl($settings);

        $response = Http::withToken($accessToken)
            ->post("{$baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $checkout->uuid,
                    'description' => 'DentaLink Order',
                    'amount' => [
                        'currency_code' => $checkout->currency,
                        'value' => number_format((float) $checkout->amount, 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'brand_name' => config('app.name', 'DentaLink'),
                    'landing_page' => 'LOGIN',
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('payments.paypal.success', ['checkout' => $checkout->uuid]),
                    'cancel_url' => route('payments.cancel', ['checkout' => $checkout->uuid]),
                ],
            ]);

        if (! $response->successful()) {
            Log::error('PayPal order creation failed', ['body' => $response->json()]);
            $checkout->markFailed('PayPal order creation failed');

            throw new RuntimeException(__('dentalink.notifications.payment_init_failed'));
        }

        $orderId = $response->json('id');
        $checkout->update(['gateway_session_id' => $orderId]);

        $approveLink = collect($response->json('links', []))
            ->firstWhere('rel', 'approve');

        if (! $approveLink || empty($approveLink['href'])) {
            $checkout->markFailed('PayPal approve URL missing');

            throw new RuntimeException(__('dentalink.notifications.payment_init_failed'));
        }

        return (string) $approveLink['href'];
    }

    protected function capturePaypalOrder(PaymentCheckout $checkout, PaymentGateway $settings, ?string $token): void
    {
        $orderId = $token ?: $checkout->gateway_session_id;

        if (! $orderId) {
            throw new RuntimeException(__('dentalink.notifications.payment_verification_failed'));
        }

        $accessToken = $this->paypalAccessToken($settings);
        $baseUrl = $this->paypalBaseUrl($settings);

        $response = Http::withToken($accessToken)
            ->withBody('{}', 'application/json')
            ->post("{$baseUrl}/v2/checkout/orders/{$orderId}/capture");

        if (! $response->successful()) {
            Log::error('PayPal capture failed', ['body' => $response->json()]);
            $checkout->markFailed('PayPal capture failed');

            throw new RuntimeException(__('dentalink.notifications.payment_verification_failed'));
        }

        $status = $response->json('status');

        if (! in_array($status, ['COMPLETED', 'APPROVED'], true)) {
            $checkout->markFailed("PayPal status: {$status}");

            throw new RuntimeException(__('dentalink.notifications.payment_not_completed'));
        }

        $captureId = data_get($response->json(), 'purchase_units.0.payments.captures.0.id', $orderId);

        $checkout->update([
            'gateway_session_id' => $orderId,
            'gateway_payment_id' => $captureId,
        ]);
    }

    protected function paypalAccessToken(PaymentGateway $settings): string
    {
        $response = Http::asForm()
            ->withBasicAuth($settings->public_key, $settings->secret_key)
            ->post($this->paypalBaseUrl($settings).'/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if (! $response->successful() || ! $response->json('access_token')) {
            throw new RuntimeException(__('dentalink.notifications.payment_gateway_auth_failed'));
        }

        return (string) $response->json('access_token');
    }

    protected function paypalBaseUrl(PaymentGateway $settings): string
    {
        return $settings->is_sandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }
}
