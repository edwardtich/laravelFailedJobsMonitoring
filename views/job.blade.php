@extends('failed-jobs-manager::layout')

@section('title', __('failed-jobs-manager::failed-jobs-manager.pages.show_title', ['id' => $failedJob->id]))
@section('subtitle', __('failed-jobs-manager::failed-jobs-manager.pages.show_subtitle'))

@section('content')
    @php($payload = $failedJob->payloadDetails())
    @php($exception = $failedJob->exceptionDetails())

    <a href="{{ route('failed-jobs-manager::index') }}" class="fjm-button fjm-button-green">
        {{ __('failed-jobs-manager::failed-jobs-manager.actions.back') }}
    </a>

    @if(config('failed-jobs-manager.ui.allow_retry'))
        <form action="{{ route('failed-jobs-manager::retry', $failedJob->id) }}" method="post" class="fjm-inline-form">
            @csrf
            @method('patch')
            <button type="submit" class="fjm-button fjm-button-orange">
                {{ __('failed-jobs-manager::failed-jobs-manager.actions.retry') }}
            </button>
        </form>
    @endif

    @if(config('failed-jobs-manager.ui.allow_delete'))
        <form action="{{ route('failed-jobs-manager::destroy', $failedJob->id) }}" method="post" class="fjm-inline-form">
            @csrf
            @method('delete')
            <button type="submit" class="fjm-button fjm-button-red" onclick="return confirm('{{ __('failed-jobs-manager::failed-jobs-manager.actions.delete_confirm') }}')">
                {{ __('failed-jobs-manager::failed-jobs-manager.actions.delete') }}
            </button>
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
                <th>{{ __('failed-jobs-manager::failed-jobs-manager.filters.queue') }}</th>
                <td><span class="fjm-badge">{{ $failedJob->queue ?: __('failed-jobs-manager::failed-jobs-manager.details.default_queue') }}</span></td>
            </tr>
            <tr>
                <th>{{ __('failed-jobs-manager::failed-jobs-manager.details.failed_at') }}</th>
                <td>{{ $failedJob->failed_at?->format('d.m.Y H:i:s') }}</td>
            </tr>
        </table>
    </div>

    <div class="fjm-detail-grid">
        <section class="fjm-detail-card">
            <h4 class="fjm-section-title">{{ __('failed-jobs-manager::failed-jobs-manager.details.payload_summary') }}</h4>
            <dl class="fjm-kv">
                @foreach($payload->summary() as $label => $value)
                    <dt>{{ $label }}</dt>
                    <dd>{{ $value === null || $value === '' ? __('failed-jobs-manager::failed-jobs-manager.details.empty_value') : $value }}</dd>
                @endforeach
            </dl>
        </section>

        <section class="fjm-detail-card fjm-detail-card-danger">
            <h4 class="fjm-section-title">{{ __('failed-jobs-manager::failed-jobs-manager.details.exception_summary') }}</h4>
            <div class="fjm-exception-message">{{ $exception->message() }}</div>
            <br>
            <dl class="fjm-kv">
                @foreach($exception->summary() as $label => $value)
                    <dt>{{ $label }}</dt>
                    <dd>{{ $value === null || $value === '' ? __('failed-jobs-manager::failed-jobs-manager.details.empty_value') : $value }}</dd>
                @endforeach
            </dl>
        </section>
    </div>

    @if($payload->command())
        <details class="fjm-details">
            <summary>{{ __('failed-jobs-manager::failed-jobs-manager.details.serialized_command') }}</summary>
            <div class="fjm-details-body">
                <pre class="fjm-pre">{{ $payload->command() }}</pre>
            </div>
        </details>
    @endif

    <details class="fjm-details">
        <summary>{{ __('failed-jobs-manager::failed-jobs-manager.details.raw_payload') }}</summary>
        <div class="fjm-details-body">
            <pre class="fjm-pre">{{ $payload->pretty() }}</pre>
        </div>
    </details>

    <details class="fjm-details">
        <summary>{{ __('failed-jobs-manager::failed-jobs-manager.details.raw_exception') }}</summary>
        <div class="fjm-details-body">
            <pre class="fjm-pre">{{ $exception->raw() }}</pre>
        </div>
    </details>
@endsection
