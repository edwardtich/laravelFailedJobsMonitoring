<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Authorization\FailedJobsManagerAuthorization;
use EdwardTich\FailedJobsManager\Queries\FailedJobQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowFailedJobsController
{
    public function __invoke(
        Request $request,
        FailedJobQuery $failedJobQuery,
        FailedJobsManagerAuthorization $authorization,
    ): View
    {
        $filters = array_filter($request->validate([
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
        ]), static fn ($value) => $value !== null && $value !== '');

        $searchPayload = $authorization->can($request->user(), FailedJobsManagerAuthorization::VIEW_SENSITIVE_PAYLOAD);

        $jobs = $failedJobQuery
            ->list($filters, $searchPayload)
            ->paginate((int) config('failed-jobs-manager.ui.per_page', 20))
            ->appends($filters);

        $isFilterApplied = $filters !== [];

        return view('failed-jobs-manager::jobs', [
            'jobs' => $jobs,
            'queues' => $failedJobQuery->queueOptions(),
            'filteredJobsCount' => $isFilterApplied ? $jobs->total() : null,
            'isFilterApplied' => $isFilterApplied,
            'canRetry' => $authorization->canOperate($request->user(), FailedJobsManagerAuthorization::RETRY),
            'canDelete' => $authorization->canOperate($request->user(), FailedJobsManagerAuthorization::DELETE),
        ]);
    }
}
