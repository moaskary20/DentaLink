<?php

namespace App\Filament\App\Pages;

use App\Models\Conversation;
use App\Models\Message;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class ChatPage extends Page
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.communication');
    }

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.chat.nav');
    }
    public function getTitle(): string
    {
        return __('dentalink.pages.chat.title');
    }

    protected static string $view = 'filament.app.pages.chat-page';

    

    protected static ?int $navigationSort = 1;

    #[Url]
    public ?int $conversationId = null;

    public string $messageBody = '';

    public function getConversations()
    {
        return Conversation::query()
            ->with(['lab', 'order'])
            ->where('doctor_id', Auth::id())
            ->orderByDesc('last_message_at')
            ->get();
    }

    public function getActiveConversation(): ?Conversation
    {
        if ($this->conversationId) {
            return Conversation::query()
                ->with(['lab', 'messages.sender'])
                ->where('doctor_id', Auth::id())
                ->find($this->conversationId);
        }

        return $this->getConversations()->first();
    }

    public function selectConversation(int $id): void
    {
        $this->conversationId = $id;
    }

    public function sendMessage(): void
    {
        $conversation = $this->getActiveConversation();

        if (! $conversation || blank($this->messageBody)) {
            return;
        }

        Message::query()->create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $this->messageBody,
        ]);

        $conversation->update(['last_message_at' => now()]);
        $this->messageBody = '';
    }
}
