<?php

namespace EdwardTich\FailedJobsManager\Queries;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class FailedJobQuery
{
    /**
     * @param array<string, mixed> $filters
     *
     * @return Builder<FailedJob>
     */
    public function list(array $filters = [], bool $fullText = false): Builder
    {
        return FailedJob::query()
            ->filter($filters, $fullText)
            ->ordered();
    }

    /**
     * @return array<string, string>
     */
    public function queueOptions(): array
    {
        $ttl = (int) config('failed-jobs-manager.ui.queues_cache_ttl', 60);
        $resolve = fn () => $this->queues()->pluck('queue', 'queue')->all();

        if ($ttl <= 0) {
            return $resolve();
        }

        return Cache::remember('failed-jobs-manager:queues:' . (new FailedJob())->getTable(), $ttl, $resolve);
    }

    /**
     * @return Builder<FailedJob>
     */
    public function queues(): Builder
    {
        return FailedJob::query()
            ->select('queue')
            ->whereNotNull('queue')
            ->distinct()
            ->orderBy('queue');
    }
}
