<?php

return [
    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),

    'user_model' => env('ACTIVITY_LOGGER_USER_MODEL', 'App\Models\User'),

    'auto_register_middleware' => env('ACTIVITY_LOGGER_AUTO_REGISTER', false),

    'log_request_headers' => env('ACTIVITY_LOGGER_REQUEST_HEADERS', true),
    
    'log_request_params' => env('ACTIVITY_LOGGER_REQUEST_PARAMS', true),
    
    'log_request_body' => env('ACTIVITY_LOGGER_REQUEST_BODY', true),
    
    'log_response_headers' => env('ACTIVITY_LOGGER_RESPONSE_HEADERS', false),
    
    'log_response_body' => env('ACTIVITY_LOGGER_RESPONSE_BODY', false),
    
    'log_error_trace' => env('ACTIVITY_LOGGER_ERROR_TRACE', true),
    
    'log_errors' => env('ACTIVITY_LOGGER_LOG_ERRORS', true),

    'max_body_size' => env('ACTIVITY_LOGGER_MAX_BODY_SIZE', 10000),

    'skip_urls' => [
        'telescope/*',
        'horizon/*',
        '_debugbar/*',
        'livewire/*',
        'broadcasting/*',
        'health',
        'favicon.ico',
        'robots.txt',
    ],

    'skip_methods' => [
        // 'OPTIONS',
    ],

    'skip_routes' => [
        // 'password.reset',
        // 'verification.verify',
    ],

    'sensitive_headers' => [
        'authorization',
        'cookie',
        'x-csrf-token',
        'x-xsrf-token',
        'api-key',
        'api-secret',
        'access-token',
    ],

    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'credit_card',
        'card_number',
        'cvv',
        'cvc',
        'ssn',
        'social_security_number',
        'pin',
        'secret',
        'token',
        'api_key',
        'api_secret',
        'private_key',
    ],

    'cleanup' => [
        'enabled' => env('ACTIVITY_LOGGER_CLEANUP_ENABLED', true),
        
        'keep_days' => env('ACTIVITY_LOGGER_KEEP_DAYS', 90),
        
        'chunk_size' => env('ACTIVITY_LOGGER_CLEANUP_CHUNK', 1000),
        
        'schedule' => env('ACTIVITY_LOGGER_CLEANUP_SCHEDULE', 'daily'),
    ],

    'export' => [
        'chunk_size' => 1000,
        
        'formats' => ['json', 'csv', 'xml'],
        
        'default_format' => 'json',
        
        'compress' => true,
    ],

    'monitoring' => [
        'slow_request_threshold' => env('ACTIVITY_LOGGER_SLOW_REQUEST_MS', 1000),
        
        'high_memory_threshold' => env('ACTIVITY_LOGGER_HIGH_MEMORY_MB', 50),
        
        'alert_on_errors' => env('ACTIVITY_LOGGER_ALERT_ERRORS', true),
        
        'alert_channels' => ['mail', 'slack'],
    ],

    'analysis' => [
        'enable_statistics' => true,
        
        'cache_duration' => 3600,
        
        'top_urls_count' => 20,
        
        'top_users_count' => 20,
    ],
    
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
];