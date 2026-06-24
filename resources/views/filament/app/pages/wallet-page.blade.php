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
            <button type="button" wire:click="openAddCardModal" class="dentalink-btn dentalink-btn-outline" style="font-size:11px;padding:5px 12px;">@lang('dentalink.blades.wallet.add_card')</button>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
            @forelse ($this->getPaymentMethods() as $method)
                <div style="border:1.5px solid {{ $method->is_default ? 'var(--primary)' : 'var(--border)' }};border-radius:var(--radius-sm);padding:14px;text-align:center;">
                    <div style="font-size:22px;">💳</div>
                    <div style="font-size:12px;font-weight:700;margin-top:6px;">
                        {{ $method->label }}
                        @if ($method->last_four)
                            •••• {{ $method->last_four }}
                        @endif
                    </div>
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

    @if ($showAddCardModal)
        <div class="dentalink-modal-backdrop" wire:click.self="closeAddCardModal">
            <div class="dentalink-modal" role="dialog" aria-modal="true" aria-labelledby="add-card-title">
                <div class="dentalink-modal-header">
                    <h3 id="add-card-title" class="dentalink-modal-title">@lang('dentalink.blades.wallet.card_form.title')</h3>
                    <button type="button" wire:click="closeAddCardModal" class="dentalink-modal-close" aria-label="@lang('dentalink.actions.cancel')">&times;</button>
                </div>

                <form wire:submit="saveCard" class="dentalink-modal-body">
                    <div class="form-group">
                        <label class="form-label" for="card-type">@lang('dentalink.blades.wallet.card_form.type')</label>
                        <select wire:model="cardForm.type" id="card-type" class="form-control filter-select">
                            <option value="visa">Visa</option>
                            <option value="mastercard">Mastercard</option>
                        </select>
                        @error('cardForm.type') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="card-holder">@lang('dentalink.blades.wallet.card_form.holder')</label>
                        <input wire:model="cardForm.holder" id="card-holder" type="text" class="form-control" placeholder="{{ __('dentalink.blades.wallet.card_form.holder_placeholder') }}">
                        @error('cardForm.holder') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="card-number">@lang('dentalink.blades.wallet.card_form.number')</label>
                        <input wire:model="cardForm.number" id="card-number" type="text" inputmode="numeric" maxlength="23" class="form-control" placeholder="1234 5678 9012 3456" autocomplete="cc-number">
                        @error('cardForm.number') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="card-expiry-month">@lang('dentalink.blades.wallet.card_form.expiry_month')</label>
                            <input wire:model="cardForm.expiry_month" id="card-expiry-month" type="text" inputmode="numeric" maxlength="2" class="form-control" placeholder="MM">
                            @error('cardForm.expiry_month') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label class="form-label" for="card-expiry-year">@lang('dentalink.blades.wallet.card_form.expiry_year')</label>
                            <input wire:model="cardForm.expiry_year" id="card-expiry-year" type="text" inputmode="numeric" maxlength="2" class="form-control" placeholder="YY">
                            @error('cardForm.expiry_year') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <label style="display:flex;align-items:center;gap:8px;margin-top:16px;font-size:13px;cursor:pointer;">
                        <input wire:model="cardForm.is_default" type="checkbox" style="width:16px;height:16px;">
                        @lang('dentalink.blades.wallet.card_form.set_default')
                    </label>

                    <div class="dentalink-modal-footer">
                        <button type="button" wire:click="closeAddCardModal" class="dentalink-btn dentalink-btn-outline">@lang('dentalink.actions.cancel')</button>
                        <button type="submit" class="dentalink-btn dentalink-btn-primary" wire:loading.attr="disabled" wire:target="saveCard">
                            <span wire:loading.remove wire:target="saveCard">@lang('dentalink.blades.wallet.card_form.save')</span>
                            <span wire:loading wire:target="saveCard">@lang('dentalink.blades.wallet.card_form.saving')</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-filament-panels::page>
