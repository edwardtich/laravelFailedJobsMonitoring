<?php

use EdwardTich\FailedJobsManager\Controllers\DeleteFailedJobController;
use EdwardTich\FailedJobsManager\Controllers\RetryFailedJobController;
use EdwardTich\FailedJobsManager\Controllers\RetryFailedJobsController;
use EdwardTich\FailedJobsManager\Controllers\ShowFailedJobController;
use EdwardTich\FailedJobsManager\Controllers\ShowFailedJobsController;
use Illuminate\Support\Facades\Route;

Route::get('', ShowFailedJobsController::class)->name('failed-jobs-manager::index');

Route::patch('jobs/retry/bulk', RetryFailedJobsController::class)->name('failed-jobs-manager::retry-bulk');

Route::get('jobs/{failedJob}', ShowFailedJobController::class)->name('failed-jobs-manager::show');

Route::patch('jobs/{failedJob}/retry', RetryFailedJobController::class)->name('failed-jobs-manager::retry');

Route::delete('jobs/{failedJob}', DeleteFailedJobController::class)->name('failed-jobs-manager::destroy');
