<?php

namespace App\Actions;

use App\Models\Record;
use Illuminate\Support\Str;

class GenerateRecordsAction
{
    public function execute(int $count = 1000): void
    {
        $records = [];
        for ($i = 0; $i < $count; $i++) {
            $status = $i % 2 === 0 ? 'Allowed' : 'Prohibited';
            $records[] = [
                'text' => Str::random(20),
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Record::insert($records);
    }
} 