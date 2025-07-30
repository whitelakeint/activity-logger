<?php

use Illuminate\Support\Facades\Route;
use ActivityLogger\Http\Controllers\ActivityLogController;
use ActivityLogger\Http\Controllers\ActivityLogDashboardController;
use ActivityLogger\Http\Controllers\ActivityLogReportController;
use ActivityLogger\Http\Controllers\ActivityLoggerWebController;

Route::prefix('activity-logger')->name('activity-logger.')->group(function () {
    
    // Web Dashboard Routes (Primary Interface)
    Route::get('/', [ActivityLoggerWebController::class, 'index'])->name('dashboard');
    Route::get('/logs', [ActivityLoggerWebController::class, 'logs'])->name('logs');
    Route::get('/logs/{id}', [ActivityLoggerWebController::class, 'showLog'])->name('logs.show');
    Route::get('/performance', [ActivityLoggerWebController::class, 'performance'])->name('performance');
    Route::get('/errors', [ActivityLoggerWebController::class, 'errors'])->name('errors');
    Route::get('/traffic', [ActivityLoggerWebController::class, 'traffic'])->name('traffic');
    Route::get('/users', [ActivityLoggerWebController::class, 'users'])->name('users');
    Route::get('/users/{id}', [ActivityLoggerWebController::class, 'showUser'])->name('users.show');
    Route::get('/reports', [ActivityLoggerWebController::class, 'reports'])->name('reports');
    
    // AJAX API Routes for Dashboard
    Route::prefix('api')->group(function () {
        Route::get('realtime-stats', [ActivityLoggerWebController::class, 'realtimeStats'])->name('api.realtime');
        Route::get('chart-data', [ActivityLoggerWebController::class, 'chartData'])->name('api.charts');
        Route::post('export', [ActivityLoggerWebController::class, 'export'])->name('api.export');
        
        // Legacy API Routes for Activity Logs
        Route::get('logs', [ActivityLogController::class, 'index'])->name('api.logs.index');
        Route::get('logs/{id}', [ActivityLogController::class, 'show'])->name('api.logs.show');
        Route::get('statistics', [ActivityLogController::class, 'statistics'])->name('api.statistics');
        Route::get('export-legacy', [ActivityLogController::class, 'export'])->name('api.export.legacy');
    });
    
    // Dashboard JSON API Routes (for existing controllers)
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [ActivityLogDashboardController::class, 'dashboard'])->name('dashboard.json');
        Route::get('realtime', [ActivityLogDashboardController::class, 'realtimeStats'])->name('dashboard.realtime');
        Route::get('performance', [ActivityLogDashboardController::class, 'performanceReport'])->name('dashboard.performance.json');
        Route::get('errors', [ActivityLogDashboardController::class, 'errorReport'])->name('dashboard.errors.json');
        Route::get('traffic', [ActivityLogDashboardController::class, 'trafficReport'])->name('dashboard.traffic.json');
        Route::get('users/{userId}', [ActivityLogDashboardController::class, 'userActivity'])->name('dashboard.user.json');
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