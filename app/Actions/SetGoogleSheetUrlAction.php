<?php

namespace App\Actions;

use App\DTO\GoogleSheetUrlDto;
use App\Models\Setting;

class SetGoogleSheetUrlAction
{
    public function execute(GoogleSheetUrlDto $dto): void
    {
        Setting::updateOrCreate(
            ['key' => 'google_sheet_url'],
            ['value' => $dto->url]
        );
    }
} 