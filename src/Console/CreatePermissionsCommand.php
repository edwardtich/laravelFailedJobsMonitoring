<?php

namespace EdwardTich\FailedJobsManager\Console;

use EdwardTich\FailedJobsManager\Authorization\FailedJobsManagerAuthorization;
use Illuminate\Console\Command;

class CreatePermissionsCommand extends Command
{
    protected $signature = 'failed-jobs-manager:permissions {--guard= : Guard name, defaults to auth.defaults.guard}';

    protected $description = 'Create the configured failed jobs manager permissions with spatie/laravel-permission';

    private const string REGISTRAR = '\\Spatie\\Permission\\PermissionRegistrar';

    public function handle(FailedJobsManagerAuthorization $authorization): int
    {
        if (! class_exists(self::REGISTRAR)) {
            $this->error('spatie/laravel-permission is not installed. Create the permissions in your application instead.');

            return self::FAILURE;
        }

        $permissions = array_values($authorization->configuredPermissions());
        $filamentPermission = config('failed-jobs-manager.filament.permission');

        if (is_string($filamentPermission) && trim($filamentPermission) !== '') {
            $permissions[] = trim($filamentPermission);
        }

        $permissions = array_values(array_unique($permissions));

        if ($permissions === []) {
            $this->warn('No permissions are configured in failed-jobs-manager.authorization.permissions.');

            return self::SUCCESS;
        }

        $guard = $this->option('guard') ?: config('auth.defaults.guard');
        $registrar = app(self::REGISTRAR);
        $permissionClass = $registrar->getPermissionClass();

        foreach ($permissions as $permission) {
            $permissionClass::findOrCreate($permission, $guard);
            $this->line("  {$permission} ({$guard})");
        }

        $registrar->forgetCachedPermissions();

        $this->info('Permissions are ready. Assign them to roles or users in your application.');

        return self::SUCCESS;
    }
}
