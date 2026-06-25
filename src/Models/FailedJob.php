<?php

namespace EdwardTich\FailedJobsManager\Models;

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
                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where('payload', 'like', '%' . $search . '%')
                        ->orWhere('exception', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['failed_from'] ?? null, function (Builder $query, string $date) {
                $query->whereDate('failed_at', '>=', $date);
            })
            ->when($filters['failed_to'] ?? null, function (Builder $query, string $date) {
                $query->whereDate('failed_at', '<=', $date);
            });
    }

    public function getPayloadPreview(): string
    {
        return Str::limit($this->payload, config('failed-jobs-manager.ui.payload_preview_length'));
    }

    public function getExceptionPreview(): string
    {
        return Str::limit($this->exception, config('failed-jobs-manager.ui.exception_preview_length'));
    }
}
