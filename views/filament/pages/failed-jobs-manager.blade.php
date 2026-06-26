<x-filament-panels::page>
    <style>
        .fjm-panel {
            display: grid;
            gap: 1rem;
        }

        .fjm-card {
            border: 1px solid rgba(148, 163, 184, .24);
            border-radius: .75rem;
            background: rgb(255, 255, 255);
            box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
        }

        .dark .fjm-card {
            border-color: rgba(148, 163, 184, .18);
            background: rgb(24, 24, 27);
        }

        .fjm-metric {
            max-width: 20rem;
            padding: 1rem;
        }

        .fjm-label {
            display: block;
            margin-bottom: .35rem;
            font-size: .8125rem;
            font-weight: 600;
            color: rgb(55, 65, 81);
        }

        .dark .fjm-label {
            color: rgb(228, 228, 231);
        }

        .fjm-muted {
            color: rgb(100, 116, 139);
        }

        .dark .fjm-muted {
            color: rgb(161, 161, 170);
        }

        .fjm-input {
            appearance: none;
            -moz-appearance: none;
            -webkit-appearance: none;
            width: 100%;
            min-height: 2.5rem;
            border: 1px solid rgb(209, 213, 219);
            border-radius: .5rem;
            background: rgb(255, 255, 255);
            padding: .45rem .7rem;
            color: rgb(17, 24, 39);
            font-size: .875rem;
            outline: none;
        }

        select.fjm-input {
            background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 8L10 12L14 8' stroke='%236B7280' stroke-width='1.7' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-position: right .65rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem 1.25rem;
            padding-right: 2.25rem;
        }

        select.fjm-input::-ms-expand {
            display: none;
        }

        .fjm-input:focus {
            border-color: rgb(245, 158, 11);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, .16);
        }

        .dark .fjm-input {
            border-color: rgb(63, 63, 70);
            background: rgb(9, 9, 11);
            color: rgb(244, 244, 245);
        }

        .fjm-filters {
            display: grid;
            grid-template-columns: minmax(10rem, 1fr) minmax(16rem, 2fr) minmax(10rem, 1fr) minmax(10rem, 1fr) auto;
            gap: .9rem;
            align-items: end;
            padding: 1rem;
        }

        .fjm-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .fjm-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .fjm-pagination-actions {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .fjm-selected {
            font-size: .875rem;
            color: rgb(100, 116, 139);
        }

        .dark .fjm-selected {
            color: rgb(161, 161, 170);
        }

        .fjm-table-wrap {
            overflow-x: auto;
        }

        .fjm-table {
            width: 100%;
            min-width: 980px;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        .fjm-table th {
            border-bottom: 1px solid rgba(148, 163, 184, .22);
            background: rgb(248, 250, 252);
            padding: .75rem 1rem;
            color: rgb(100, 116, 139);
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-align: left;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .dark .fjm-table th {
            border-bottom-color: rgba(148, 163, 184, .16);
            background: rgb(39, 39, 42);
            color: rgb(161, 161, 170);
        }

        .fjm-table td {
            border-bottom: 1px solid rgba(148, 163, 184, .16);
            padding: .95rem 1rem;
            color: rgb(39, 39, 42);
            vertical-align: top;
        }

        .dark .fjm-table td {
            border-bottom-color: rgba(148, 163, 184, .12);
            color: rgb(244, 244, 245);
        }

        .fjm-table tr:last-child td {
            border-bottom: 0;
        }

        .fjm-col-check {
            width: 3rem;
        }

        .fjm-col-id {
            width: 5rem;
        }

        .fjm-col-queue {
            width: 9rem;
        }

        .fjm-col-payload {
            width: 22rem;
        }

        .fjm-col-date {
            width: 12rem;
        }

        .fjm-col-actions {
            width: 12rem;
            text-align: right !important;
        }

        .fjm-id {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
        }

        .fjm-badge {
            display: inline-flex;
            max-width: 100%;
            border-radius: 999px;
            background: rgb(241, 245, 249);
            padding: .2rem .55rem;
            color: rgb(51, 65, 85);
            font-size: .75rem;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dark .fjm-badge {
            background: rgb(39, 39, 42);
            color: rgb(228, 228, 231);
        }

        .fjm-job-title {
            font-weight: 700;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .fjm-job-meta {
            margin-top: .25rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: .75rem;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .fjm-exception {
            color: rgb(185, 28, 28);
            font-weight: 600;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        .dark .fjm-exception {
            color: rgb(248, 113, 113);
        }

        .fjm-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: .5rem;
        }

        .fjm-details-row td {
            background: rgb(248, 250, 252);
            padding: 0;
        }

        .dark .fjm-details-row td {
            background: rgb(9, 9, 11);
        }

        .fjm-summary-columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }

        .fjm-details {
            background: transparent;
        }

        .dark .fjm-details {
            background: transparent;
        }

        .fjm-details summary {
            cursor: pointer;
            padding: .85rem 1rem;
            color: rgb(55, 65, 81);
            font-size: .875rem;
            font-weight: 700;
        }

        .dark .fjm-details summary {
            color: rgb(228, 228, 231);
        }

        .fjm-details-body {
            border-top: 1px solid rgba(148, 163, 184, .18);
            padding: 1rem;
        }

        .fjm-details dl {
            margin: 0;
        }

        .fjm-summary-section {
            min-width: 0;
        }

        .fjm-summary-section + .fjm-summary-section {
            border-left: 1px solid rgba(148, 163, 184, .18);
            padding-left: .85rem;
        }

        .fjm-section-title {
            margin: 0 0 .7rem;
            color: rgb(39, 39, 42);
            font-size: .8125rem;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .dark .fjm-section-title {
            color: rgb(228, 228, 231);
        }

        .fjm-definition {
            display: grid;
            grid-template-columns: 8rem minmax(0, 1fr);
            gap: .5rem;
            margin: 0 0 .55rem;
            font-size: .8125rem;
            line-height: 1.45;
        }

        .fjm-definition:last-child {
            margin-bottom: 0;
        }

        .fjm-definition dt {
            color: rgb(100, 116, 139);
            font-weight: 700;
        }

        .dark .fjm-definition dt {
            color: rgb(161, 161, 170);
        }

        .fjm-definition dd {
            margin: 0;
            overflow-wrap: anywhere;
        }

        .fjm-empty {
            padding: 2rem;
            text-align: center;
            color: rgb(100, 116, 139);
        }

        .dark .fjm-empty {
            color: rgb(161, 161, 170);
        }

        @media (max-width: 1100px) {
            .fjm-filters {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .fjm-filters,
            .fjm-summary-columns {
                grid-template-columns: 1fr;
            }

            .fjm-summary-section + .fjm-summary-section {
                border-left: 0;
                border-top: 1px solid rgba(148, 163, 184, .18);
                padding-left: 0;
                padding-top: .85rem;
            }

            .fjm-toolbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .fjm-pagination {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <div class="fjm-panel">
        @if($isFilterApplied)
            <div class="fjm-card fjm-metric">
                <div class="fjm-label">{{ __('failed-jobs-manager::failed-jobs-manager.metrics.found') }}</div>
                <div style="font-size: 1.75rem; font-weight: 700; line-height: 1;">{{ $filteredJobsCount }}</div>
                <div class="fjm-muted" style="margin-top: .35rem; font-size: .875rem;">
                    {{ __('failed-jobs-manager::failed-jobs-manager.metrics.filtered') }}
                </div>
            </div>
        @endif

        <div class="fjm-card">
            <div class="fjm-filters">
                <label>
                    <span class="fjm-label">{{ __('failed-jobs-manager::failed-jobs-manager.filters.queue') }}</span>
                    <select wire:model.live="queue" class="fjm-input">
                        <option value="">{{ __('failed-jobs-manager::failed-jobs-manager.filters.not_selected') }}</option>
                        @foreach($queues as $queueOption)
                            <option value="{{ $queueOption }}">{{ $queueOption }}</option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span class="fjm-label">{{ __('failed-jobs-manager::failed-jobs-manager.filters.search') }}</span>
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        class="fjm-input"
                        placeholder="{{ __('failed-jobs-manager::failed-jobs-manager.filters.search_placeholder') }}"
                    >
                </label>

                <label>
                    <span class="fjm-label">{{ __('failed-jobs-manager::failed-jobs-manager.filters.failed_from') }}</span>
                    <input type="date" wire:model.live="failedFrom" class="fjm-input">
                </label>

                <label>
                    <span class="fjm-label">{{ __('failed-jobs-manager::failed-jobs-manager.filters.failed_to') }}</span>
                    <input type="date" wire:model.live="failedTo" class="fjm-input">
                </label>

                <x-filament::button color="gray" wire:click="resetFilters">
                    {{ __('failed-jobs-manager::failed-jobs-manager.filters.reset') }}
                </x-filament::button>
            </div>
        </div>

        @if(config('failed-jobs-manager.ui.allow_retry'))
            <div class="fjm-toolbar">
                <x-filament::button
                    color="warning"
                    wire:click="retrySelected"
                    :disabled="empty($selectedJobs)"
                >
                    {{ __('failed-jobs-manager::failed-jobs-manager.actions.retry_selected') }}
                </x-filament::button>

                @if(! empty($selectedJobs))
                    <span class="fjm-selected">
                        {{ __('failed-jobs-manager::failed-jobs-manager.actions.selected_count', ['count' => count($selectedJobs)]) }}
                    </span>
                @endif
            </div>
        @endif

        <div class="fjm-card fjm-table-wrap">
            <table class="fjm-table">
                <thead>
                    <tr>
                        @if(config('failed-jobs-manager.ui.allow_retry'))
                            <th class="fjm-col-check">
                                <input
                                    type="checkbox"
                                    wire:click="toggleCurrentPageSelection"
                                    @checked($allCurrentPageJobsSelected)
                                >
                            </th>
                        @endif
                        <th class="fjm-col-id">ID</th>
                        <th class="fjm-col-queue">{{ __('failed-jobs-manager::failed-jobs-manager.filters.queue') }}</th>
                        <th class="fjm-col-payload">{{ __('failed-jobs-manager::failed-jobs-manager.table.payload') }}</th>
                        <th>{{ __('failed-jobs-manager::failed-jobs-manager.table.exception') }}</th>
                        <th class="fjm-col-date">{{ __('failed-jobs-manager::failed-jobs-manager.table.failed_at') }}</th>
                        <th class="fjm-col-actions">{{ __('failed-jobs-manager::failed-jobs-manager.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                        @php($payload = $job->payloadDetails())
                        @php($exception = $job->exceptionDetails())

                        <tr>
                            @if(config('failed-jobs-manager.ui.allow_retry'))
                                <td>
                                    <input type="checkbox" wire:model.live="selectedJobs" value="{{ $job->id }}">
                                </td>
                            @endif
                            <td class="fjm-id">{{ $job->id }}</td>
                            <td>
                                <span class="fjm-badge">
                                    {{ $job->queue ?: __('failed-jobs-manager::failed-jobs-manager.details.default_queue') }}
                                </span>
                            </td>
                            <td>
                                <div class="fjm-job-title">{{ $payload->shortJobName() }}</div>
                                <div class="fjm-job-meta fjm-muted">
                                    {{ $payload->uuid() ?: __('failed-jobs-manager::failed-jobs-manager.table.no_uuid') }}
                                </div>
                            </td>
                            <td>
                                <div class="fjm-exception">{{ $exception->message() }}</div>
                            </td>
                            <td>{{ $job->failed_at?->format('d.m.Y H:i:s') }}</td>
                            <td>
                                <div class="fjm-actions">
                                    @if(config('failed-jobs-manager.ui.allow_retry'))
                                        <x-filament::button size="sm" color="warning" wire:click="retry({{ $job->id }})">
                                            {{ __('failed-jobs-manager::failed-jobs-manager.actions.retry') }}
                                        </x-filament::button>
                                    @endif

                                    @if(config('failed-jobs-manager.ui.allow_delete'))
                                        <x-filament::button
                                            size="sm"
                                            color="danger"
                                            wire:click="delete({{ $job->id }})"
                                            wire:confirm="{{ __('failed-jobs-manager::failed-jobs-manager.actions.delete_confirm') }}"
                                        >
                                            {{ __('failed-jobs-manager::failed-jobs-manager.actions.delete') }}
                                        </x-filament::button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr class="fjm-details-row">
                            <td colspan="{{ config('failed-jobs-manager.ui.allow_retry') ? 7 : 6 }}">
                                <details class="fjm-details">
                                    <summary>
                                        {{ __('failed-jobs-manager::failed-jobs-manager.details.payload_summary') }}
                                        /
                                        {{ __('failed-jobs-manager::failed-jobs-manager.details.exception_summary') }}
                                    </summary>
                                    <div class="fjm-details-body">
                                        <div class="fjm-summary-columns">
                                            <div class="fjm-summary-section">
                                                <h3 class="fjm-section-title">{{ __('failed-jobs-manager::failed-jobs-manager.details.payload_summary') }}</h3>
                                                <dl>
                                                    @foreach($payload->summary() as $label => $value)
                                                        <div class="fjm-definition">
                                                            <dt>{{ $label }}</dt>
                                                            <dd>{{ $value === null || $value === '' ? __('failed-jobs-manager::failed-jobs-manager.details.empty_value') : $value }}</dd>
                                                        </div>
                                                    @endforeach
                                                </dl>
                                            </div>

                                            <div class="fjm-summary-section">
                                                <h3 class="fjm-section-title">{{ __('failed-jobs-manager::failed-jobs-manager.details.exception_summary') }}</h3>
                                                <div class="fjm-exception" style="margin-bottom: .7rem;">{{ $exception->message() }}</div>
                                                <dl>
                                                    @foreach($exception->summary() as $label => $value)
                                                        <div class="fjm-definition">
                                                            <dt>{{ $label }}</dt>
                                                            <dd>{{ $value === null || $value === '' ? __('failed-jobs-manager::failed-jobs-manager.details.empty_value') : $value }}</dd>
                                                        </div>
                                                    @endforeach
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ config('failed-jobs-manager.ui.allow_retry') ? 7 : 6 }}">
                                <div class="fjm-empty">{{ __('failed-jobs-manager::failed-jobs-manager.table.empty') }}</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="fjm-pagination">
            <div class="fjm-muted" style="font-size: .875rem;">
                {{ __('failed-jobs-manager::failed-jobs-manager.table.shown') }}:
                @if($jobs->count() > 0)
                    {{ $jobs->firstItem() }}-{{ $jobs->lastItem() }} {{ __('failed-jobs-manager::failed-jobs-manager.table.of') }} {{ $jobs->total() }}
                @else
                    0
                @endif
            </div>

            <div class="fjm-pagination-actions">
                @if(! $jobs->onFirstPage())
                    <x-filament::button color="gray" size="sm" wire:click="previousPage">
                        {{ __('failed-jobs-manager::failed-jobs-manager.table.previous') }}
                    </x-filament::button>
                @endif

                @if($jobs->hasMorePages())
                    <x-filament::button color="gray" size="sm" wire:click="nextPage">
                        {{ __('failed-jobs-manager::failed-jobs-manager.table.next') }}
                    </x-filament::button>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
