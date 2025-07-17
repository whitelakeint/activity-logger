# Laravel Activity Logger

A comprehensive activity logging package for Laravel applications that tracks all HTTP requests and responses with detailed information.

## Features

- **Complete Request/Response Tracking**: Logs all HTTP requests with headers, parameters, body, and response data
- **User Activity Monitoring**: Track user actions with IP addresses, user agents, and session information
- **Performance Metrics**: Monitor response time, memory usage, query count, and query execution time
- **Error Tracking**: Capture and log application errors with stack traces
- **Browser & Device Detection**: Identify browsers, platforms, and devices
- **Geographical Information**: Track country, city, and timezone
- **Request Tracking**: Track controller actions, middleware, and request IDs for better debugging
- **Advanced Search & Filtering**: Search logs by date, user, URL, method, response code, controller, location, and more
- **Export Functionality**: Export logs in JSON, CSV, or XML formats
- **Console Commands**: Clean old logs, analyze statistics, and export data
- **Customizable**: Extensive configuration options for what to log and what to skip

## Installation

### Option 1: Install from Git Repository

1. Add the repository to your Laravel project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/whitelakeint/activity-logger.git"
        }
    ],
    "require": {
        "whitelakeint/activity-logger": "dev-main"
    }
}
```

2. Install the package:

```bash
composer install
```

### Option 2: Install from Packagist (when published)

```bash
composer require whitelakeint/activity-logger
```

### Option 3: Install from Local Path (Development)

1. Add to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/activity-logger"
        }
    ],
    "require": {
        "whitelakeint/activity-logger": "@dev"
    }
}
```

2. Install the package:

```bash
composer install
```

### Post-Installation Steps

1. Publish the configuration file:

```bash
php artisan vendor:publish --tag=activity-logger-config
```

2. Optionally publish the migrations (if you want to customize them):

```bash
php artisan vendor:publish --tag=activity-logger-migrations
```

3. Run the migrations:

```bash
php artisan migrate
```

4. (Optional) Clear config cache:

```bash
php artisan config:clear
```

## Configuration

### Basic Setup

Add the middleware to your routes in `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \ActivityLogger\Middleware\ActivityLoggerMiddleware::class,
    ],
    
    'api' => [
        // ... other middleware
        \ActivityLogger\Middleware\ActivityLoggerMiddleware::class,
    ],
];
```

Or use it selectively on specific routes:

```php
Route::middleware(['activity-logger'])->group(function () {
    // Your routes here
});
```

### Configuration Options

Edit `config/activity-logger.php` to customize the package:

```php
return [
    'enabled' => true,
    'auto_register_middleware' => false,
    'log_request_headers' => true,
    'log_request_body' => true,
    'log_response_body' => false,
    'max_body_size' => 10000,
    'skip_urls' => ['telescope/*', 'horizon/*'],
    'sensitive_fields' => ['password', 'credit_card'],
    // ... more options
];
```

## Usage

### Using the Facade

```php
use ActivityLogger\Facades\ActivityLogger;

// Search logs (request_date is used internally for efficient filtering)
$logs = ActivityLogger::search([
    'user_id' => 123,
    'start_date' => '2025-01-01',
    'end_date' => '2025-01-31',
    'method' => 'POST',
]);

// Quick search across multiple fields
$quickResults = ActivityLogger::quickSearch('user login', [
    'date' => '2025-01-17',
    'user_id' => 123,
]);

// Get recent errors
$errors = ActivityLogger::getRecentErrors(60); // Last 60 minutes

// Get slow requests
$slowRequests = ActivityLogger::getSlowRequests(1000); // > 1 second

// Get high memory usage requests
$highMemory = ActivityLogger::getHighMemoryRequests(52428800); // > 50MB

// Get requests with many database queries
$highQueries = ActivityLogger::getHighQueryCountRequests(50); // > 50 queries

// Get slow database query requests
$slowQueries = ActivityLogger::getSlowQueryRequests(1000); // > 1 second

// Search by controller
$controllerLogs = ActivityLogger::searchByController('UserController@index');

// Search by geographical location
$countryLogs = ActivityLogger::searchByCountry('US');
$cityLogs = ActivityLogger::searchByCity('New York');

// Search by request ID (useful for tracing)
$requestLogs = ActivityLogger::searchByRequestId('req_123456');

// Get statistics
$stats = ActivityLogger::getStatistics([
    'start_date' => '2025-01-01',
]);

// Export logs
$jsonData = ActivityLogger::export(['user_id' => 123], 'json');
$csvData = ActivityLogger::export(['method' => 'POST'], 'csv');
```

### Console Commands

```bash
# Clear old logs (older than 90 days by default)
php artisan activity-logger:clear --days=30

# Export logs with additional filters
php artisan activity-logger:export --format=csv --start=2025-01-01 --controller=UserController --country=US --output=logs.csv

# Analyze logs
php artisan activity-logger:analyze --start=2025-01-01 --detailed
```

### API Endpoints

The package provides RESTful API endpoints for accessing logs:

```bash
# Get logs with filters
GET /activity-logger/api/logs?start_date=2025-01-01&method=POST&user_id=123

# Get specific log
GET /activity-logger/api/logs/123

# Get statistics
GET /activity-logger/api/statistics?start_date=2025-01-01&end_date=2025-01-31

# Export logs
GET /activity-logger/api/export?format=csv&start_date=2025-01-01
```

### Dashboard Routes

```bash
# Main dashboard
GET /activity-logger/dashboard

# Real-time statistics
GET /activity-logger/dashboard/realtime

# Performance report
GET /activity-logger/dashboard/performance

# Error report
GET /activity-logger/dashboard/errors

# Traffic report
GET /activity-logger/dashboard/traffic

# User activity
GET /activity-logger/dashboard/users/{userId}
```

### Reports

```bash
# Daily report
GET /activity-logger/reports/daily?date=2025-01-17

# Weekly report
GET /activity-logger/reports/weekly?end_date=2025-01-17

# Monthly report
GET /activity-logger/reports/monthly?month=2025-01

# Custom report
GET /activity-logger/reports/custom?start_date=2025-01-01&end_date=2025-01-31
```

### Model Scopes

```php
use ActivityLogger\Models\ActivityLog;

// Get logs for a specific user
$userLogs = ActivityLog::forUser(123)->get();

// Get logs for a date range
$dateLogs = ActivityLog::forDateRange('2025-01-01', '2025-01-31')->get();

// Get failed requests
$failures = ActivityLog::failed()->get();

// Get slow requests
$slow = ActivityLog::slowRequests(1000)->get();

// Chain multiple scopes
$logs = ActivityLog::forUser(123)
    ->forMethod('POST')
    ->successful()
    ->forDateRange('2025-01-01', '2025-01-31')
    ->forCountry('US')
    ->highQueryCount(50)
    ->get();

// Search by controller action
$controllerLogs = ActivityLog::where('controller_action', 'LIKE', '%UserController%')->get();

// Search by request ID
$requestLogs = ActivityLog::forRequestId('req_123456')->get();

// Find slow database queries
$slowQueries = ActivityLog::slowQueryTime(1000)->get();
```

## Advanced Features

### Custom Data

You can add custom data to logs by extending the middleware:

```php
class CustomActivityLogger extends ActivityLoggerMiddleware
{
    protected function getCustomData($request, $response, $exception)
    {
        return [
            'tenant_id' => tenant()->id,
            'api_version' => $request->header('X-API-Version'),
            // Add any custom data
        ];
    }
}
```

### Scheduled Cleanup

Add to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('activity-logger:clear --days=90')->daily();
}
```

### Performance Monitoring

Monitor your application's performance:

```php
// Get requests with high memory usage (in bytes)
$highMemory = ActivityLogger::getHighMemoryRequests(52428800); // > 50MB

// Get requests with many database queries
$highQueries = ActivityLogger::getHighQueryCountRequests(50);

// Get requests with slow database queries
$slowQueries = ActivityLogger::getSlowQueryRequests(1000); // > 1 second

// Get statistics for performance analysis
$stats = ActivityLogger::getStatistics();
echo "Average response time: " . $stats['average_response_time'] . "ms";
echo "Average memory usage: " . round($stats['average_memory'] / (1024 * 1024), 2) . "MB";
echo "Average query count: " . $stats['average_query_count'];
echo "Average query time: " . $stats['average_query_time'] . "ms";
```

### Search Service

Use the search service for advanced searching:

```php
use ActivityLogger\Services\ActivityLogSearchService;

$searchService = app(ActivityLogSearchService::class);

// Quick search
$results = $searchService->quickSearch('error', ['date' => '2025-01-17']);

// Search errors
$errors = $searchService->searchErrors(['start_date' => '2025-01-01']);

// Search performance issues
$issues = $searchService->searchPerformanceIssues([
    'slow_threshold' => 2000, // 2 seconds
    'memory_threshold' => 104857600, // 100MB
]);

// Search by location
$locationLogs = $searchService->searchByLocation('US', 'New York');

// Get today's activity
$todayLogs = $searchService->getTodaysActivity();
```

## Database Schema

The package creates an `activity_logs` table with the following key fields:

**Basic Request Information:**
- `user_id` - The authenticated user's ID
- `session_id` - Session identifier
- `ip_address` - Client IP address
- `request_date` - Date of the request
- `requested_at` - Timestamp of the request
- `method` - HTTP method (GET, POST, etc.)
- `url` - Full URL of the request
- `referer` - Referer URL
- `user_agent` - User agent string

**Request Context:**
- `route_name` - Laravel route name
- `controller_action` - Controller and action
- `middleware` - Applied middleware
- `request_id` - Unique request identifier
- `request_headers` - Request headers
- `request_params` - Query parameters
- `request_body` - Request body data

**Response Information:**
- `response_code` - HTTP response code
- `response_time` - Response time in milliseconds
- `response_size` - Response size in bytes
- `response_headers` - Response headers
- `response_body` - Response body data

**Performance Metrics:**
- `memory_usage` - Memory used by the request (in bytes)
- `query_count` - Number of database queries
- `query_time` - Database query execution time
- `cpu_usage` - CPU usage percentage

**Client Information:**
- `browser` - Browser name
- `platform` - Operating system
- `device` - Device type
- `is_ajax` - Whether request was AJAX
- `is_mobile` - Whether request was from mobile

**Geographical Information:**
- `country` - Country code
- `city` - City name
- `timezone` - Timezone

**Error Information:**
- `error_message` - Error message if any
- `error_trace` - Stack trace
- `error_type` - Exception class

**Additional:**
- `custom_data` - Custom data (JSON)
- `created_at`, `updated_at` - Timestamps

## API Response Format

### Activity Log Resource

The API returns logs in this format (excluding `request_date` for visibility):

```json
{
  "id": 123,
  "user_id": 456,
  "ip_address": "192.168.1.1",
  "requested_at": "2025-01-17T10:30:00Z",
  "method": "POST",
  "url": "https://example.com/api/users",
  "response_code": 200,
  "response_time": 150.5,
  "memory_usage": 1048576,
  "query_count": 3,
  "query_time": 45.2,
  "controller_action": "UserController@store",
  "country": "US",
  "city": "New York",
  "formatted_duration": "150.5ms",
  "formatted_memory_usage": "1.00MB",
  "is_successful": true
}
```

### Search Parameters

Available search parameters:
- `user_id` - Filter by user ID
- `ip_address` - Filter by IP address
- `start_date` / `end_date` - Date range (uses `request_date` internally)
- `method` - HTTP method
- `url` - URL pattern
- `response_code` - HTTP response code
- `controller_action` - Controller action
- `country` / `city` - Geographical filters
- `min_response_time` / `max_response_time` - Performance filters
- `has_errors` - Boolean filter for errors
- `is_mobile` / `is_ajax` - Request type filters

## Security Considerations

- Sensitive fields like passwords are automatically excluded from logs
- Configure `sensitive_fields` to add more fields to exclude
- Headers like Authorization and Cookie are filtered by default
- Request/response bodies can be disabled for sensitive endpoints
- The `request_date` field is used internally for efficient searching but excluded from API responses

## Configuration

### Routes Configuration

```php
// config/activity-logger.php
'routes' => [
    'enable_routes' => env('ACTIVITY_LOGGER_ENABLE_ROUTES', true),
    'middleware' => ['web'],
    'prefix' => 'activity-logger',
    'domain' => null,
],

'dashboard' => [
    'enabled' => env('ACTIVITY_LOGGER_DASHBOARD_ENABLED', true),
    'realtime_refresh' => 30, // seconds
    'default_date_range' => 7, // days
],
```

### Date Handling

The package uses two date fields:
- `request_date` - Used internally for efficient database indexing and searching
- `requested_at` - Full timestamp shown in API responses and exports

This dual approach provides:
- Fast date-based queries using the indexed `request_date` field
- Precise timing information via `requested_at`
- Clean API responses without exposing internal date optimization

## Publishing to Git Repository

### Preparing for Git Repository

1. Create a new repository on GitHub/GitLab/Bitbucket
2. Initialize git in your package directory:

```bash
cd packages/activity-logger
git init
git add .
git commit -m "Initial commit: Laravel Activity Logger package"
git branch -M main
git remote add origin https://github.com/whitelakeint/activity-logger.git
git push -u origin main
```

### Creating Releases

1. Tag your releases for version management:

```bash
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

2. Update your `composer.json` in projects that use this package:

```json
{
    "require": {
        "whitelakeint/activity-logger": "^1.0"
    }
}
```

### Branch Strategy

- `main` - Stable releases
- `develop` - Development branch
- `feature/*` - Feature branches

### Installation from Different Branches

```bash
# Install from main branch
composer require whitelakeint/activity-logger:dev-main

# Install from develop branch
composer require whitelakeint/activity-logger:dev-develop

# Install specific version
composer require whitelakeint/activity-logger:^1.0
```

## Deployment Considerations

### Performance Optimization

1. **Database Indexes**: The migration includes proper indexes on frequently queried fields
2. **Request Date Optimization**: Uses `request_date` for efficient date-based queries
3. **Cleanup Strategy**: Implement regular cleanup of old logs

```bash
# Add to your cron/scheduler
php artisan activity-logger:clear --days=90
```

### Production Settings

```php
// config/activity-logger.php - Production recommendations
'log_request_body' => false,  // Disable for performance
'log_response_body' => false, // Disable for performance
'max_body_size' => 5000,      // Reduce size limit
'cleanup' => [
    'enabled' => true,
    'keep_days' => 30,        // Keep logs for 30 days
],
```

### Environment Variables

```bash
# .env settings
ACTIVITY_LOGGER_ENABLED=true
ACTIVITY_LOGGER_AUTO_REGISTER=true
ACTIVITY_LOGGER_REQUEST_BODY=false
ACTIVITY_LOGGER_RESPONSE_BODY=false
ACTIVITY_LOGGER_KEEP_DAYS=30
ACTIVITY_LOGGER_DASHBOARD_ENABLED=true
```

### Monitoring & Alerts

1. Monitor log table size
2. Set up alerts for high error rates
3. Monitor performance impact

### Security Notes

- Ensure sensitive data is properly filtered
- Restrict access to dashboard endpoints
- Consider IP whitelisting for admin routes
- Review logged data regularly

## Troubleshooting

### Common Issues

1. **Migration Errors**: Ensure database supports JSON columns
2. **Performance Issues**: Check index usage and cleanup frequency
3. **Memory Issues**: Reduce `max_body_size` or disable body logging
4. **Missing Data**: Verify middleware is properly registered

### Debug Mode

```php
// Enable debug logging
'log_errors' => true,
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This package is open-source software licensed under the MIT license.