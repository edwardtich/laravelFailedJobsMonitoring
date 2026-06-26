<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use EdwardTich\FailedJobsManager\Services\FailedJobRetryService;
use Illuminate\Http\RedirectResponse;

class RetryFailedJobController
{
    public function __invoke(FailedJob $failedJob, FailedJobRetryService $retryService): RedirectResponse
    {
        abort_unless(config('failed-jobs-manager.ui.allow_retry'), 403);

        $result = $retryService->retryByFailedJobIds([$failedJob->id]);

        $redirect = redirect()->route('failed-jobs-manager::index');

        if (! $result) {
            return $redirect->with('error', __('failed-jobs-manager::failed-jobs-manager.flash.retry_failed'));
        }

        return $redirect->with('success', __('failed-jobs-manager::failed-jobs-manager.flash.retry_sent'));
    }
}
