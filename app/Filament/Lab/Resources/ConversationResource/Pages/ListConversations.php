<?php

namespace App\Filament\Lab\Resources\ConversationResource\Pages;

use App\Filament\Lab\Resources\ConversationResource;
use Filament\Resources\Pages\ListRecords;

class ListConversations extends ListRecords
{
    protected static string $resource = ConversationResource::class;
}
