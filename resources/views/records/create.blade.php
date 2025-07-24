@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Добавить запись</h1>
    <form action="{{ route('records.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="text" class="form-label">Текст</label>
            <input type="text" name="text" id="text" class="form-control" value="{{ old('text') }}" required>
            @error('text')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Статус</label>
            <select name="status" id="status" class="form-select" required>
                <option value="Allowed" {{ old('status') == 'Allowed' ? 'selected' : '' }}>Allowed</option>
                <option value="Prohibited" {{ old('status') == 'Prohibited' ? 'selected' : '' }}>Prohibited</option>
            </select>
            @error('status')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="{{ route('records.index') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection 