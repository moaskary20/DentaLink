<x-filament-panels::page class="dentalink-page">
    <div class="section-header">
        <div>
            <div class="section-title">@lang('dentalink.blades.chat.title')</div>
            <div class="section-sub">@lang('dentalink.blades.chat.subtitle')</div>
        </div>
    </div>

    <div class="card">
        <div class="chat-layout">
            <div class="chat-sidebar">
                @forelse ($this->getConversations() as $conversation)
                    <div
                        wire:click="selectConversation({{ $conversation->id }})"
                        class="chat-thread {{ ($this->getActiveConversation()?->id === $conversation->id) ? 'active' : '' }}"
                    >
                        <div class="avatar" style="background:var(--primary);">{{ strtoupper(substr($conversation->lab?->name ?? 'L', 0, 2)) }}</div>
                        <div>
                            <div style="font-size:13px;font-weight:700;">{{ $conversation->lab?->name ?? __('dentalink.fields.lab') }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $conversation->subject ?? __('dentalink.blades.chat.order_subject', ['number' => $conversation->order?->order_number ?? '—']) }}</div>
                        </div>
                    </div>
                @empty
                    <div class="chat-thread active">
                        <div class="avatar" style="background:var(--primary);">DL</div>
                        <div>
                            <div style="font-size:13px;font-weight:700;">@lang('dentalink.blades.order_tracking.fallback_lab')</div>
                            <div style="font-size:11px;color:var(--text-muted);">@lang('dentalink.blades.chat.order_subject', ['number' => 'ORD-2847'])</div>
                        </div>
                    </div>
                @endforelse
            </div>

            <div>
                @php $active = $this->getActiveConversation(); @endphp
                <div style="padding:12px 16px;border-bottom:1px solid var(--border);font-weight:700;">
                    {{ $active?->lab?->name ?? __('dentalink.blades.order_tracking.fallback_lab') }} — {{ $active?->subject ?? __('dentalink.blades.chat.order_subject', ['number' => 'ORD-2847']) }}
                </div>
                <div class="chat-messages">
                    @if ($active?->messages->isNotEmpty())
                        @foreach ($active->messages as $message)
                            <div class="chat-bubble {{ $message->sender_id === auth()->id() ? 'sent' : 'received' }}">
                                {{ $message->body }}
                            </div>
                        @endforeach
                    @else
                        <div class="chat-bubble received">@lang('dentalink.blades.chat.fallback_msg1')</div>
                        <div class="chat-bubble sent">@lang('dentalink.blades.chat.fallback_msg2')</div>
                        <div class="chat-bubble received">@lang('dentalink.blades.chat.fallback_msg3')</div>
                    @endif
                </div>
                <form wire:submit="sendMessage" style="padding:12px 16px;border-top:1px solid var(--border);display:flex;gap:8px;">
                    <input wire:model="messageBody" type="text" class="form-control" placeholder="{{ __('dentalink.blades.chat.input_placeholder') }}">
                    <button type="submit" class="dentalink-btn dentalink-btn-primary">@lang('dentalink.actions.send')</button>
                </form>
            </div>
        </div>
    </div>
</x-filament-panels::page>
