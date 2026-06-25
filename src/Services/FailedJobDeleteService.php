<?php

namespace EdwardTich\FailedJobsManager\Services;

use EdwardTich\FailedJobsManager\Models\FailedJob;

class FailedJobDeleteService
{
    public function delete(FailedJob $failedJob): bool
    {
        return (bool) $failedJob->delete();
    }
}
