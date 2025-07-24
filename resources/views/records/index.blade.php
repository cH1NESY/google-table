@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Список записей</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="mb-3">
        <form action="{{ route('records.setSheetUrl') }}" method="POST" class="row g-2 align-items-center mb-2">
            @csrf
            <div class="col-auto">
                <label for="sheet_url" class="col-form-label">Google Sheet URL:</label>
            </div>
            <div class="col-auto">
                <input type="url" name="sheet_url" id="sheet_url" class="form-control" value="{{ old('sheet_url', $sheetUrl) }}" required style="min-width:350px;">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-info">Сохранить</button>
            </div>
        </form>
        @error('sheet_url')<div class="text-danger">{{ $message }}</div>@enderror
        <a href="{{ route('records.create') }}" class="btn btn-primary">Добавить запись</a>
        <form action="{{ route('records.generate') }}" method="POST" style="display:inline-block">
            @csrf
            <button type="submit" class="btn btn-success">Сгенерировать 1000 строк</button>
        </form>
        <form action="{{ route('records.clear') }}" method="POST" style="display:inline-block" onsubmit="return confirm('Очистить таблицу?')">
            @csrf
            <button type="submit" class="btn btn-danger">Очистить таблицу</button>
        </form>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Текст</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->text }}</td>
                    <td>{{ $record->status }}</td>
                    <td>
                        <a href="{{ route('records.edit', $record) }}" class="btn btn-sm btn-warning">Редактировать</a>
                        <form action="{{ route('records.destroy', $record) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить запись?')">Удалить</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $records->links('vendor.pagination.bootstrap-4') }}
</div>
@endsection
