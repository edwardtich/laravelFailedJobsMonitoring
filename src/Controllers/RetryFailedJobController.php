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

        $retryService->retry([$failedJob->id]);

        return redirect()
            ->route('failed-jobs-manager::index')
            ->with('success', 'Задача отправлена на перезапуск');
    }
}
