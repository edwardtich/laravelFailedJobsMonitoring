<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use Illuminate\View\View;

class ShowFailedJobController
{
    public function __invoke(FailedJob $failedJob): View
    {
        return view('failed-jobs-manager::job', compact('failedJob'));
    }
}
