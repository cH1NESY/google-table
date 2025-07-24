<?php

namespace App\Actions;

use App\DTO\GoogleSheetCommentDto;
use App\Models\Setting;

class FetchGoogleSheetCommentsAction
{
    /**
     * @return GoogleSheetCommentDto[]
     */
    public function execute(int $count = null): array
    {
        $sheetUrl = Setting::where('key', 'google_sheet_url')->value('value');
        if (!$sheetUrl) return [];
        $spreadsheetId = $this->getSheetIdFromUrl($sheetUrl);
        $range = 'A2:F';
        $rows = $this->getRows($spreadsheetId, $range);
        $result = [];
        $printed = 0;
        foreach ($rows as $row) {
            if ($count && $printed >= $count) break;
            $id = $row[0] ?? '';
            $comment = $row[5] ?? '';
            $result[] = new GoogleSheetCommentDto($id, $comment);
            $printed++;
        }
        return $result;
    }

    private function getSheetService(): \Google_Service_Sheets
    {
        $client = new \Google_Client();
        $client->setApplicationName('Laravel Google Table Sync');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');
        return new \Google_Service_Sheets($client);
    }

    private function getSheetIdFromUrl($url)
    {
        if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function getRows($spreadsheetId, $range)
    {
        $service = $this->getSheetService();
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        return $response->getValues();
    }
} 