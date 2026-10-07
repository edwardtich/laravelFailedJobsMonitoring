<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use EdwardTich\FailedJobsManager\Services\FailedJobsManagerAuditDispatcher;
use EdwardTich\FailedJobsManager\Services\FailedJobRetryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RetryFailedJobController
{
    public function __invoke(
        Request $request,
        FailedJob $failedJob,
        FailedJobRetryService $retryService,
        FailedJobsManagerAuditDispatcher $auditDispatcher,
    ): RedirectResponse
    {
        abort_unless(config('failed-jobs-manager.ui.allow_retry'), 403);

        $result = $retryService->retryByFailedJobIdsWithResult([$failedJob->id]);

        $auditDispatcher->retried($result->retriedFailedJobIds(), $request->user(), $request->ip());

        $redirect = redirect()->route('failed-jobs-manager::index');

        if (! $result->isSuccessful()) {
            return $redirect->with('error', __('failed-jobs-manager::failed-jobs-manager.flash.retry_failed'));
        }

        return $redirect->with('success', __('failed-jobs-manager::failed-jobs-manager.flash.retry_sent'));
    }
}
