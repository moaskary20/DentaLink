<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Filament\App\Pages\OrderTracking;
use App\Filament\App\Resources\OrderResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('track')
                ->label(__('dentalink.actions.track_order'))
                ->icon('heroicon-o-map')
                ->url(fn () => OrderTracking::getUrl(['order' => $this->record->order_number])),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('dentalink.sections.order_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->label(__('dentalink.fields.order_number_full'))
                            ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state?->label()),
                        Infolists\Components\TextEntry::make('service_name')
                            ->label(__('dentalink.fields.service')),
                        Infolists\Components\TextEntry::make('lab.name')
                            ->label(__('dentalink.fields.laboratory')),
                        Infolists\Components\TextEntry::make('tooth_number')
                            ->label(__('dentalink.fields.tooth_area')),
                        Infolists\Components\TextEntry::make('material'),
                        Infolists\Components\TextEntry::make('shade'),
                        Infolists\Components\TextEntry::make('total')
                            ->money('usd'),
                        Infolists\Components\TextEntry::make('expected_delivery_at')
                            ->label(__('dentalink.fields.expected_delivery_at'))
                            ->date('M j, Y'),
                        Infolists\Components\IconEntry::make('is_express')
                            ->label(__('dentalink.fields.express'))
                            ->boolean(),
                        Infolists\Components\TextEntry::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
