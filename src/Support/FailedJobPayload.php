<?php

namespace EdwardTich\FailedJobsManager\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FailedJobPayload
{
    /**
     * @param array<string, mixed>|null $decoded
     */
    private function __construct(
        private readonly string $raw,
        private readonly ?array $decoded,
    ) {
    }

    public static function from(string $payload): self
    {
        $decoded = json_decode($payload, true);

        return new self($payload, is_array($decoded) ? $decoded : null);
    }

    public function isJson(): bool
    {
        return $this->decoded !== null;
    }

    public function raw(): string
    {
        return $this->raw;
    }

    public function pretty(): string
    {
        if (! $this->isJson()) {
            return $this->raw;
        }

        return json_encode($this->decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: $this->raw;
    }

    public function uuid(): ?string
    {
        return $this->string('uuid');
    }

    public function displayName(): ?string
    {
        return $this->string('displayName') ?: $this->jobClass();
    }

    public function job(): ?string
    {
        return $this->string('job');
    }

    public function jobClass(): ?string
    {
        $commandName = $this->string('data.commandName');

        if ($commandName) {
            return $commandName;
        }

        $displayName = $this->string('displayName');

        if ($displayName && str_contains($displayName, '\\')) {
            return $displayName;
        }

        return null;
    }

    public function shortJobName(): string
    {
        $jobClass = $this->jobClass() ?: $this->displayName();

        if (! $jobClass) {
            return __('failed-jobs-manager::failed-jobs-manager.payload.unknown_job');
        }

        return class_basename($jobClass);
    }

    public function attempts(): ?int
    {
        $attempts = Arr::get($this->decoded ?? [], 'attempts');

        return is_numeric($attempts) ? (int) $attempts : null;
    }

    public function maxTries(): mixed
    {
        return Arr::get($this->decoded ?? [], 'maxTries');
    }

    public function timeout(): mixed
    {
        return Arr::get($this->decoded ?? [], 'timeout');
    }

    public function command(): ?string
    {
        return $this->string('data.command');
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        return [
            __('failed-jobs-manager::failed-jobs-manager.payload.job') => $this->displayName(),
            __('failed-jobs-manager::failed-jobs-manager.payload.handler') => $this->job(),
            __('failed-jobs-manager::failed-jobs-manager.payload.uuid') => $this->uuid(),
            __('failed-jobs-manager::failed-jobs-manager.payload.attempts') => $this->attempts(),
            __('failed-jobs-manager::failed-jobs-manager.payload.max_tries') => $this->maxTries(),
            __('failed-jobs-manager::failed-jobs-manager.payload.timeout') => $this->timeout(),
        ];
    }

    public function preview(int $limit): string
    {
        return Str::limit($this->shortJobName(), $limit);
    }

    private function string(string $key): ?string
    {
        $value = Arr::get($this->decoded ?? [], $key);

        return is_string($value) && $value !== '' ? $value : null;
    }
}
