<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use EdwardTich\FailedJobsManager\Services\FailedJobDeleteService;
use EdwardTich\FailedJobsManager\Services\FailedJobsManagerAuditDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DeleteFailedJobController
{
    public function __invoke(
        Request $request,
        FailedJob $failedJob,
        FailedJobDeleteService $deleteService,
        FailedJobsManagerAuditDispatcher $auditDispatcher,
    ): RedirectResponse
    {
        abort_unless(config('failed-jobs-manager.ui.allow_delete'), 403);

        $result = $deleteService->delete($failedJob);

        if (! $result) {
            return redirect()
                ->route('failed-jobs-manager::index')
                ->with('error', __('failed-jobs-manager::failed-jobs-manager.flash.delete_failed'));
        }

        $auditDispatcher->deleted($failedJob, $request->user(), $request->ip());

        return redirect()
            ->route('failed-jobs-manager::index')
            ->with('success', __('failed-jobs-manager::failed-jobs-manager.flash.deleted'));
    }
}
