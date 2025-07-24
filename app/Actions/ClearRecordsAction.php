<?php

namespace App\Actions;

use App\Models\Record;

class ClearRecordsAction
{
    public function execute(): void
    {
        Record::truncate();
    }
} 