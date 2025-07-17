<?php

use Illuminate\Support\Facades\Route;
use ActivityLogger\Http\Controllers\ActivityLogController;
use ActivityLogger\Http\Controllers\ActivityLogDashboardController;
use ActivityLogger\Http\Controllers\ActivityLogReportController;

Route::prefix('activity-logger')->name('activity-logger.')->group(function () {
    
    // API Routes for Activity Logs
    Route::prefix('api')->group(function () {
        Route::get('logs', [ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/{id}', [ActivityLogController::class, 'show'])->name('logs.show');
        Route::get('statistics', [ActivityLogController::class, 'statistics'])->name('statistics');
        Route::get('export', [ActivityLogController::class, 'export'])->name('export');
    });
    
    // Dashboard Routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [ActivityLogDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('realtime', [ActivityLogDashboardController::class, 'realtimeStats'])->name('dashboard.realtime');
        Route::get('performance', [ActivityLogDashboardController::class, 'performanceReport'])->name('dashboard.performance');
        Route::get('errors', [ActivityLogDashboardController::class, 'errorReport'])->name('dashboard.errors');
        Route::get('traffic', [ActivityLogDashboardController::class, 'trafficReport'])->name('dashboard.traffic');
        Route::get('users/{userId}', [ActivityLogDashboardController::class, 'userActivity'])->name('dashboard.user');
    });
    
    // Reports Routes
    Route::prefix('reports')->group(function () {
        Route::get('daily', [ActivityLogReportController::class, 'dailyReport'])->name('reports.daily');
        Route::get('weekly', [ActivityLogReportController::class, 'weeklyReport'])->name('reports.weekly');
        Route::get('monthly', [ActivityLogReportController::class, 'monthlyReport'])->name('reports.monthly');
        Route::get('custom', [ActivityLogReportController::class, 'customReport'])->name('reports.custom');
        Route::get('export', [ActivityLogReportController::class, 'exportReport'])->name('reports.export');
    });
});

// Example usage routes (these would typically be in your main application)
Route::get('/activity-logger/demo', function () {
    return response()->json([
        'message' => 'Activity Logger Demo',
        'endpoints' => [
            'logs' => '/activity-logger/api/logs',
            'statistics' => '/activity-logger/api/statistics',
            'dashboard' => '/activity-logger/dashboard',
            'reports' => '/activity-logger/reports/daily',
        ],
    ]);
})->name('activity-logger.demo');