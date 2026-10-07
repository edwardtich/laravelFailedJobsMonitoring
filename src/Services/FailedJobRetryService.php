<?php

namespace EdwardTich\FailedJobsManager\Services;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use EdwardTich\FailedJobsManager\Support\FailedJobRetryResult;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Throwable;

class FailedJobRetryService
{
    /**
     * @param array<int, int|string> $ids
     */
    public function retryByFailedJobIds(array $ids): bool
    {
        return $this->retryByFailedJobIdsWithResult($ids)->isSuccessful();
    }

    /**
     * @param array<int, int|string> $ids
     */
    public function retryByFailedJobIdsWithResult(array $ids): FailedJobRetryResult
    {
        $ids = array_values(array_unique(array_filter(
            array_map(static fn ($id) => (int) $id, $ids),
            static fn (int $id) => $id > 0,
        )));

        $retriedFailedJobIds = [];
        $completed = true;
        $columns = (new FailedJob())->hasUuidColumn() ? ['id', 'uuid'] : ['id'];

        foreach (array_chunk($ids, $this->retryChunkSize()) as $chunk) {
            $failedJobs = FailedJob::query()
                ->select($columns)
                ->whereKey($chunk)
                ->get()
                ->keyBy('id');

            foreach ($chunk as $id) {
                $failedJob = $failedJobs->get($id);

                if ($failedJob === null) {
                    continue;
                }

                $retried = $this->retryOne($failedJob);

                if ($retried === null) {
                    continue;
                }

                if ($retried) {
                    $retriedFailedJobIds[] = $id;
                } else {
                    $completed = false;
                }
            }
        }

        return $completed
            ? FailedJobRetryResult::completed($retriedFailedJobIds)
            : FailedJobRetryResult::failed($retriedFailedJobIds);
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

        try {
            return Artisan::call('queue:retry', [
                'id' => $retryKeys,
            ]) === 0;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    /**
     * Returns null when the job was already taken by a concurrent request.
     */
    private function retryOne(FailedJob $failedJob): ?bool
    {
        $retryKey = $failedJob->retryKey();
        $store = Cache::getStore();
        $lock = $store instanceof LockProvider
            ? $store->lock('failed-jobs-manager:retry:' . $failedJob->getTable() . ':' . $retryKey, $this->lockSeconds())
            : null;

        if ($lock !== null && ! $lock->get()) {
            return null;
        }

        try {
            // Another request may have retried (and forgotten) the job between loading and locking.
            if (! FailedJob::query()->whereKey($failedJob->getKey())->exists()) {
                return null;
            }

            return $this->retry([$retryKey]);
        } finally {
            $lock?->release();
        }
    }

    private function retryChunkSize(): int
    {
        return max(1, (int) config('failed-jobs-manager.ui.retry_chunk_size', 100));
    }

    private function lockSeconds(): int
    {
        return max(1, (int) config('failed-jobs-manager.ui.retry_lock_seconds', 60));
    }
}
