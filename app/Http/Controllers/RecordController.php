<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record;
use Illuminate\Support\Str;
use App\Models\Setting;
use App\Actions\CreateRecordAction;
use App\DTO\RecordDto;
use App\Actions\UpdateRecordAction;
use App\Actions\DeleteRecordAction;
use App\Actions\GenerateRecordsAction;
use App\Actions\ClearRecordsAction;
use App\Actions\SetGoogleSheetUrlAction;
use App\DTO\GoogleSheetUrlDto;
use App\Http\Requests\StoreRecordRequest;
use App\Http\Requests\UpdateRecordRequest;
use App\Http\Requests\SetGoogleSheetUrlRequest;

class RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $records = Record::orderByDesc('id')->paginate(20);
        $sheetUrl = Setting::where('key', 'google_sheet_url')->value('value');
        return view('records.index', compact('records', 'sheetUrl'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('records.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecordRequest $request)
    {
        $dto = new RecordDto($request->validated());
        (new CreateRecordAction())->execute($dto);
        return redirect()->route('records.index')->with('success', 'Запись добавлена');
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $record = Record::findOrFail($id);
        return view('records.edit', compact('record'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecordRequest $request, $id)
    {
        $record = Record::findOrFail($id);
        $dto = new RecordDto($request->validated());
        (new UpdateRecordAction())->execute($record, $dto);
        return redirect()->route('records.index')->with('success', 'Запись обновлена');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $record = Record::findOrFail($id);
        (new DeleteRecordAction())->execute($record);
        return redirect()->route('records.index')->with('success', 'Запись удалена');
    }

    // Генерация 1000 строк
    public function generate()
    {
        (new GenerateRecordsAction())->execute();
        return redirect()->route('records.index')->with('success', '1000 строк сгенерировано');
    }

    // Очистка таблицы
    public function clear()
    {
        (new ClearRecordsAction())->execute();
        return redirect()->route('records.index')->with('success', 'Таблица очищена');
    }

    public function setSheetUrl(SetGoogleSheetUrlRequest $request)
    {
        $dto = new GoogleSheetUrlDto($request->input('sheet_url'));
        (new SetGoogleSheetUrlAction())->execute($dto);
        return redirect()->route('records.index')->with('success', 'URL Google Sheet сохранён');
    }
}
