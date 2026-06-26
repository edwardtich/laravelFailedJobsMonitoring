<?php

namespace EdwardTich\FailedJobsManager\Models;

use EdwardTich\FailedJobsManager\Support\FailedJobException;
use EdwardTich\FailedJobsManager\Support\FailedJobPayload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property string|null $connection
 * @property string|null $queue
 * @property string $payload
 * @property string $exception
 * @property \Illuminate\Support\Carbon $failed_at
 *
 * @mixin Builder<\EdwardTich\FailedJobsManager\Models\FailedJob>
 */
class FailedJob extends Model
{
    protected $guarded = [];

    protected $casts = [
        'failed_at' => 'datetime',
    ];

    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('failed-jobs-manager.table'));

        if ($connection = config('failed-jobs-manager.connection')) {
            $this->setConnection($connection);
        }
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderByDesc('failed_at')->orderByDesc('id');
    }

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['queue'] ?? null, function (Builder $query, string $queue) {
                $query->where('queue', $queue);
            })
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $search = trim(Str::limit($search, (int) config('failed-jobs-manager.ui.search_max_length', 120), ''));

                if ($search === '') {
                    return;
                }

                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where('payload', 'like', '%' . $search . '%')
                        ->orWhere('exception', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['failed_from'] ?? null, function (Builder $query, string $date) {
                $query->where('failed_at', '>=', $date . ' 00:00:00');
            })
            ->when($filters['failed_to'] ?? null, function (Builder $query, string $date) {
                $query->where('failed_at', '<=', $date . ' 23:59:59');
            });
    }

    public function getPayloadPreview(): string
    {
        return $this->payloadDetails()->preview(config('failed-jobs-manager.ui.payload_preview_length'));
    }

    public function getExceptionPreview(): string
    {
        return $this->exceptionDetails()->preview(config('failed-jobs-manager.ui.exception_preview_length'));
    }

    public function payloadDetails(): FailedJobPayload
    {
        return FailedJobPayload::from($this->payload);
    }

    public function exceptionDetails(): FailedJobException
    {
        return FailedJobException::from($this->exception);
    }

    public function retryKey(): int|string
    {
        return match (config('failed-jobs-manager.ui.retry_key', 'auto')) {
            'id' => $this->id,
            'uuid' => $this->uuid ?: $this->id,
            default => $this->uuid ?: $this->id,
        };
    }
}
