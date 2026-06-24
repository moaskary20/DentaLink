<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ConversationResource\Pages;
use App\Filament\Admin\Resources\ConversationResource\RelationManagers\MessagesRelationManager;
use App\Models\Conversation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConversationResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.communication');
    }

    protected static ?string $model = Conversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.chat_conversations.nav');
    }

    

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('lab_id')
                    ->relationship('lab', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->searchable(),
                Forms\Components\TextInput::make('subject'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label(__('dentalink.fields.doctor'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('lab.name')
                    ->label(__('dentalink.fields.lab'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label(__('dentalink.fields.order_number'))
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—'),
                Tables\Columns\TextColumn::make('subject')
                    ->limit(30),
                Tables\Columns\TextColumn::make('messages_count')
                    ->counts('messages')
                    ->label(__('dentalink.fields.messages')),
                Tables\Columns\TextColumn::make('last_message_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('last_message_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('dentalink.sections.conversation'))
                    ->schema([
                        Infolists\Components\TextEntry::make('doctor.name')->label(__('dentalink.fields.doctor')),
                        Infolists\Components\TextEntry::make('lab.name')->label(__('dentalink.fields.lab')),
                        Infolists\Components\TextEntry::make('order.order_number')->label(__('dentalink.fields.order_number')),
                        Infolists\Components\TextEntry::make('subject'),
                        Infolists\Components\TextEntry::make('last_message_at')->dateTime('M j, Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConversations::route('/'),
            'view' => Pages\ViewConversation::route('/{record}'),
        ];
    }
}
