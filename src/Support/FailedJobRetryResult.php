<?php

namespace EdwardTich\FailedJobsManager\Support;

class FailedJobRetryResult
{
    /**
     * @param array<int, int> $retriedFailedJobIds
     */
    private function __construct(
        private readonly bool $completed,
        private readonly array $retriedFailedJobIds,
    ) {
    }

    /**
     * @param array<int, int> $retriedFailedJobIds
     */
    public static function completed(array $retriedFailedJobIds): self
    {
        return new self(true, $retriedFailedJobIds);
    }

    /**
     * @param array<int, int> $retriedFailedJobIds
     */
    public static function failed(array $retriedFailedJobIds): self
    {
        return new self(false, $retriedFailedJobIds);
    }

    public function isSuccessful(): bool
    {
        return $this->completed && $this->retriedFailedJobIds !== [];
    }

    public function isPartiallySuccessful(): bool
    {
        return ! $this->completed && $this->retriedFailedJobIds !== [];
    }

    /**
     * @return array<int, int>
     */
    public function retriedFailedJobIds(): array
    {
        return $this->retriedFailedJobIds;
    }
}
