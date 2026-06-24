<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WalletResource\Pages;
use App\Filament\Admin\Resources\WalletResource\RelationManagers\TransactionsRelationManager;
use App\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WalletResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.finance');
    }

    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    

    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('dentalink.models.wallet');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dentalink.models.wallets');
    }

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.wallets.nav');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('balance')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\TextInput::make('currency')
                    ->default('USD')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('dentalink.fields.user'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label(__('dentalink.fields.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.role')
                    ->label(__('dentalink.fields.role'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),
                Tables\Columns\TextColumn::make('balance')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transactions_count')
                    ->counts('transactions')
                    ->label(__('dentalink.fields.transactions')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('balance', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('dentalink.sections.wallet_overview'))
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')->label(__('dentalink.fields.user')),
                        Infolists\Components\TextEntry::make('user.email')->label(__('dentalink.fields.email')),
                        Infolists\Components\TextEntry::make('balance')->money('usd'),
                        Infolists\Components\TextEntry::make('currency'),
                        Infolists\Components\TextEntry::make('updated_at')->dateTime('M j, Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWallets::route('/'),
            'view' => Pages\ViewWallet::route('/{record}'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }
}
