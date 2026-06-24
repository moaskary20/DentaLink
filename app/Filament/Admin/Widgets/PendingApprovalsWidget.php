<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\ApprovalStatus;
use App\Filament\Admin\Resources\ApprovalRequestResource;
use App\Models\ApprovalRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingApprovalsWidget extends BaseWidget
{
    public function getHeading(): ?string
    {
        return __('dentalink.widgets.pending_approvals.heading');
    }

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ApprovalRequest::query()
                    ->with(['requester', 'approvable'])
                    ->where('status', ApprovalStatus::Pending)
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('approvable_type')
                    ->label(__('dentalink.fields.type'))
                    ->formatStateUsing(fn ($state) => class_basename($state)),
                Tables\Columns\TextColumn::make('requester.name')
                    ->label(__('dentalink.fields.requested_by')),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('review')
                    ->label(__('dentalink.actions.review'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (ApprovalRequest $record) => ApprovalRequestResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('dentalink.widgets.empty.no_pending_approvals'));
    }
}
