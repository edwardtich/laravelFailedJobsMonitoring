<?php

namespace EdwardTich\FailedJobsManager\Events;

class FailedJobDeleted
{
    public function __construct(
        public readonly int $failedJobId,
        public readonly ?string $failedJobUuid,
        public readonly ?string $actorId,
        public readonly ?string $actorType,
        public readonly ?string $ipAddress,
    ) {
    }
}
