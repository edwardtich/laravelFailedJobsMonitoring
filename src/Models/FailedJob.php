<?php

namespace EdwardTich\FailedJobsManager\Models;

use EdwardTich\FailedJobsManager\Support\FailedJobException;
use EdwardTich\FailedJobsManager\Support\FailedJobPayload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string|null $uuid
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
    protected $guarded = ['*'];

    public $timestamps = false;

    private ?FailedJobPayload $payloadDetails = null;

    private ?FailedJobException $exceptionDetails = null;

    /**
     * @var array<string, bool>
     */
    private static array $uuidColumns = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('failed-jobs-manager.table') ?: config('queue.failed.table', 'failed_jobs'));

        if ($connection = config('failed-jobs-manager.connection') ?: config('queue.failed.database')) {
            $this->setConnection($connection);
        }
    }

    protected function casts(): array
    {
        return [
            'failed_at' => 'datetime',
        ];
    }

    public function scopeOrdered(Builder $query): void
    {
        // The auto-increment key follows insertion (= failure) order and is indexed, unlike failed_at.
        $query->orderByDesc($this->getKeyName());
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function scopeFilter(Builder $query, array $filters, bool $searchPayload = false): void
    {
        $query
            ->when($filters['queue'] ?? null, function (Builder $query, string $queue) {
                $query->where('queue', $queue);
            })
            ->when($filters['search'] ?? null, function (Builder $query, string $search) use ($searchPayload) {
                $search = trim(Str::limit($search, (int) config('failed-jobs-manager.ui.search_max_length', 120), ''));

                if ($search === '') {
                    return;
                }

                $pattern = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search) . '%';
                $grammar = $query->getQuery()->getGrammar();

                $query->where(function (Builder $query) use ($search, $pattern, $grammar, $searchPayload) {
                    $query->whereRaw($grammar->wrap('exception') . " like ? escape '!'", [$pattern]);

                    if ($this->hasUuidColumn()) {
                        $query->orWhere('uuid', $search);
                    } elseif (Str::isUuid($search)) {
                        // Strict UUID format keeps this an exact match, not a way to probe hidden payload content.
                        $query->orWhere('payload', 'like', '%"uuid":"' . strtolower($search) . '"%');
                    }

                    if ($searchPayload) {
                        $query->orWhereRaw($grammar->wrap('payload') . " like ? escape '!'", [$pattern]);
                    }
                });
            })
            ->when($filters['failed_from'] ?? null, function (Builder $query, string $date) {
                $query->where('failed_at', '>=', Carbon::createFromFormat('!Y-m-d', $date));
            })
            ->when($filters['failed_to'] ?? null, function (Builder $query, string $date) {
                $query->where('failed_at', '<', Carbon::createFromFormat('!Y-m-d', $date)->addDay());
            });
    }

    public function getPayloadPreview(): string
    {
        return $this->payloadDetails()->preview((int) config('failed-jobs-manager.ui.payload_preview_length', 300));
    }

    public function getExceptionPreview(): string
    {
        return $this->exceptionDetails()->preview((int) config('failed-jobs-manager.ui.exception_preview_length', 500));
    }

    public function payloadDetails(): FailedJobPayload
    {
        return $this->payloadDetails ??= FailedJobPayload::from((string) $this->payload);
    }

    public function exceptionDetails(): FailedJobException
    {
        return $this->exceptionDetails ??= FailedJobException::from((string) $this->exception);
    }

    /**
     * Legacy failed_jobs tables (created before Laravel 8) have no uuid column.
     */
    public function hasUuidColumn(): bool
    {
        $key = $this->getConnectionName() . ':' . $this->getTable();

        return self::$uuidColumns[$key] ??= $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'uuid');
    }

    /**
     * The uuid column when it exists, otherwise the uuid stored in the payload (they are equal for database-uuids).
     */
    public function uuidValue(): ?string
    {
        $uuid = $this->getAttributes()['uuid'] ?? null;

        return filled($uuid) ? (string) $uuid : $this->payloadDetails()->uuid();
    }

    public function retryKey(): int|string
    {
        $strategy = config('failed-jobs-manager.ui.retry_key', 'auto');

        if ($strategy === 'auto') {
            // Only the "database-uuids" failer looks jobs up by uuid; "database" or a missing driver use id.
            $strategy = config('queue.failed.driver') === 'database-uuids' ? 'uuid' : 'id';
        }

        $uuid = $this->getAttributes()['uuid'] ?? null;

        return $strategy === 'uuid' && filled($uuid) ? (string) $uuid : $this->id;
    }
}
