<?php

use EdwardTich\FailedJobsManager\Authorization\FailedJobsManagerAuthorization;
use EdwardTich\FailedJobsManager\Controllers\DeleteFailedJobController;
use EdwardTich\FailedJobsManager\Controllers\RetryFailedJobController;
use EdwardTich\FailedJobsManager\Controllers\RetryFailedJobsController;
use EdwardTich\FailedJobsManager\Controllers\ShowFailedJobController;
use EdwardTich\FailedJobsManager\Controllers\ShowFailedJobsController;
use EdwardTich\FailedJobsManager\Http\Middleware\AuthorizeFailedJobsManager;
use Illuminate\Support\Facades\Route;

Route::get('', ShowFailedJobsController::class)
    ->middleware(AuthorizeFailedJobsManager::class . ':' . FailedJobsManagerAuthorization::VIEW)
    ->name('failed-jobs-manager::index');

Route::patch('jobs/retry/bulk', RetryFailedJobsController::class)
    ->middleware(AuthorizeFailedJobsManager::class . ':' . FailedJobsManagerAuthorization::RETRY)
    ->name('failed-jobs-manager::retry-bulk');

Route::get('jobs/{failedJob}', ShowFailedJobController::class)
    ->whereNumber('failedJob')
    ->middleware(AuthorizeFailedJobsManager::class . ':' . FailedJobsManagerAuthorization::VIEW)
    ->name('failed-jobs-manager::show');

Route::patch('jobs/{failedJob}/retry', RetryFailedJobController::class)
    ->whereNumber('failedJob')
    ->middleware(AuthorizeFailedJobsManager::class . ':' . FailedJobsManagerAuthorization::RETRY)
    ->name('failed-jobs-manager::retry');

Route::delete('jobs/{failedJob}', DeleteFailedJobController::class)
    ->whereNumber('failedJob')
    ->middleware(AuthorizeFailedJobsManager::class . ':' . FailedJobsManagerAuthorization::DELETE)
    ->name('failed-jobs-manager::destroy');
