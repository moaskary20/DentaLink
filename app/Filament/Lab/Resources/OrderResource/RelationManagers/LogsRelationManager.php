<?php

namespace App\Filament\Lab\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class LogsRelationManager extends RelationManager
{

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('dentalink.relation_managers.activity_log');
    }
    protected static string $relationship = 'logs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('icon')->default('📋'),
                Forms\Components\Textarea::make('message')->required()->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')->label(__('dentalink.common.em_dash')),
                Tables\Columns\TextColumn::make('message')->wrap(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y H:i'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data) => array_merge($data, ['user_id' => Auth::id()])),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
