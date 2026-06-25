<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use EdwardTich\FailedJobsManager\Queries\FailedJobQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowFailedJobsController
{
    public function __invoke(Request $request, FailedJobQuery $failedJobQuery): View
    {
        $filters = $request->only('queue', 'search', 'failed_from', 'failed_to');
        $query = $failedJobQuery->list($filters);
        $totalJobsCount = FailedJob::query()->count();
        $filteredJobsCount = (clone $query)->count();
        $isFilterApplied = collect($filters)->filter(static fn ($value) => $value !== null && $value !== '')->isNotEmpty();

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
            'totalJobsCount',
            'filteredJobsCount',
            'isFilterApplied'
        ));
    }
}
