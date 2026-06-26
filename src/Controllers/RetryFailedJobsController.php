<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Services\FailedJobRetryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RetryFailedJobsController
{
    public function __invoke(Request $request, FailedJobRetryService $retryService): RedirectResponse
    {
        abort_unless(config('failed-jobs-manager.ui.allow_retry'), 403);

        $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],
            'ids.*' => [
                'integer',
                'distinct',
            ],
        ]);

        $result = $retryService->retryByFailedJobIds($request->input('ids'));

        $redirect = redirect()->route('failed-jobs-manager::index');

        if (! $result) {
            return $redirect->with('error', __('failed-jobs-manager::failed-jobs-manager.flash.retry_failed'));
        }

        return $redirect->with('success', __('failed-jobs-manager::failed-jobs-manager.flash.bulk_retry_sent'));
    }
}
