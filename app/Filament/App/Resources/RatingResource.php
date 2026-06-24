<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\RatingResource\Pages;
use App\Models\Rating;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RatingResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.laboratories');
    }

    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('doctor_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->label(__('dentalink.fields.order'))
                    ->relationship('order', 'order_number', fn ($query) => $query->where('doctor_id', Auth::id()))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('lab_id')
                    ->label(__('dentalink.fields.laboratory'))
                    ->relationship('lab', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('score')
                    ->label(__('dentalink.fields.rating'))
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('review')
                    ->label(__('dentalink.actions.review'))
                    ->rows(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lab.name')
                    ->label(__('dentalink.fields.lab'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label(__('dentalink.fields.order'))
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—'),
                Tables\Columns\TextColumn::make('score')
                    ->label(__('dentalink.fields.rating'))
                    ->formatStateUsing(fn ($state) => str_repeat('★', (int) $state) . str_repeat('☆', 5 - (int) $state)),
                Tables\Columns\TextColumn::make('review')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRatings::route('/'),
            'create' => Pages\CreateRating::route('/create'),
            'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }
}
