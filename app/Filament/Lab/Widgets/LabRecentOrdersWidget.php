<?php

namespace App\Filament\Lab\Widgets;

use App\Filament\Lab\Resources\OrderResource;
use App\Models\Order;
use App\Support\CurrentLab;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LabRecentOrdersWidget extends BaseWidget
{
    public function getHeading(): ?string
    {
        return __('dentalink.widgets.recent_orders.heading');
    }

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $labId = CurrentLab::id();

        return $table
            ->query(
                Order::query()
                    ->with(['doctor'])
                    ->when($labId, fn ($q) => $q->where('lab_id', $labId))
                    ->when(! $labId, fn ($q) => $q->whereRaw('1 = 0'))
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label(__('dentalink.fields.order_number'))
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—'),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label(__('dentalink.fields.doctor')),
                Tables\Columns\TextColumn::make('service_name')
                    ->label(__('dentalink.fields.service')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\TextColumn::make('expected_delivery_at')
                    ->label(__('dentalink.fields.delivery'))
                    ->date('M j, Y'),
                Tables\Columns\TextColumn::make('cost')
                    ->money('usd'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('dentalink.actions.manage'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Order $record) => OrderResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('dentalink.widgets.empty.no_orders'))
            ->emptyStateDescription(__('dentalink.widgets.empty.no_orders_desc'));
    }
}
