<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Queries\FailedJobQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowFailedJobsController
{
    public function __invoke(Request $request, FailedJobQuery $failedJobQuery): View
    {
        $filters = $request->validate([
            'queue' => [
                'nullable',
                'string',
                'max:255',
            ],
            'search' => [
                'nullable',
                'string',
                'max:' . (int) config('failed-jobs-manager.ui.search_max_length', 120),
            ],
            'failed_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'failed_to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:failed_from',
            ],
        ]);

        $query = $failedJobQuery->list($filters);
        $isFilterApplied = collect($filters)->filter(static fn ($value) => $value !== null && $value !== '')->isNotEmpty();
        $filteredJobsCount = $isFilterApplied ? (clone $query)->count() : null;

        $jobs = $query
            ->paginate(config('failed-jobs-manager.ui.per_page'))
            ->appends($request->query());

        $queues = $failedJobQuery
            ->queues()
            ->pluck('queue', 'queue')
            ->toArray();

        return view('failed-jobs-manager::jobs', compact(
            'jobs',
            'queues',
            'filteredJobsCount',
            'isFilterApplied'
        ));
    }
}
