# Contributing to Laravel Activity Logger

Thank you for considering contributing to the Laravel Activity Logger package! This guide will help you understand how to contribute effectively.

## Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to amit@whitelakedigital.com.

## How to Contribute

### Reporting Bugs

Before creating bug reports, please check the existing issues as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples**
- **Describe the behavior you observed and what behavior you expected**
- **Include screenshots if applicable**
- **Specify your PHP version, Laravel version, and package version**

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

- **Use a clear and descriptive title**
- **Provide a step-by-step description of the suggested enhancement**
- **Provide specific examples to demonstrate the steps**
- **Describe the current behavior and explain which behavior you expected**
- **Explain why this enhancement would be useful**

### Pull Requests

- Fork the repository
- Create a new branch from `develop`
- Make your changes
- Add tests if applicable
- Update documentation if needed
- Ensure all tests pass
- Submit a pull request

#### Branch Naming

- `feature/description` - for new features
- `bugfix/description` - for bug fixes
- `hotfix/description` - for urgent fixes
- `docs/description` - for documentation updates

#### Commit Messages

Use clear and meaningful commit messages:

```
feat: add user activity tracking
fix: resolve memory leak in logging middleware
docs: update installation instructions
test: add unit tests for search service
```

## Development Setup

1. Clone the repository:
```bash
git clone https://github.com/whitelakeint/activity-logger.git
cd activity-logger
```

2. Install dependencies:
```bash
composer install
```

3. Set up testing environment:
```bash
cp phpunit.xml.example phpunit.xml
```

4. Run tests:
```bash
./vendor/bin/phpunit
```

## Coding Standards

This project follows the PSR-12 coding standard. Please ensure your code follows these standards:

- Use 4 spaces for indentation
- Follow PSR-12 naming conventions
- Add proper docblocks to all methods
- Keep lines under 120 characters when possible

## Testing

- Write tests for new features
- Ensure all existing tests pass
- Maintain test coverage above 80%
- Use descriptive test names

## Documentation

- Update README.md for new features
- Add inline comments for complex logic
- Update CHANGELOG.md following Keep a Changelog format
- Include examples in documentation

## Release Process

1. Update version in `composer.json`
2. Update `CHANGELOG.md`
3. Create a pull request to `main`
4. Tag the release after merge
5. Update release notes on GitHub

## Getting Help

If you need help with contributing:

- Check the existing documentation
- Look at similar implementations in the codebase
- Open an issue for discussion
- Contact the maintainers

## Recognition

Contributors will be recognized in the README.md file and release notes.

## License

By contributing to this project, you agree that your contributions will be licensed under the MIT License.