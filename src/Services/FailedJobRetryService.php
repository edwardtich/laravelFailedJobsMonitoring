<?php

namespace EdwardTich\FailedJobsManager\Services;

use Illuminate\Support\Facades\Artisan;

class FailedJobRetryService
{
    /**
     * @param array<int, int> $ids
     */
    public function retry(array $ids): int
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        if ($ids === []) {
            return 0;
        }

        return Artisan::call('queue:retry', [
            'id' => $ids,
        ]);
    }
}
