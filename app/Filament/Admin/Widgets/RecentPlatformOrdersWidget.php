<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentPlatformOrdersWidget extends BaseWidget
{
    public function getHeading(): ?string
    {
        return __('dentalink.widgets.recent_platform_orders.heading');
    }

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->with(['doctor', 'lab'])->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label(__('dentalink.fields.order_number'))
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label(__('dentalink.fields.doctor')),
                Tables\Columns\TextColumn::make('lab.name')
                    ->label(__('dentalink.fields.lab')),
                Tables\Columns\TextColumn::make('service_name')
                    ->label(__('dentalink.fields.service')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\TextColumn::make('total')
                    ->money('usd'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('dentalink.actions.view'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Order $record) => OrderResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
