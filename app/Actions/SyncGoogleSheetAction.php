<?php

namespace App\Actions;

use App\DTO\GoogleSheetSyncDto;
use App\Models\Setting;

class SyncGoogleSheetAction
{
    public function execute(GoogleSheetSyncDto $dto): bool
    {
        $sheetUrl = Setting::where('key', 'google_sheet_url')->value('value');
        if (!$sheetUrl) return false;
        $spreadsheetId = $this->getSheetIdFromUrl($sheetUrl);
        $range = 'A1:Z';
        $service = $this->getSheetService();

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
        $this->clearRange($service, $spreadsheetId, $range);
        $this->writeRows($service, $spreadsheetId, $range, $newRows);
        return true;
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

    private function writeRows($service, $spreadsheetId, $range, $values)
    {
        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $params = ['valueInputOption' => 'RAW'];
        return $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
    }

    private function clearRange($service, $spreadsheetId, $range)
    {
        $service->spreadsheets_values->clear($spreadsheetId, $range, new \Google_Service_Sheets_ClearValuesRequest());
    }
} 