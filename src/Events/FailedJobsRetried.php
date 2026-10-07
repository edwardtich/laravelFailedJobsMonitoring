<?php

namespace EdwardTich\FailedJobsManager\Events;

class FailedJobsRetried
{
    /**
     * @param array<int, int> $failedJobIds
     */
    public function __construct(
        public readonly array $failedJobIds,
        public readonly ?string $actorId,
        public readonly ?string $actorType,
        public readonly ?string $ipAddress,
    ) {
    }
}
