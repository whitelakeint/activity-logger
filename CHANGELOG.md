# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of Laravel Activity Logger
- Comprehensive request/response logging
- Performance metrics tracking (response time, memory usage, query count)
- Error tracking with stack traces
- Browser and device detection
- Geographical information tracking
- Advanced search and filtering capabilities
- RESTful API endpoints
- Real-time dashboard
- Comprehensive reporting system
- Export functionality (JSON, CSV, XML)
- Console commands for maintenance
- Configurable middleware
- Security features for sensitive data filtering

### Features
- **Complete Request Tracking**: User ID, IP address, timestamps, HTTP methods, URLs
- **Performance Monitoring**: Response time, memory usage, database query metrics
- **Error Handling**: Detailed error logging with stack traces
- **Device Detection**: Browser, platform, and device identification
- **Geographical Tracking**: Country, city, and timezone information
- **Advanced Search**: Multiple search methods and filters
- **API Endpoints**: RESTful API for accessing logs and statistics
- **Dashboard**: Real-time monitoring and statistics
- **Reports**: Daily, weekly, monthly, and custom reports
- **Export**: Multiple export formats with customizable fields
- **Security**: Automatic filtering of sensitive data
- **Optimization**: Efficient date-based indexing and search

### Technical Details
- Laravel 9.x, 10.x, 11.x support
- PHP 8.0+ compatibility
- Database optimization with proper indexing
- Efficient date handling with dual date fields
- Comprehensive API resources
- Middleware-based logging
- Configurable cleanup strategies

## [1.0.0] - 2025-01-17

### Added
- Initial stable release