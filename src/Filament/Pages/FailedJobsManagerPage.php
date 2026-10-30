<?php

namespace EdwardTich\FailedJobsManager\Filament\Pages;

use EdwardTich\FailedJobsManager\Authorization\FailedJobsManagerAuthorization;
use EdwardTich\FailedJobsManager\Models\FailedJob;
use EdwardTich\FailedJobsManager\Queries\FailedJobQuery;
use EdwardTich\FailedJobsManager\Services\FailedJobDeleteService;
use EdwardTich\FailedJobsManager\Services\FailedJobsManagerAuditDispatcher;
use EdwardTich\FailedJobsManager\Services\FailedJobRetryService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\WithPagination;

class FailedJobsManagerPage extends Page
{
    use WithPagination;

    protected static ?string $slug = 'failed-jobs-manager';

    public ?string $queue = null;

    public ?string $search = null;

    public ?string $failedFrom = null;

    public ?string $failedTo = null;

    /**
     * @var array<int, int|string>
     */
    public array $selectedJobs = [];

    public static function getNavigationLabel(): string
    {
        return __('failed-jobs-manager::failed-jobs-manager.pages.index_title');
    }

    public static function getCluster(): ?string
    {
        return config('failed-jobs-manager.filament.cluster');
    }

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-queue-list';
    }

    public function getView(): string
    {
        return 'failed-jobs-manager::filament.pages.failed-jobs-manager';
    }

    public function getTitle(): string
    {
        return __('failed-jobs-manager::failed-jobs-manager.pages.index_title');
    }

    public function getSubheading(): ?string
    {
        return __('failed-jobs-manager::failed-jobs-manager.pages.index_subtitle');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('failed-jobs-manager.filament.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return config('failed-jobs-manager.filament.navigation_sort');
    }

    public static function getNavigationParentItem(): ?string
    {
        return config('failed-jobs-manager.filament.navigation_parent_item');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) config('failed-jobs-manager.filament.register_navigation', true);
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()?->user();

        if (! $user) {
            return false;
        }

        $permission = static::getConfiguredPermission();

        // A configured permission is authoritative: roles are only a fallback for projects without permissions.
        if ($permission) {
            return $user->can($permission) && static::canPerform($user, FailedJobsManagerAuthorization::VIEW);
        }

        if (app(FailedJobsManagerAuthorization::class)->isConfigured(FailedJobsManagerAuthorization::VIEW)) {
            return static::canPerform($user, FailedJobsManagerAuthorization::VIEW);
        }

        $roles = array_filter((array) config('failed-jobs-manager.filament.roles', ['super_admin']));

        if ($roles === []) {
            return static::canPerform($user, FailedJobsManagerAuthorization::VIEW);
        }

        if (method_exists($user, 'hasAnyRole')) {
            return (bool) $user->hasAnyRole($roles)
                && static::canPerform($user, FailedJobsManagerAuthorization::VIEW);
        }

        if (method_exists($user, 'hasRole')) {
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    return static::canPerform($user, FailedJobsManagerAuthorization::VIEW);
                }
            }
        }

        return false;
    }

    public function updatedQueue(): void
    {
        $this->selectedJobs = [];
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->selectedJobs = [];
        $this->resetPage();
    }

    public function updatedFailedFrom(): void
    {
        $this->selectedJobs = [];
        $this->resetPage();
    }

    public function updatedFailedTo(): void
    {
        $this->selectedJobs = [];
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->queue = null;
        $this->search = null;
        $this->failedFrom = null;
        $this->failedTo = null;
        $this->selectedJobs = [];
        $this->resetPage();
    }

    public function retry(int $id): void
    {
        abort_unless(config('failed-jobs-manager.ui.allow_retry'), 403);
        $this->authorizeAbility(FailedJobsManagerAuthorization::RETRY);

        $failedJob = FailedJob::query()->findOrFail($id);

        $retryService = app(FailedJobRetryService::class);
        $result = $retryService->retryByFailedJobIdsWithResult([$failedJob->id]);

        app(FailedJobsManagerAuditDispatcher::class)->retried(
            $result->retriedFailedJobIds(),
            Filament::auth()?->user(),
            request()->ip(),
        );

        $this->selectedJobs = array_values(array_diff($this->selectedJobs, [$id]));

        if (! $result->isSuccessful()) {
            Notification::make()
                ->title(__('failed-jobs-manager::failed-jobs-manager.flash.retry_failed'))
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('failed-jobs-manager::failed-jobs-manager.flash.retry_sent'))
            ->success()
            ->send();
    }

    public function retrySelected(): void
    {
        abort_unless(config('failed-jobs-manager.ui.allow_retry'), 403);
        $this->authorizeAbility(FailedJobsManagerAuthorization::RETRY);

        $selectedJobs = array_values(array_unique(array_map('intval', $this->selectedJobs)));
        $maxIds = max(1, (int) config('failed-jobs-manager.ui.retry_max_ids', 50));

        if ($selectedJobs === []) {
            return;
        }

        // Refuse instead of silently retrying only the first $maxIds jobs.
        if (count($selectedJobs) > $maxIds) {
            Notification::make()
                ->title(__('failed-jobs-manager::failed-jobs-manager.flash.bulk_retry_too_many', ['max' => $maxIds]))
                ->danger()
                ->send();

            return;
        }

        $retryService = app(FailedJobRetryService::class);
        $result = $retryService->retryByFailedJobIdsWithResult($selectedJobs);

        app(FailedJobsManagerAuditDispatcher::class)->retried(
            $result->retriedFailedJobIds(),
            Filament::auth()?->user(),
            request()->ip(),
        );

        $this->selectedJobs = [];

        if ($result->isPartiallySuccessful()) {
            Notification::make()
                ->title(__('failed-jobs-manager::failed-jobs-manager.flash.bulk_retry_partially_sent'))
                ->warning()
                ->send();

            return;
        }

        if (! $result->isSuccessful()) {
            Notification::make()
                ->title(__('failed-jobs-manager::failed-jobs-manager.flash.retry_failed'))
                ->danger()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('failed-jobs-manager::failed-jobs-manager.flash.bulk_retry_sent'))
            ->success()
            ->send();
    }

    public function toggleCurrentPageSelection(): void
    {
        abort_unless(config('failed-jobs-manager.ui.allow_retry'), 403);
        $this->authorizeAbility(FailedJobsManagerAuthorization::RETRY);

        $currentPageJobIds = $this->currentPageJobIds();

        if ($currentPageJobIds === []) {
            return;
        }

        $selectedJobs = array_values(array_unique(array_map('intval', $this->selectedJobs)));

        if (array_diff($currentPageJobIds, $selectedJobs) === []) {
            $this->selectedJobs = array_values(array_diff($selectedJobs, $currentPageJobIds));

            return;
        }

        $this->selectedJobs = array_values(array_unique(array_merge($selectedJobs, $currentPageJobIds)));
    }

    public function delete(int $id): void
    {
        abort_unless(config('failed-jobs-manager.ui.allow_delete'), 403);
        $this->authorizeAbility(FailedJobsManagerAuthorization::DELETE);

        $failedJob = FailedJob::query()->findOrFail($id);

        $deleteService = app(FailedJobDeleteService::class);
        $result = $deleteService->delete($failedJob);

        $this->selectedJobs = array_values(array_diff($this->selectedJobs, [$id]));

        if (! $result) {
            Notification::make()
                ->title(__('failed-jobs-manager::failed-jobs-manager.flash.delete_failed'))
                ->danger()
                ->send();

            return;
        }

        app(FailedJobsManagerAuditDispatcher::class)->deleted(
            $failedJob,
            Filament::auth()?->user(),
            request()->ip(),
        );

        Notification::make()
            ->title(__('failed-jobs-manager::failed-jobs-manager.flash.deleted'))
            ->success()
            ->send();
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $filters = $this->filters();
        $failedJobQuery = app(FailedJobQuery::class);
        $authorization = app(FailedJobsManagerAuthorization::class);
        $user = Filament::auth()?->user();
        $isFilterApplied = array_filter($filters) !== [];
        $canViewUnmasked = $authorization->canViewUnmasked($user);
        $jobs = $failedJobQuery->list($filters, $canViewUnmasked)->paginate(
            perPage: $this->perPage(),
            pageName: 'page',
            page: $this->getPage()
        );
        $currentPageJobIds = $jobs->getCollection()->pluck('id')->map(static fn ($id) => (int) $id)->all();
        $selectedJobs = array_values(array_unique(array_map('intval', $this->selectedJobs)));

        return [
            'jobs' => $jobs,
            'queues' => $failedJobQuery->queueOptions(),
            'filteredJobsCount' => $isFilterApplied ? $jobs->total() : null,
            'isFilterApplied' => $isFilterApplied,
            'canRetry' => $authorization->canOperate($user, FailedJobsManagerAuthorization::RETRY),
            'canDelete' => $authorization->canOperate($user, FailedJobsManagerAuthorization::DELETE),
            'canViewUnmasked' => $canViewUnmasked,
            'allCurrentPageJobsSelected' => $currentPageJobIds !== [] && array_diff($currentPageJobIds, $selectedJobs) === [],
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function filters(): array
    {
        $failedFrom = $this->dateFilter($this->failedFrom);
        $failedTo = $this->dateFilter($this->failedTo);

        if ($failedFrom && $failedTo && $failedFrom > $failedTo) {
            $failedTo = null;
        }

        return [
            'queue' => $this->stringFilter($this->queue, 255),
            'search' => $this->stringFilter($this->search, (int) config('failed-jobs-manager.ui.search_max_length', 120)),
            'failed_from' => $failedFrom,
            'failed_to' => $failedTo,
        ];
    }

    private function stringFilter(?string $value, int $limit): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return Str::limit($value, $limit, '');
    }

    private function dateFilter(?string $value): ?string
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $value);

            return $date && $date->format('Y-m-d') === $value ? $value : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private static function getConfiguredPermission(): ?string
    {
        $permission = config('failed-jobs-manager.filament.permission');

        if ($permission) {
            return $permission;
        }

        $filamentShield = '\\BezhanSalleh\\FilamentShield\\Facades\\FilamentShield';

        if (! class_exists($filamentShield)) {
            return null;
        }

        $page = $filamentShield::getPages()[static::class] ?? null;

        return $page ? array_key_first($page['permissions']) : null;
    }

    private static function canPerform(?Authenticatable $user, string $ability): bool
    {
        return app(FailedJobsManagerAuthorization::class)->can($user, $ability);
    }

    private function authorizeAbility(string $ability): void
    {
        abort_unless(static::canAccess(), 403);

        app(FailedJobsManagerAuthorization::class)->authorize(Filament::auth()?->user(), $ability);
    }

    private function canSearchPayload(): bool
    {
        return app(FailedJobsManagerAuthorization::class)->canViewUnmasked(Filament::auth()?->user());
    }

    private function perPage(): int
    {
        return max(1, (int) config('failed-jobs-manager.ui.per_page', 20));
    }

    /**
     * @return array<int, int>
     */
    private function currentPageJobIds(): array
    {
        return app(FailedJobQuery::class)
            ->list($this->filters(), $this->canSearchPayload())
            ->forPage($this->getPage(), $this->perPage())
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->all();
    }
}
