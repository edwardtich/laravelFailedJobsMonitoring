<?php

namespace EdwardTich\FailedJobsManager\Services;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use Illuminate\Support\Facades\Artisan;

class FailedJobRetryService
{
    /**
     * @param array<int, int|string> $ids
     */
    public function retryByFailedJobIds(array $ids): bool
    {
        $ids = array_values(array_unique(array_map(static fn ($id) => (int) $id, $ids)));

        if ($ids === []) {
            return true;
        }

        $failedJobs = FailedJob::query()
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $retryKeys = [];

        foreach ($ids as $id) {
            if (! $failedJobs->has($id)) {
                continue;
            }

            $retryKeys[] = $failedJobs->get($id)->retryKey();
        }

        return $this->retry($retryKeys);
    }

    /**
     * @param array<int, int|string> $retryKeys
     */
    public function retry(array $retryKeys): bool
    {
        $retryKeys = array_values(array_unique(array_filter($retryKeys, static fn ($retryKey) => $retryKey !== null && $retryKey !== '')));

        if ($retryKeys === []) {
            return false;
        }

        return Artisan::call('queue:retry', [
            'id' => $retryKeys,
        ]) === 0;
    }
}
