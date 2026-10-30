<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Authorization\FailedJobsManagerAuthorization;
use EdwardTich\FailedJobsManager\Models\FailedJob;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowFailedJobController
{
    public function __invoke(
        Request $request,
        FailedJob $failedJob,
        FailedJobsManagerAuthorization $authorization,
    ): View
    {
        $user = $request->user();

        return view('failed-jobs-manager::job', [
            'failedJob' => $failedJob,
            'canViewUnmasked' => $authorization->canViewUnmasked($user),
            'canRetry' => $authorization->canOperate($user, FailedJobsManagerAuthorization::RETRY),
            'canDelete' => $authorization->canOperate($user, FailedJobsManagerAuthorization::DELETE),
        ]);
    }
}
