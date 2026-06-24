<?php

namespace App\Filament\Admin\Resources;

use App\Enums\ApprovalStatus;
use App\Filament\Admin\Resources\ApprovalRequestResource\Pages;
use App\Models\ApprovalRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ApprovalRequestResource extends Resource
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.approvals');
    }

    protected static ?string $model = ApprovalRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('dentalink.models.approval_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('dentalink.models.approval_requests');
    }

    public static function getNavigationLabel(): string
    {
        return __('dentalink.resources.approval_requests.nav');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options(collect(ApprovalStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()]))
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('approvable_type')
                    ->label(__('dentalink.fields.type'))
                    ->formatStateUsing(fn ($state) => class_basename($state)),
                Tables\Columns\TextColumn::make('requester.name')
                    ->label(__('dentalink.fields.requested_by')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?ApprovalStatus $state) => $state?->label())
                    ->color(fn (?ApprovalStatus $state) => match ($state) {
                        ApprovalStatus::Approved => 'success',
                        ApprovalStatus::Pending => 'warning',
                        ApprovalStatus::Rejected => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(ApprovalStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(__('dentalink.actions.approve'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (ApprovalRequest $record) => $record->status === ApprovalStatus::Pending)
                    ->action(function (ApprovalRequest $record) {
                        app(\App\Services\ApprovalService::class)->approve($record, Auth::id());
                        Notification::make()->title(__('dentalink.notifications.request_approved'))->success()->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label(__('dentalink.actions.reject'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (ApprovalRequest $record) => $record->status === ApprovalStatus::Pending)
                    ->requiresConfirmation()
                    ->action(function (ApprovalRequest $record) {
                        app(\App\Services\ApprovalService::class)->reject($record, Auth::id());
                        Notification::make()->title(__('dentalink.notifications.request_rejected'))->warning()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovalRequests::route('/'),
            'edit' => Pages\EditApprovalRequest::route('/{record}/edit'),
        ];
    }
}
