<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RatingResource\Pages;
use App\Models\Rating;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RatingResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.operations');
    }

    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.ratings_reviews.nav');
    }

    

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('lab_id')
                    ->relationship('lab', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('score')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required(),
                Forms\Components\Textarea::make('review')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label(__('dentalink.fields.order_number'))
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label(__('dentalink.fields.doctor'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('lab.name')
                    ->label(__('dentalink.fields.lab'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('score')
                    ->label(__('dentalink.fields.rating'))
                    ->formatStateUsing(fn ($state) => str_repeat('★', (int) $state).str_repeat('☆', 5 - (int) $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('review')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('lab_id')
                    ->relationship('lab', 'name')
                    ->label(__('dentalink.fields.lab')),
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
            'index' => Pages\ListRatings::route('/'),
            'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }
}
