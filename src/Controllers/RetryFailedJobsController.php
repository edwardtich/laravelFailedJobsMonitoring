<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Services\FailedJobRetryService;
use EdwardTich\FailedJobsManager\Services\FailedJobsManagerAuditDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RetryFailedJobsController
{
    public function __invoke(
        Request $request,
        FailedJobRetryService $retryService,
        FailedJobsManagerAuditDispatcher $auditDispatcher,
    ): RedirectResponse
    {
        abort_unless(config('failed-jobs-manager.ui.allow_retry'), 403);

        $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
                'max:' . max(1, (int) config('failed-jobs-manager.ui.retry_max_ids', 50)),
            ],
            'ids.*' => [
                'integer',
                'min:1',
            ],
        ]);

        $result = $retryService->retryByFailedJobIdsWithResult($request->input('ids'));

        $auditDispatcher->retried($result->retriedFailedJobIds(), $request->user(), $request->ip());

        $redirect = redirect()->route('failed-jobs-manager::index');

        if ($result->isPartiallySuccessful()) {
            return $redirect->with('warning', __('failed-jobs-manager::failed-jobs-manager.flash.bulk_retry_partially_sent'));
        }

        if (! $result->isSuccessful()) {
            return $redirect->with('error', __('failed-jobs-manager::failed-jobs-manager.flash.retry_failed'));
        }

        return $redirect->with('success', __('failed-jobs-manager::failed-jobs-manager.flash.bulk_retry_sent'));
    }
}
