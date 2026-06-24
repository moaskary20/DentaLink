<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\TransactionResource;
use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactionsWidget extends BaseWidget
{
    public function getHeading(): ?string
    {
        return __('dentalink.widgets.recent_transactions.heading');
    }

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query()->with(['wallet.user', 'order'])->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('wallet.user.name')
                    ->label(__('dentalink.fields.user')),
                Tables\Columns\TextColumn::make('description')
                    ->limit(40),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\TextColumn::make('amount')
                    ->money('usd')
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('M j, Y'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('dentalink.actions.view'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Transaction $record) => TransactionResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
