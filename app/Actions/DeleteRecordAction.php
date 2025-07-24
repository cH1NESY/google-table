<?php

namespace App\Actions;

use App\Models\Record;

class DeleteRecordAction
{
    public function execute(Record $record): void
    {
        $record->delete();
    }
} 