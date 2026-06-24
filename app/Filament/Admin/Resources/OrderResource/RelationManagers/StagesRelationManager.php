<?php

namespace App\Filament\Admin\Resources\OrderResource\RelationManagers;

use App\Enums\OrderStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables;
use Filament\Tables\Table;

class StagesRelationManager extends RelationManager
{

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('dentalink.relation_managers.order_stages');
    }
    protected static string $relationship = 'stages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                    ->required(),
                Forms\Components\TextInput::make('label')
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->required(),
                Forms\Components\DateTimePicker::make('completed_at'),
                Forms\Components\DateTimePicker::make('expected_at'),
                Forms\Components\Toggle::make('is_current'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('dentalink.fields.number_sign'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('label'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\IconColumn::make('is_current')
                    ->boolean()
                    ->label(__('dentalink.fields.current')),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime('M j, Y H:i'),
                Tables\Columns\TextColumn::make('expected_at')
                    ->dateTime('M j, Y'),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
