<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\SyncGoogleSheetAction;
use App\DTO\GoogleSheetSyncDto;
use App\Models\Record;
use App\Actions\FetchGoogleSheetCommentsAction;
use Illuminate\Support\Facades\Log;

class SyncGoogleSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-google-sheet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Allowed records with Google Sheet';

    /**
     * Execute the console command.
     */
    public function handle():void
    {
        Log::info('Scheduler sync run at ' . now());
        $this->info('Синхронизация с Google Sheet...');
        $records = Record::allowed()->get()->map(function($record) {
            return $record->toArray();
        })->all();
        // Получаем комментарии из Google Sheet через Action
        $comments = (new FetchGoogleSheetCommentsAction())->execute();
        $commentsMap = [];
        foreach ($comments as $commentDto) {
            $commentsMap[$commentDto->id] = $commentDto->comment;
        }
        $dto = new GoogleSheetSyncDto($records, $commentsMap);
        $result = (new SyncGoogleSheetAction())->execute($dto);
        if ($result) {
            $this->info('Синхронизация завершена успешно.');
        } else {
            $this->error('Не удалось выполнить синхронизацию. Проверьте настройки.');
        }
    }
}
