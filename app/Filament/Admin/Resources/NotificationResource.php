<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NotificationResource\Pages;
use App\Models\AppNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.communication');
    }

    protected static ?string $model = AppNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.notifications.nav');
    }

    

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('icon'),
                Forms\Components\TextInput::make('type'),
                Forms\Components\Toggle::make('is_read')
                    ->label(__('dentalink.fields.read')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('dentalink.fields.user'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->label(__('dentalink.common.em_dash')),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('body')
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_read')
                    ->boolean()
                    ->label(__('dentalink.fields.read')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label(__('dentalink.filters.read_status')),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label(__('dentalink.fields.user')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
