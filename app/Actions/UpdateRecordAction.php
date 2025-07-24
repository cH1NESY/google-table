<?php

namespace App\Actions;

use App\DTO\RecordDto;
use App\Models\Record;

class UpdateRecordAction
{
    public function execute(Record $record, RecordDto $dto): Record
    {
        $record->update([
            'text' => $dto->text,
            'status' => $dto->status,
        ]);
        return $record;
    }
} 