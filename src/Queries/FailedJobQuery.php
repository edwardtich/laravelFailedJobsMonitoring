<?php

namespace EdwardTich\FailedJobsManager\Queries;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use Illuminate\Database\Eloquent\Builder;

class FailedJobQuery
{
    /**
     * @param array<string, mixed> $filters
     *
     * @return Builder<FailedJob>
     */
    public function list(array $filters = []): Builder
    {
        return FailedJob::query()
            ->filter($filters)
            ->ordered();
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
