<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecordController;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('records', RecordController::class);
Route::post('records/generate', [RecordController::class, 'generate'])->name('records.generate');
Route::post('records/clear', [RecordController::class, 'clear'])->name('records.clear');
Route::post('records/set-sheet-url', [RecordController::class, 'setSheetUrl'])->name('records.setSheetUrl');

Route::get('/fetch/{count?}', function ($count = null) {
    Artisan::call('app:fetch-google-sheet-comments', $count ? ['count' => $count] : []);
    return nl2br(e(Artisan::output()));
});
