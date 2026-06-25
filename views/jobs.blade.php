@extends('failed-jobs-manager::layout')

@section('title', 'Failed jobs')
@section('subtitle', 'Standard failed_jobs table with retry and cleanup actions')

@section('content')
    @if(session('success'))
        <div class="fjm-message fjm-message-success">{{ session('success') }}</div>
    @endif

    <div class="fjm-metrics">
        <div class="fjm-metric">
            <div class="fjm-metric-label">Всего упавших задач</div>
            <div class="fjm-metric-value">{{ $totalJobsCount }}</div>
            <div class="fjm-metric-help">Записи в failed_jobs</div>
        </div>
        <div class="fjm-metric">
            <div class="fjm-metric-label">Найдено</div>
            <div class="fjm-metric-value">{{ $filteredJobsCount }}</div>
            <div class="fjm-metric-help">
                @if($isFilterApplied)
                    С учетом текущих фильтров
                @else
                    Фильтры не применены
                @endif
            </div>
        </div>
    </div>

    @include('failed-jobs-manager::partials.filter')

    @if(config('failed-jobs-manager.ui.allow_retry'))
        <form id="failed-jobs-bulk-retry-form" action="{{ route('failed-jobs-manager::retry-bulk') }}" method="post">
            @csrf
            @method('patch')
        </form>
    @endif

    @include('failed-jobs-manager::partials.table')

    @if(config('failed-jobs-manager.ui.allow_retry'))
        <div class="fjm-panel">
            <button type="submit" form="failed-jobs-bulk-retry-form" class="fjm-button fjm-button-orange">Перезапустить выбранные</button>
        </div>
    @endif

    <div class="fjm-pagination">
        <div>
            Показано:
            @if($jobs->count() > 0)
                {{ $jobs->firstItem() }}-{{ $jobs->lastItem() }} из {{ $jobs->total() }}
            @else
                0
            @endif
        </div>

        <div class="fjm-pagination-actions">
            @if(!$jobs->onFirstPage())
                <a href="{{ $jobs->previousPageUrl() }}" class="fjm-button fjm-button-blue">Предыдущая</a>
            @endif

            @if($jobs->hasMorePages())
                <a href="{{ $jobs->nextPageUrl() }}" class="fjm-button fjm-button-blue">Следующая</a>
            @endif
        </div>
    </div>
@endsection
