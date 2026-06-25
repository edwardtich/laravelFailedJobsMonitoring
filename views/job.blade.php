@extends('failed-jobs-manager::layout')

@section('title', 'Failed job #' . $failedJob->id)
@section('subtitle', 'Payload and exception details')

@section('content')
    <a href="{{ route('failed-jobs-manager::index') }}" class="fjm-button fjm-button-green">Назад</a>

    @if(config('failed-jobs-manager.ui.allow_retry'))
        <form action="{{ route('failed-jobs-manager::retry', $failedJob->id) }}" method="post" class="fjm-inline-form">
            @csrf
            @method('patch')
            <button type="submit" class="fjm-button fjm-button-orange">Перезапустить</button>
        </form>
    @endif

    @if(config('failed-jobs-manager.ui.allow_delete'))
        <form action="{{ route('failed-jobs-manager::destroy', $failedJob->id) }}" method="post" class="fjm-inline-form">
            @csrf
            @method('delete')
            <button type="submit" class="fjm-button fjm-button-red" onclick="return confirm('Удалить запись?')">Удалить</button>
        </form>
    @endif

    <br><br>

    <div class="fjm-table-wrap">
        <table class="fjm-table">
            <tr>
                <th>ID</th>
                <td><span class="fjm-id">{{ $failedJob->id }}</span></td>
            </tr>
            <tr>
                <th>UUID</th>
                <td>{{ $failedJob->uuid }}</td>
            </tr>
            <tr>
                <th>Queue</th>
                <td><span class="fjm-badge">{{ $failedJob->queue ?: 'default' }}</span></td>
            </tr>
            <tr>
                <th>Дата падения</th>
                <td>{{ $failedJob->failed_at?->format('d.m.Y H:i:s') }}</td>
            </tr>
        </table>
    </div>

    <h4 class="fjm-section-title">Payload</h4>
    <pre class="fjm-pre">{{ $failedJob->payload }}</pre>

    <h4 class="fjm-section-title">Exception</h4>
    <pre class="fjm-pre">{{ $failedJob->exception }}</pre>
@endsection
