<?php

namespace App\Http\Controllers;

use App\Filament\App\Pages\CreateOrder;
use App\Filament\App\Pages\OrderTracking;
use App\Models\PaymentCheckout;
use App\Services\PaymentGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentCallbackController extends Controller
{
    public function stripeSuccess(Request $request, string $checkout, PaymentGatewayService $payments): RedirectResponse
    {
        $record = $this->findOwnedCheckout($checkout);

        if ($sessionId = $request->query('session_id')) {
            $record->update(['gateway_session_id' => $sessionId]);
        }

        try {
            $order = $payments->completeCheckout($record);

            return redirect()
                ->to(OrderTracking::getUrl(['order' => $order->order_number], panel: 'app'))
                ->with('success', __('dentalink.notifications.order_created'));
        } catch (\Throwable $e) {
            return redirect()
                ->to(CreateOrder::getUrl(panel: 'app'))
                ->with('error', $e->getMessage());
        }
    }

    public function paypalSuccess(Request $request, string $checkout, PaymentGatewayService $payments): RedirectResponse
    {
        $record = $this->findOwnedCheckout($checkout);
        $token = $request->query('token') ?: $record->gateway_session_id;

        try {
            $order = $payments->completeCheckout($record, $token);

            return redirect()
                ->to(OrderTracking::getUrl(['order' => $order->order_number], panel: 'app'))
                ->with('success', __('dentalink.notifications.order_created'));
        } catch (\Throwable $e) {
            return redirect()
                ->to(CreateOrder::getUrl(panel: 'app'))
                ->with('error', $e->getMessage());
        }
    }

    public function cancel(string $checkout): RedirectResponse
    {
        $record = $this->findOwnedCheckout($checkout);
        $record->markFailed('Cancelled by user');

        return redirect()
            ->to(CreateOrder::getUrl(panel: 'app'))
            ->with('error', __('dentalink.notifications.payment_cancelled'));
    }

    protected function findOwnedCheckout(string $uuid): PaymentCheckout
    {
        return PaymentCheckout::query()
            ->where('uuid', $uuid)
            ->where('doctor_id', Auth::id())
            ->firstOrFail();
    }
}
