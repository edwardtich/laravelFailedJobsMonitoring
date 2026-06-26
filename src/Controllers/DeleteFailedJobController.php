<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use EdwardTich\FailedJobsManager\Services\FailedJobDeleteService;
use Illuminate\Http\RedirectResponse;

class DeleteFailedJobController
{
    public function __invoke(FailedJob $failedJob, FailedJobDeleteService $deleteService): RedirectResponse
    {
        abort_unless(config('failed-jobs-manager.ui.allow_delete'), 403);

        $result = $deleteService->delete($failedJob);

        if (! $result) {
            return redirect()
                ->route('failed-jobs-manager::index')
                ->with('error', __('failed-jobs-manager::failed-jobs-manager.flash.delete_failed'));
        }

        return redirect()
            ->route('failed-jobs-manager::index')
            ->with('success', __('failed-jobs-manager::failed-jobs-manager.flash.deleted'));
    }
}
