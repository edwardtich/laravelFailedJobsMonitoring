<?php

namespace EdwardTich\FailedJobsManager\Authorization;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

class FailedJobsManagerAuthorization
{
    public const string GATE = 'viewFailedJobsManager';

    public const string VIEW = 'view';

    public const string RETRY = 'retry';

    public const string DELETE = 'delete';

    public const string VIEW_SENSITIVE_PAYLOAD = 'view_sensitive_payload';

    private const array ABILITIES = [
        self::VIEW,
        self::RETRY,
        self::DELETE,
        self::VIEW_SENSITIVE_PAYLOAD,
    ];

    public function authorize(?Authenticatable $user, string $ability): void
    {
        abort_unless($this->can($user, $ability), 403);
    }

    /**
     * Checks an operation for a user who already passed page access (Filament page or standalone gate).
     */
    public function can(?Authenticatable $user, string $ability): bool
    {
        if (! in_array($ability, self::ABILITIES, true)) {
            return false;
        }

        if ($ability !== self::VIEW && ! $this->can($user, self::VIEW)) {
            return false;
        }

        $permission = $this->permission($ability);

        if ($permission === null) {
            return $ability !== self::VIEW_SENSITIVE_PAYLOAD;
        }

        return $user !== null && Gate::forUser($user)->allows($permission);
    }

    /**
     * Same as can(), but also respects the ui.allow_retry / ui.allow_delete switches.
     */
    public function canOperate(?Authenticatable $user, string $ability): bool
    {
        $enabled = match ($ability) {
            self::RETRY => (bool) config('failed-jobs-manager.ui.allow_retry'),
            self::DELETE => (bool) config('failed-jobs-manager.ui.allow_delete'),
            default => true,
        };

        return $enabled && $this->can($user, $ability);
    }

    public function canUseStandalone(?Authenticatable $user, string $ability): bool
    {
        return $this->hasStandaloneAccess($user) && $this->can($user, $ability);
    }

    private function hasStandaloneAccess(?Authenticatable $user): bool
    {
        if ($this->permission(self::VIEW) !== null) {
            // The configured view permission is enforced by can().
            return true;
        }

        $gate = config('failed-jobs-manager.authorization.gate', self::GATE);

        if (is_string($gate) && $gate !== '' && Gate::has($gate)) {
            return Gate::forUser($user)->allows($gate);
        }

        return app()->environment('local');
    }

    private function permission(string $ability): ?string
    {
        $permission = config('failed-jobs-manager.authorization.permissions.' . $ability);

        return is_string($permission) && trim($permission) !== '' ? trim($permission) : null;
    }
}
