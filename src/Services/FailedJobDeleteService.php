<?php

namespace EdwardTich\FailedJobsManager\Services;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use Illuminate\Support\Facades\Artisan;

class FailedJobDeleteService
{
    public function delete(FailedJob $failedJob): bool
    {
        return Artisan::call('queue:forget', [
            'id' => $failedJob->retryKey(),
        ]) === 0;
    }
}
