<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Services\GoogleSheetService;
use App\Actions\FetchGoogleSheetCommentsAction;

class FetchGoogleSheetComments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-google-sheet-comments {count? : Ограничить количество строк}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Вывести ID и комментарии из Google Sheet с прогресс-баром';

    /**
     * Execute the console command.
     */
    public function handle():void
    {
        $count = $this->argument('count') ? (int)$this->argument('count') : null;
        $comments = (new FetchGoogleSheetCommentsAction())->execute($count);
        if (empty($comments)) {
            $this->info('Нет данных в Google Sheet.');
        }
        $total = count($comments);
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        foreach ($comments as $dto) {
            $this->newLine();
            $this->line("ID: {$dto->id} | Комментарий: {$dto->comment}");
            $bar->advance();
        }
        $bar->finish();

        $this->info('Готово.');
    }
}
