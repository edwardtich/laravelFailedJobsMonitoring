<?php

namespace EdwardTich\FailedJobsManager\Services;

use EdwardTich\FailedJobsManager\Events\FailedJobDeleted;
use EdwardTich\FailedJobsManager\Events\FailedJobsRetried;
use EdwardTich\FailedJobsManager\Models\FailedJob;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Events\Dispatcher;

class FailedJobsManagerAuditDispatcher
{
    public function __construct(private readonly Dispatcher $events)
    {
    }

    /**
     * @param array<int, int> $failedJobIds
     */
    public function retried(array $failedJobIds, ?Authenticatable $actor, ?string $ipAddress): void
    {
        if ($failedJobIds === []) {
            return;
        }

        $this->events->dispatch(new FailedJobsRetried(
            failedJobIds: $failedJobIds,
            actorId: $this->actorId($actor),
            actorType: $actor ? $actor::class : null,
            ipAddress: $ipAddress,
        ));
    }

    public function deleted(FailedJob $failedJob, ?Authenticatable $actor, ?string $ipAddress): void
    {
        $this->events->dispatch(new FailedJobDeleted(
            failedJobId: $failedJob->id,
            failedJobUuid: $failedJob->uuidValue(),
            actorId: $this->actorId($actor),
            actorType: $actor ? $actor::class : null,
            ipAddress: $ipAddress,
        ));
    }

    private function actorId(?Authenticatable $actor): ?string
    {
        $identifier = $actor?->getAuthIdentifier();

        return $identifier === null ? null : (string) $identifier;
    }
}
