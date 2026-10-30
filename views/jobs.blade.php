@extends('failed-jobs-manager::layout')

@section('title', __('failed-jobs-manager::failed-jobs-manager.pages.index_title'))
@section('subtitle', __('failed-jobs-manager::failed-jobs-manager.pages.index_subtitle'))

@section('content')
    @if(session('success'))
        <div class="fjm-message fjm-message-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="fjm-message fjm-message-error">{{ session('error') }}</div>
    @endif

    @if(session('warning'))
        <div class="fjm-message fjm-message-warning">{{ session('warning') }}</div>
    @endif

    @if($errors->any())
        <div class="fjm-message fjm-message-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if($isFilterApplied)
        <div class="fjm-metrics">
            <div class="fjm-metric">
                <div class="fjm-metric-label">{{ __('failed-jobs-manager::failed-jobs-manager.metrics.found') }}</div>
                <div class="fjm-metric-value">{{ $filteredJobsCount }}</div>
                <div class="fjm-metric-help">{{ __('failed-jobs-manager::failed-jobs-manager.metrics.filtered') }}</div>
            </div>
        </div>
    @endif

    @include('failed-jobs-manager::partials.filter')

    @if($canRetry)
        <form id="failed-jobs-bulk-retry-form" action="{{ route('failed-jobs-manager::retry-bulk') }}" method="post">
            @csrf
            @method('patch')
        </form>
    @endif

    @include('failed-jobs-manager::partials.table')

    @if($canRetry)
        <div class="fjm-panel">
            <button type="submit" form="failed-jobs-bulk-retry-form" class="fjm-button fjm-button-orange" onclick="return confirm(@js(__('failed-jobs-manager::failed-jobs-manager.actions.retry_selected_confirm')))">
                {{ __('failed-jobs-manager::failed-jobs-manager.actions.retry_selected') }}
            </button>
        </div>
    @endif

    <div class="fjm-pagination">
        <div>
            {{ __('failed-jobs-manager::failed-jobs-manager.table.shown') }}:
            @if($jobs->count() > 0)
                {{ $jobs->firstItem() }}-{{ $jobs->lastItem() }} {{ __('failed-jobs-manager::failed-jobs-manager.table.of') }} {{ $jobs->total() }}
            @else
                0
            @endif
        </div>

        <div class="fjm-pagination-actions">
            @if(!$jobs->onFirstPage())
                <a href="{{ $jobs->previousPageUrl() }}" class="fjm-button fjm-button-blue">
                    {{ __('failed-jobs-manager::failed-jobs-manager.table.previous') }}
                </a>
            @endif

            @if($jobs->hasMorePages())
                <a href="{{ $jobs->nextPageUrl() }}" class="fjm-button fjm-button-blue">
                    {{ __('failed-jobs-manager::failed-jobs-manager.table.next') }}
                </a>
            @endif
        </div>
    </div>
@endsection
