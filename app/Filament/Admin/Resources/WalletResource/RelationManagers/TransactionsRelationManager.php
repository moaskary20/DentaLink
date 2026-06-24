<?php

namespace App\Filament\Admin\Resources\WalletResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')->limit(40),
                Tables\Columns\TextColumn::make('type')->badge()->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\TextColumn::make('amount')->money('usd'),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
