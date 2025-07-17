# Setup Guide for Laravel Activity Logger

This guide will help you set up and publish the Laravel Activity Logger package to a Git repository.

## Prerequisites

- Git installed on your system
- GitHub/GitLab/Bitbucket account
- Composer installed
- PHP 8.0 or higher

## Step 1: Prepare the Package

1. Ensure all files are in the correct location:
```
packages/activity-logger/
├── src/
├── config/
├── database/
├── tests/
├── composer.json
├── README.md
├── LICENSE
├── CHANGELOG.md
├── CONTRIBUTING.md
├── phpunit.xml
├── .gitignore
└── SETUP.md
```

2. Update the `composer.json` file with your details:
```json
{
    "name": "whitelakeint/activity-logger",
    "description": "Comprehensive activity logging package for Laravel applications",
    "authors": [
        {
            "name": "Amit Shah",
            "email": "amit@whitelakedigital.com"
        }
    ]
}
```

## Step 2: Initialize Git Repository

1. Navigate to the package directory:
```bash
cd packages/activity-logger
```

2. Initialize Git repository:
```bash
git init
```

3. Add all files:
```bash
git add .
```

4. Create initial commit:
```bash
git commit -m "Initial commit: Laravel Activity Logger package

- Complete request/response logging
- Performance metrics tracking
- Error tracking with stack traces
- Advanced search and filtering
- RESTful API endpoints
- Real-time dashboard
- Comprehensive reporting system
- Export functionality
- Console commands for maintenance"
```

## Step 3: Create Remote Repository

1. Create a new repository on GitHub (or your preferred platform):
   - Repository name: `activity-logger`
   - Description: "Comprehensive activity logging package for Laravel applications"
   - Make it public (or private if preferred)

2. Add remote origin:
```bash
git remote add origin https://github.com/whitelakeint/activity-logger.git
```

3. Push to remote:
```bash
git branch -M main
git push -u origin main
```

## Step 4: Create Development Branch

1. Create and switch to development branch:
```bash
git checkout -b develop
git push -u origin develop
```

2. Set up branch protection (optional but recommended):
   - Go to repository settings
   - Set up branch protection rules for `main`
   - Require pull requests before merging

## Step 5: Tag First Release

1. Switch to main branch:
```bash
git checkout main
```

2. Create and push first tag:
```bash
git tag -a v1.0.0 -m "Release version 1.0.0

Initial stable release with:
- Complete activity logging system
- Performance monitoring
- Error tracking
- Advanced search capabilities
- RESTful API
- Dashboard and reporting
- Export functionality
- Laravel 9.x, 10.x, 11.x support"

git push origin v1.0.0
```

## Step 6: Testing the Installation

1. Create a test Laravel project:
```bash
composer create-project laravel/laravel test-project
cd test-project
```

2. Add the package to composer.json:
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/whitelakeint/activity-logger.git"
        }
    ],
    "require": {
        "whitelakeint/activity-logger": "^1.0"
    }
}
```

3. Install the package:
```bash
composer install
```

4. Publish and run migrations:
```bash
php artisan vendor:publish --tag=activity-logger-config
php artisan migrate
```

5. Test the middleware:
```php
// In routes/web.php
Route::middleware(['activity-logger'])->group(function () {
    Route::get('/test', function () {
        return response()->json(['message' => 'Activity logged!']);
    });
});
```

## Step 7: Documentation and Examples

1. Create comprehensive documentation
2. Add usage examples
3. Include troubleshooting guide
4. Add contribution guidelines

## Step 8: Continuous Integration (Optional)

1. Set up GitHub Actions for testing:
```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php: [8.0, 8.1, 8.2]
        laravel: [9.*, 10.*, 11.*]
    
    steps:
    - uses: actions/checkout@v2
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ matrix.php }}
    - name: Install dependencies
      run: composer install
    - name: Run tests
      run: vendor/bin/phpunit
```

## Step 9: Publishing to Packagist (Optional)

1. Submit your package to Packagist.org
2. Set up auto-update hooks
3. Configure version constraints

## Workflow for Updates

1. Create feature branch from `develop`
2. Make changes
3. Update tests
4. Update documentation
5. Create pull request to `develop`
6. Merge to `develop`
7. When ready for release, merge `develop` to `main`
8. Tag new version
9. Update CHANGELOG.md

## Troubleshooting

### Common Issues

1. **Permission denied**: Check SSH keys or use HTTPS
2. **Composer install fails**: Verify repository URL and access
3. **Migration errors**: Check database compatibility
4. **Autoload issues**: Run `composer dump-autoload`

### Getting Help

- Check the documentation
- Review existing issues
- Contact maintainers
- Open new issue with detailed information

## Best Practices

1. **Version Control**: Use semantic versioning
2. **Testing**: Maintain good test coverage
3. **Documentation**: Keep README updated
4. **Security**: Regular dependency updates
5. **Compatibility**: Test with multiple Laravel versions

## Maintenance

1. **Regular Updates**: Keep dependencies updated
2. **Security Patches**: Apply security fixes promptly
3. **Performance**: Monitor and optimize
4. **Community**: Respond to issues and PRs

This setup guide ensures your Laravel Activity Logger package is properly configured, documented, and ready for distribution and collaboration.