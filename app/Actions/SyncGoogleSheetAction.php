<?php

namespace App\Actions;

use App\DTO\GoogleSheetSyncDto;
use App\Models\Setting;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ClearValuesRequest;
use Google_Service_Sheets_ValueRange;

class SyncGoogleSheetAction
{

    private Google_Service_Sheets $service;

    public function __construct()
    {
        $client = new Google_Client();
        $client->setApplicationName('Laravel Google Table Sync');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->setAccessType('offline');
        $this->service = new Google_Service_Sheets($client);
    }

    public function execute(GoogleSheetSyncDto $dto): bool
    {
        $sheetUrl = Setting::where('key', 'google_sheet_url')->value('value');
        if ($sheetUrl === null || $sheetUrl === false || $sheetUrl === '') {
            return false;
        }
        $spreadsheetId = $this->getSheetIdFromUrl($sheetUrl);
        $range = 'A1:Z';

        // Формируем новые строки для выгрузки
        $header = ['ID', 'Text', 'Status', 'Created At', 'Updated At', 'Комментарий'];
        $newRows = [$header];
        foreach ($dto->records as $record) {
            $row = [
                $record['id'],
                $record['text'],
                $record['status'],
                $record['created_at'],
                $record['updated_at'],
                $dto->comments[$record['id']] ?? '',
            ];
            $newRows[] = $row;
        }
        $this->clearRange($spreadsheetId, $range);
        $this->writeRows($spreadsheetId, $range, $newRows);
        return true;
    }

    private function getSheetIdFromUrl(string $url): ?string
    {
        if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function writeRows(string $spreadsheetId, string $range, array $values): mixed
    {
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = ['valueInputOption' => 'RAW'];
        return $this->service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
    }

    private function clearRange(string $spreadsheetId, string $range): void
    {
        $this->service->spreadsheets_values->clear($spreadsheetId, $range, new Google_Service_Sheets_ClearValuesRequest());
    }
}
