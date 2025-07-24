<?php

namespace App\Actions;

use App\DTO\RecordDto;
use App\Models\Record;

class CreateRecordAction
{
    public function execute(RecordDto $dto): Record
    {
        return Record::create([
            'text' => $dto->text,
            'status' => $dto->status,
        ]);
    }
} 