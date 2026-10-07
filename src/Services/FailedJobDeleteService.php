<?php

namespace EdwardTich\FailedJobsManager\Services;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class FailedJobDeleteService
{
    public function delete(FailedJob $failedJob): bool
    {
        try {
            return Artisan::call('queue:forget', [
                'id' => $failedJob->retryKey(),
            ]) === 0;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}
