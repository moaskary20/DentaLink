<x-filament-panels::page class="dentalink-page">
    <div class="section-header">
        <div>
            <div class="section-title">@lang('dentalink.blades.wallet.title')</div>
        </div>
    </div>

    <div class="grid-2" style="margin-bottom:16px;">
        <div class="wallet-card">
            <div class="wallet-label">@lang('dentalink.blades.wallet.balance_label')</div>
            <div class="wallet-balance">${{ number_format($this->getBalance(), 2) }}</div>
            <div class="wallet-label">@lang('dentalink.blades.wallet.last_updated', ['datetime' => now()->format('M j, g:i A')])</div>
            <div class="wallet-actions">
                <button type="button" wire:click="deposit" class="wallet-btn wallet-btn-white">@lang('dentalink.blades.wallet.deposit')</button>
                <button type="button" wire:click="withdraw" class="wallet-btn wallet-btn-outline">@lang('dentalink.blades.wallet.withdraw')</button>
                <button type="button" wire:click="addCard('apple_pay')" class="wallet-btn wallet-btn-outline">@lang('dentalink.blades.wallet.apple_pay')</button>
            </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);">@lang('dentalink.blades.wallet.total_paid')</div>
                        <div style="font-size:22px;font-weight:800;">${{ number_format($this->getTotalPaid(), 0) }}</div>
                    </div>
                    <div style="font-size:28px;">💳</div>
                </div>
            </div>
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <div style="font-size:12px;color:var(--text-muted);">@lang('dentalink.blades.wallet.pending_invoices')</div>
                        <div style="font-size:22px;font-weight:800;color:var(--danger);">${{ number_format($this->getPendingAmount(), 0) }}</div>
                    </div>
                    <div style="font-size:28px;">⏳</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">
            @lang('dentalink.blades.wallet.payment_methods')
            <button type="button" wire:click="addCard('visa')" class="dentalink-btn dentalink-btn-outline" style="font-size:11px;padding:5px 12px;">@lang('dentalink.blades.wallet.add_card')</button>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
            @forelse ($this->getPaymentMethods() as $method)
                <div style="border:1.5px solid {{ $method->is_default ? 'var(--primary)' : 'var(--border)' }};border-radius:var(--radius-sm);padding:14px;text-align:center;">
                    <div style="font-size:22px;">💳</div>
                    <div style="font-size:12px;font-weight:700;margin-top:6px;">{{ $method->label }} •••• {{ $method->last_four }}</div>
                    @if ($method->is_default)
                        <div style="font-size:10px;color:var(--primary);">@lang('dentalink.blades.wallet.default')</div>
                    @endif
                </div>
            @empty
                <div style="border:1.5px solid var(--primary);border-radius:var(--radius-sm);padding:14px;text-align:center;">
                    <div style="font-size:22px;">💳</div>
                    <div style="font-size:12px;font-weight:700;margin-top:6px;">Visa •••• 4521</div>
                    <div style="font-size:10px;color:var(--primary);">@lang('dentalink.blades.wallet.default')</div>
                </div>
                <div style="border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:14px;text-align:center;">
                    <div style="font-size:22px;">🏧</div>
                    <div style="font-size:12px;font-weight:700;margin-top:6px;">Mastercard •••• 8834</div>
                </div>
            @endforelse
        </div>

        <div class="card-title">@lang('dentalink.blades.wallet.transaction_history')</div>
        <div class="table-wrap">
            <table class="dentalink-table">
                <thead>
                    <tr>
                        <th>@lang('dentalink.fields.date')</th>
                        <th>@lang('dentalink.fields.description')</th>
                        <th>@lang('dentalink.fields.amount')</th>
                        <th>@lang('dentalink.fields.status')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->getTransactions() as $transaction)
                        <tr>
                            <td style="font-size:12px;color:var(--text-muted);">{{ $transaction->created_at?->format('M j') }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td style="font-weight:700;color:{{ $transaction->amount >= 0 ? 'var(--success)' : 'var(--danger)' }};">
                                {{ $transaction->amount >= 0 ? '+' : '' }}${{ number_format(abs($transaction->amount), 0) }}
                            </td>
                            <td><span class="badge {{ $transaction->status?->badgeClass() ?? 'badge-green' }}">{{ $transaction->status?->label() ?? __('dentalink.enums.payment_status.completed') }}</span></td>
                        </tr>
                    @empty
                        <tr><td style="font-size:12px;color:var(--text-muted);">Jul 17</td><td>@lang('dentalink.blades.wallet.fallback_tx1')</td><td style="font-weight:700;color:var(--danger);">-$294</td><td><span class="badge badge-green">@lang('dentalink.enums.payment_status.completed')</span></td></tr>
                        <tr><td style="font-size:12px;color:var(--text-muted);">Jul 12</td><td>@lang('dentalink.blades.wallet.fallback_tx2')</td><td style="font-weight:700;color:var(--success);">+$500</td><td><span class="badge badge-green">@lang('dentalink.enums.payment_status.completed')</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
