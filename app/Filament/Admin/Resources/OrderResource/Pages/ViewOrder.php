<?php

namespace App\Filament\Admin\Resources\OrderResource\Pages;

use App\Filament\Admin\Resources\OrderResource;
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
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('dentalink.sections.order_overview'))
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—'),
                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state?->label()),
                        Infolists\Components\IconEntry::make('is_express')
                            ->label(__('dentalink.fields.express_order'))
                            ->boolean(),
                        Infolists\Components\TextEntry::make('doctor.name')
                            ->label(__('dentalink.fields.doctor')),
                        Infolists\Components\TextEntry::make('lab.name')
                            ->label(__('dentalink.fields.lab')),
                        Infolists\Components\TextEntry::make('service_name')
                            ->label(__('dentalink.fields.service')),
                        Infolists\Components\TextEntry::make('tooth_number')
                            ->label(__('dentalink.fields.tooth_area_short')),
                        Infolists\Components\TextEntry::make('material'),
                        Infolists\Components\TextEntry::make('shade')
                            ->label(__('dentalink.fields.shade')),
                        Infolists\Components\TextEntry::make('turnaround_days')
                            ->suffix(' days'),
                        Infolists\Components\TextEntry::make('expected_delivery_at')
                            ->date('M j, Y'),
                        Infolists\Components\TextEntry::make('delivered_at')
                            ->date('M j, Y')
                            ->placeholder(__('dentalink.common.not_yet')),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make(__('dentalink.sections.financial'))
                    ->schema([
                        Infolists\Components\TextEntry::make('cost')
                            ->money('usd')
                            ->label(__('dentalink.fields.service_cost')),
                        Infolists\Components\TextEntry::make('commission')
                            ->money('usd')
                            ->label(__('dentalink.fields.platform_commission')),
                        Infolists\Components\TextEntry::make('total')
                            ->money('usd')
                            ->label(__('dentalink.fields.total_paid')),
                    ])
                    ->columns(3),
                Infolists\Components\Section::make(__('dentalink.sections.notes'))
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->placeholder(__('dentalink.common.no_notes'))
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
                Infolists\Components\Section::make(__('dentalink.sections.timestamps'))
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->dateTime('M j, Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->dateTime('M j, Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }
}
