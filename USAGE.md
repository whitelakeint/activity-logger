# Activity Logger Dashboard - Usage Guide

A comprehensive guide to accessing and using the Activity Logger web-based dashboard in your Laravel application.

## 🚀 Quick Start

### Step 1: Enable Web Dashboard
Ensure the web routes are enabled in your configuration:

```php
// config/activity-logger.php
'routes' => [
    'enabled' => true,
    'prefix' => 'activity-logger',
    'middleware' => ['web', 'auth'], // Add your authentication
],
```

### Step 2: Access the Dashboard
Visit the dashboard in your browser:
```
https://your-application.com/activity-logger
```

## 🔐 Authentication & Access Control

### Basic Authentication Setup
```php
// config/activity-logger.php
'routes' => [
    'middleware' => ['web', 'auth'], // Requires user login
],
```

### Admin-Only Access
```php
'routes' => [
    'middleware' => ['web', 'auth', 'admin'], // Admin role required
],
```

### Role-Based Access Control
```php
// Using Spatie Laravel Permission
'routes' => [
    'middleware' => ['web', 'auth', 'role:admin,manager'],
],

// Using custom permissions
'routes' => [
    'middleware' => ['web', 'auth', 'permission:view-activity-logs'],
],
```

### Custom Access Control
Create a custom middleware:

```php
// app/Http/Middleware/ActivityLoggerAccess.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ActivityLoggerAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Custom logic - example: check if user is admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Access denied to Activity Logger dashboard');
        }
        
        return $next($request);
    }
}
```

Register the middleware:
```php
// app/Http/Kernel.php
protected $routeMiddleware = [
    'activity-logger.access' => \App\Http\Middleware\ActivityLoggerAccess::class,
];

// config/activity-logger.php
'routes' => [
    'middleware' => ['web', 'activity-logger.access'],
],
```

## 📊 Dashboard Pages Overview

### Main Dashboard (`/activity-logger`)
**Purpose**: Real-time overview of your application's activity

**Features**:
- Live request counters and success rates
- Interactive charts showing traffic trends
- Recent activity feed
- System health indicators
- Quick access to other sections

**Key Metrics**:
- Total requests (24h)
- Error rate percentage
- Average response time
- Active users count

### Activity Logs (`/activity-logger/logs`)
**Purpose**: Detailed view of all HTTP requests

**Features**:
- Real-time log streaming
- Advanced filtering (20+ filter options)
- Search across URLs, routes, and parameters
- Export capabilities
- Expandable rows with full request details

**Filtering Options**:
- Date/time ranges
- HTTP methods (GET, POST, PUT, DELETE)
- Response codes (2xx, 3xx, 4xx, 5xx)
- Response time ranges
- IP addresses and user agents
- Custom search queries

### Performance Analytics (`/activity-logger/performance`)
**Purpose**: Monitor application performance metrics

**Features**:
- Response time trends and distributions
- Memory usage patterns
- Database query analysis
- Slow request identification
- Performance alerts

**Key Insights**:
- Average response times by endpoint
- Memory consumption patterns
- Database query counts and execution times
- Performance bottleneck identification

### Error Monitoring (`/activity-logger/errors`)
**Purpose**: Track and analyze application errors

**Features**:
- Error timeline visualization
- Exception stack traces
- Error frequency analysis
- Debugging information
- Error categorization

**Error Information**:
- Exception type and message
- Stack trace with file locations
- Request context (URL, method, parameters)
- User information (if available)
- Occurrence frequency

### Traffic Analysis (`/activity-logger/traffic`)
**Purpose**: Understand your application's traffic patterns

**Features**:
- Geographic distribution of visitors
- Device and browser statistics
- Top pages and endpoints
- Traffic source analysis
- Visitor behavior patterns

**Analytics Include**:
- Country/city breakdown
- Desktop vs mobile vs tablet usage
- Browser and OS statistics
- Most popular pages
- Traffic trends over time

### User Activity (`/activity-logger/users`)
**Purpose**: Monitor user behavior (privacy-compliant)

**Features**:
- Active user tracking
- User session analysis
- Activity patterns
- Privacy controls and anonymization
- GDPR-compliant data handling

**Privacy Features**:
- IP address anonymization
- User ID hashing
- Data retention controls
- Export restrictions
- Consent management

### Reports & Export (`/activity-logger/reports`)
**Purpose**: Generate and download comprehensive reports

**Features**:
- Multiple export formats (CSV, Excel, JSON, PDF)
- Scheduled automated reports
- Custom report builder
- Email delivery options
- Report templates

**Export Options**:
- Data format selection
- Date range filtering
- Field selection
- Compression options
- Email delivery scheduling

## 📱 Mobile Usage

The dashboard is fully optimized for mobile devices:

### Mobile Navigation
- **Hamburger Menu**: Tap the menu icon (☰) to access all sections
- **Quick Stats**: Tap the stats icon to view key metrics
- **Search**: Tap the search icon for mobile-optimized search

### Mobile Gestures
- **Pull to Refresh**: Pull down on any page to refresh data
- **Swipe Tables**: Swipe left/right on tables to scroll horizontally
- **Touch Targets**: All buttons are optimized for touch (44px minimum)

### Mobile Features
- **Floating Action Button**: Quick access to common actions
- **Responsive Charts**: Charts automatically adapt to screen size
- **Touch-Friendly Filters**: Mobile-optimized filtering interface

## 🔧 Advanced Features

### Real-Time Notifications
**Access**: Notifications appear automatically in the top-right corner

**Types**:
- **Error Alerts**: Critical errors and spikes
- **Performance Warnings**: Slow response times
- **Security Alerts**: Suspicious activity
- **System Status**: Server status changes

**Management**:
- Click the notification bell to view history
- Dismiss individual notifications
- Configure notification preferences

### Advanced Filtering
**Access**: Use the "Show Filters" button on logs and other pages

**Filter Types**:
- **Quick Filters**: Pre-configured common filters
- **Date Ranges**: Today, yesterday, last 7 days, custom
- **Performance Filters**: Response time, memory usage, query count
- **Geographic Filters**: Country, IP address, device type
- **Advanced Query**: SQL-like search syntax

**Filter Management**:
- Save frequently used filter combinations
- Load saved filter presets
- Share filter URLs with team members
- Clear all filters quickly

### Data Export
**Access**: Use export buttons throughout the dashboard

**Export Formats**:
- **CSV**: For spreadsheet analysis
- **Excel**: For advanced Excel features
- **JSON**: For programmatic processing
- **PDF**: For formatted reports

**Export Options**:
- Select specific fields to include
- Choose date ranges
- Apply filters before export
- Compress large exports
- Email reports automatically

### Security Features
**Access**: Built-in security monitoring throughout the dashboard

**Security Tools**:
- **Vulnerability Scanner**: Automated security testing
- **Security Score**: Overall security health rating
- **Threat Detection**: Suspicious activity monitoring
- **Security Recommendations**: Actionable security improvements

## 🛠️ Customization

### Theme Customization
```php
// config/activity-logger.php
'theme' => [
    'primary_color' => '#your-brand-color',
    'logo_url' => '/path/to/your/logo.png',
    'app_name' => 'Your Application Name',
    'favicon' => '/path/to/favicon.ico',
],
```

### Custom Dashboard Title
```php
'dashboard' => [
    'title' => 'My App Analytics',
    'subtitle' => 'Application Monitoring Dashboard',
],
```

### Privacy Settings
```php
'privacy' => [
    'anonymize_ips' => true,
    'blur_sensitive_data' => true,
    'hide_user_details' => false,
    'data_retention_days' => 90,
],
```

## 🔍 Search & Filtering Examples

### Basic Search Examples
```
# Search for specific URL
url:/api/users

# Search for errors
response_code:500

# Search for slow requests
response_time:>1000

# Search by IP address
ip:192.168.1.100
```

### Advanced Query Examples
```
# Multiple conditions
method:POST AND response_code:>400

# Date range with conditions
created_at:2024-01-01..2024-01-31 AND url:*/api/*

# Complex conditions
(method:POST OR method:PUT) AND response_time:>500
```

## 📊 API Integration

Access dashboard data programmatically:

```php
// Get dashboard statistics
$stats = app('activity-logger')->getDashboardStats();

// Get filtered logs
$logs = app('activity-logger')->getLogs([
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
    'method' => 'POST',
]);

// Get performance metrics
$performance = app('activity-logger')->getPerformanceMetrics('24h');

// Export data
$export = app('activity-logger')->exportData('csv', $filters);
```

## 🚨 Troubleshooting

### Common Issues

#### Dashboard Not Loading
1. **Check Route Configuration**:
   ```php
   // Ensure routes are enabled
   'routes' => ['enabled' => true]
   ```

2. **Verify Middleware**:
   ```php
   // Check middleware configuration
   'middleware' => ['web', 'auth']
   ```

3. **Clear Cache**:
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

#### No Data Showing
1. **Verify Logging is Active**:
   ```php
   'enabled' => true,
   'log_requests' => true,
   ```

2. **Check Database Connection**:
   ```bash
   php artisan migrate:status
   ```

3. **Test Manual Logging**:
   ```php
   ActivityLogger::log('test', ['message' => 'Testing']);
   ```

#### Permission Denied
1. **Check User Authentication**:
   ```php
   // Ensure user is logged in
   auth()->check()
   ```

2. **Verify User Roles**:
   ```php
   // Check if user has required role
   auth()->user()->hasRole('admin')
   ```

3. **Review Middleware Stack**:
   ```bash
   php artisan route:list --name=activity-logger
   ```

#### Performance Issues
1. **Enable Caching**:
   ```php
   'cache' => ['enabled' => true, 'ttl' => 300]
   ```

2. **Add Database Indexes**:
   ```php
   Schema::table('activity_logs', function (Blueprint $table) {
       $table->index(['created_at', 'method']);
       $table->index('response_code');
   });
   ```

3. **Configure Data Cleanup**:
   ```php
   'cleanup' => ['enabled' => true, 'keep_days' => 30]
   ```

## 📞 Support & Resources

### Getting Help
- **Documentation**: Complete API documentation
- **Community**: Join our Discord/Slack community
- **Issues**: Report bugs on GitHub
- **Email Support**: support@yourpackage.com

### Best Practices
1. **Regular Monitoring**: Check dashboard daily
2. **Set Up Alerts**: Configure email notifications
3. **Data Retention**: Implement appropriate cleanup policies
4. **Security Reviews**: Regular security scans
5. **Performance Monitoring**: Track trends over time

### Performance Tips
- Enable caching for better response times
- Use database indexes for faster queries
- Implement data archiving for old logs
- Monitor memory usage regularly
- Set up automated cleanup jobs

---

## 📋 Quick Reference

### Default URLs
- Dashboard: `/activity-logger`
- Logs: `/activity-logger/logs`
- Performance: `/activity-logger/performance`
- Errors: `/activity-logger/errors`
- Traffic: `/activity-logger/traffic`
- Users: `/activity-logger/users`
- Reports: `/activity-logger/reports`

### Key Keyboard Shortcuts
- `Ctrl/Cmd + K`: Quick search
- `R`: Refresh current page
- `F`: Open filters
- `E`: Open export options
- `Esc`: Close modals/dropdowns

### Mobile Gestures
- **Pull Down**: Refresh data
- **Swipe Left/Right**: Scroll tables
- **Long Press**: Context menu
- **Pinch**: Zoom charts (where applicable)

---

**Happy Monitoring!** 🎉

For more detailed information, check the main [README.md](README.md) file.