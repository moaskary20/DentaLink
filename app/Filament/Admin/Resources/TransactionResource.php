<?php

namespace App\Filament\Admin\Resources;

use App\Enums\PaymentStatus;
use App\Enums\TransactionType;
use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.finance');
    }

    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.transactions.nav');
    }

    

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('wallet_id')
                    ->relationship('wallet', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name.' — $'.number_format($record->balance, 2))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->searchable(),
                Forms\Components\Select::make('type')
                    ->options(collect(TransactionType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                    ->required(),
                Forms\Components\TextInput::make('reference'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wallet.user.name')
                    ->label(__('dentalink.fields.user'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label(__('dentalink.fields.order_number'))
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—'),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (?TransactionType $state) => $state?->label()),
                Tables\Columns\TextColumn::make('amount')
                    ->money('usd')
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?PaymentStatus $state) => $state?->label())
                    ->color(fn (?PaymentStatus $state) => match ($state) {
                        PaymentStatus::Completed => 'success',
                        PaymentStatus::Pending => 'warning',
                        PaymentStatus::Failed => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(collect(TransactionType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('dentalink.sections.transaction_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('wallet.user.name')->label(__('dentalink.fields.user')),
                        Infolists\Components\TextEntry::make('type')->badge()->formatStateUsing(fn ($state) => $state?->label()),
                        Infolists\Components\TextEntry::make('amount')->money('usd'),
                        Infolists\Components\TextEntry::make('status')->badge()->formatStateUsing(fn ($state) => $state?->label()),
                        Infolists\Components\TextEntry::make('order.order_number')->label(__('dentalink.fields.order_number')),
                        Infolists\Components\TextEntry::make('description')->columnSpanFull(),
                        Infolists\Components\TextEntry::make('reference'),
                        Infolists\Components\TextEntry::make('created_at')->dateTime('M j, Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
