<?php

namespace App\Filament\Lab\Resources\ConversationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('body')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sender.name')
                    ->label(__('dentalink.fields.from')),
                Tables\Columns\TextColumn::make('body')
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y H:i'),
            ])
            ->defaultSort('created_at', 'asc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('dentalink.actions.send_message'))
                    ->mutateFormDataUsing(function (array $data) {
                        $data['sender_id'] = Auth::id();

                        $this->getOwnerRecord()->update(['last_message_at' => now()]);

                        return $data;
                    }),
            ]);
    }
}
