<?php

namespace EdwardTich\FailedJobsManager\Controllers;

use EdwardTich\FailedJobsManager\Models\FailedJob;
use EdwardTich\FailedJobsManager\Services\FailedJobRetryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RetryFailedJobsController
{
    public function __invoke(Request $request, FailedJobRetryService $retryService): RedirectResponse
    {
        abort_unless(config('failed-jobs-manager.ui.allow_retry'), 403);

        $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],
            'ids.*' => [
                'integer',
                Rule::exists(config('failed-jobs-manager.table'), 'id'),
            ],
        ]);

        $ids = FailedJob::query()
            ->whereIn('id', $request->input('ids'))
            ->pluck('id')
            ->all();

        $retryService->retry($ids);

        return redirect()
            ->route('failed-jobs-manager::index')
            ->with('success', 'Задачи отправлены на перезапуск');
    }
}
