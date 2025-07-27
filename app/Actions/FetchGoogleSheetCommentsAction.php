<?php

namespace App\Actions;

use App\DTO\GoogleSheetCommentDto;
use App\Models\Setting;

class FetchGoogleSheetCommentsAction
{
    private \Google_Client $client;

    public function __construct()
    {
        $this->client = new \Google_Client();
    }

    /**
     * @param int|null $count
     * @return GoogleSheetCommentDto[]
     */
    public function execute(int $count = null): array
    {
        $sheetUrl = Setting::where('key', 'google_sheet_url')->value('value');
        if ($sheetUrl === null || $sheetUrl === false || $sheetUrl === '') {
            return [];
        }
        $spreadsheetId = $this->getSheetIdFromUrl($sheetUrl);
        $range = 'A2:F';
        $rows = $this->getRows($spreadsheetId, $range);
        $result = [];
        $printed = 0;
        foreach ($rows as $row) {
            if ($count !== null && $count !== 0 && $printed >= $count) {
                break;
            }
            if (array_key_exists(0, $row)) {
                $id = $row[0];
            } else {
                $id = '';
            }
            if (array_key_exists(5, $row)) {
                $comment = $row[5];
            } else {
                $comment = '';
            }
            $result[] = new GoogleSheetCommentDto($id, $comment);
            $printed++;
        }
        return $result;
    }

    private function getSheetService(): \Google_Service_Sheets
    {
        $this->client->setApplicationName('Laravel Google Table Sync');
        $this->client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->setAccessType('offline');
        return new \Google_Service_Sheets($this->client);
    }

    private function getSheetIdFromUrl(string $url): ?string
    {
        if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * @param string $spreadsheetId
     * @param string $range
     * @return array<int, array<int, mixed>>
     */
    private function getRows(string $spreadsheetId, string $range): array
    {
        $service = $this->getSheetService();
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
        if ($values === null) {
            return [];
        }
        return $values;
    }
} 