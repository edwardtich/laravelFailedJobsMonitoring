<?php

namespace EdwardTich\FailedJobsManager\Filament\Pages;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use EdwardTich\FailedJobsManager\Queries\FailedJobQuery;
use EdwardTich\FailedJobsManager\Services\FailedJobDeleteService;
use EdwardTich\FailedJobsManager\Services\FailedJobRetryService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
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

    public static function registerNavigationItems(): void
    {
        if (! static::shouldRegisterNavigation() || ! static::canAccess()) {
            return;
        }

        $panel = Filament::getCurrentPanel() ?? Filament::getDefaultPanel();

        $panel?->navigationItems(static::getNavigationItems());
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()?->user();

        if (! $user) {
            return false;
        }

        $permission = static::getConfiguredPermission();

        if ($permission && $user->can($permission)) {
            return true;
        }

        $roles = array_filter((array) config('failed-jobs-manager.filament.roles', ['super_admin']));

        if ($roles === []) {
            return true;
        }

        if (method_exists($user, 'hasAnyRole')) {
            return (bool) $user->hasAnyRole($roles);
        }

        if (method_exists($user, 'hasRole')) {
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    return true;
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

        $failedJob = FailedJob::query()->findOrFail($id);

        $retryService = app(FailedJobRetryService::class);
        $result = $retryService->retryByFailedJobIds([$failedJob->id]);

        $this->selectedJobs = array_values(array_diff($this->selectedJobs, [$id]));

        if (! $result) {
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

        $selectedJobs = array_values(array_unique(array_map('intval', $this->selectedJobs)));

        if ($selectedJobs === []) {
            return;
        }

        $retryService = app(FailedJobRetryService::class);
        $result = $retryService->retryByFailedJobIds($selectedJobs);

        $this->selectedJobs = [];

        if (! $result) {
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
        $query = app(FailedJobQuery::class)->list($filters);
        $isFilterApplied = collect($filters)->filter(static fn ($value) => $value !== null && $value !== '')->isNotEmpty();
        $jobs = $query->paginate(
            perPage: (int) config('failed-jobs-manager.ui.per_page'),
            pageName: 'page',
            page: $this->getPage()
        );
        $currentPageJobIds = $jobs->getCollection()->pluck('id')->map(static fn ($id) => (int) $id)->all();
        $selectedJobs = array_values(array_unique(array_map('intval', $this->selectedJobs)));

        return [
            'jobs' => $jobs,
            'queues' => app(FailedJobQuery::class)->queues()->pluck('queue', 'queue')->toArray(),
            'filteredJobsCount' => $isFilterApplied ? (clone $query)->count() : null,
            'isFilterApplied' => $isFilterApplied,
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

    /**
     * @return array<int, int>
     */
    private function currentPageJobIds(): array
    {
        return app(FailedJobQuery::class)
            ->list($this->filters())
            ->paginate(
                perPage: (int) config('failed-jobs-manager.ui.per_page'),
                pageName: 'page',
                page: $this->getPage()
            )
            ->getCollection()
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->all();
    }
}
