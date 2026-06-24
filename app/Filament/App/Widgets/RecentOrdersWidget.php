<?php

namespace App\Filament\App\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string | Htmlable | null
    {
        return __('dentalink.widgets.recent_orders.heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with('lab')
                    ->when(Auth::id(), fn ($q) => $q->where('doctor_id', Auth::id()))
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label(__('dentalink.fields.order_number'))
                    ->weight('bold')
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : __('dentalink.common.em_dash')),
                Tables\Columns\TextColumn::make('service_name')
                    ->label(__('dentalink.fields.service')),
                Tables\Columns\TextColumn::make('lab.name')
                    ->label(__('dentalink.fields.lab')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? __('dentalink.common.em_dash')),
                Tables\Columns\TextColumn::make('expected_delivery_at')
                    ->label(__('dentalink.fields.delivery'))
                    ->date('M j')
                    ->placeholder(__('dentalink.common.em_dash')),
            ])
            ->paginated(false);
    }
}
