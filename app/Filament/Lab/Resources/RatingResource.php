<?php

namespace App\Filament\Lab\Resources;

use App\Enums\RatingDirection;
use App\Filament\Lab\Resources\RatingResource\Pages;
use App\Models\Rating;
use App\Support\CurrentLab;
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
        return __('dentalink.nav.groups.communication');
    }

    protected static ?string $model = Rating::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.reviews.nav');
    }

    

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $labId = CurrentLab::id();

        return parent::getEloquentQuery()
            ->when($labId, fn ($q) => $q->where('lab_id', $labId), fn ($q) => $q->whereRaw('1 = 0'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number', fn ($q) => $q->where('lab_id', CurrentLab::id()))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Hidden::make('lab_id')->default(fn () => CurrentLab::id()),
                Forms\Components\Hidden::make('direction')->default(RatingDirection::LabToDoctor->value),
                Forms\Components\TextInput::make('score')->numeric()->minValue(1)->maxValue(5)->required(),
                Forms\Components\Textarea::make('review')->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label(__('dentalink.fields.order_number'))
                    ->formatStateUsing(fn (?string $state) => $state ? "#{$state}" : '—'),
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label(__('dentalink.fields.doctor')),
                Tables\Columns\TextColumn::make('score')
                    ->label(__('dentalink.fields.rating'))
                    ->formatStateUsing(fn ($state) => str_repeat('★', (int) $state).str_repeat('☆', 5 - (int) $state)),
                Tables\Columns\TextColumn::make('review')
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('M j, Y'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data) => array_merge($data, [
                        'lab_id' => CurrentLab::id(),
                        'direction' => RatingDirection::LabToDoctor->value,
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRatings::route('/'),
            'create' => Pages\CreateRating::route('/create'),
        ];
    }
}
