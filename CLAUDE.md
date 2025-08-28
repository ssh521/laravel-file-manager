# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel package (`ssh521/laravel-file-manager`) that provides a simple file manager interface for Laravel applications. It offers drag & drop upload, folder management, file preview, and security features.

## Architecture

### Core Components
- **Service Provider**: `src/FileManagerServiceProvider.php` - Handles package registration, route loading, view loading, and asset publishing
- **Controller**: `src/Http/Controllers/FileManagerController.php` - Main controller handling file operations (index, upload, createFolder, delete)
- **Configuration**: `config/file-manager.php` - Comprehensive configuration for routes, storage, security, UI, and features
- **View**: `resources/views/index.blade.php` - Bootstrap 5 interface for file management
- **Routes**: `routes/web.php` - Package route definitions

### Key Features
- Folder creation and navigation with breadcrumb support
- File upload with drag & drop and MIME type validation
- Image preview capabilities
- Security measures including path traversal protection
- Configurable forbidden file extensions
- Route middleware support for authentication

## Development Commands

### Package Development
```bash
# Install dependencies
composer install

# Run tests (if available)
./vendor/bin/phpunit

# Check code style (standard Laravel/PHP tools)
./vendor/bin/php-cs-fixer fix
```

### Package Publishing
```bash
# Publish configuration
php artisan vendor:publish --tag=file-manager-config

# Publish views for customization
php artisan vendor:publish --tag=file-manager-views

# Publish assets (if any)
php artisan vendor:publish --tag=file-manager-assets
```

## Configuration

### Storage Paths
- Default storage: `storage/app/public`
- Public URL path: `/storage`
- Configurable in `config/file-manager.php`

### Security Configuration
- Path traversal protection via `isPathSafe()` method
- Forbidden extensions: `['php', 'js', 'html', 'htm']`
- Folder name validation with regex pattern
- CSRF protection on all forms

### Route Configuration
- Default prefix: `/file-manager`
- Default middleware: `['web']`
- All routes use `file-manager` name prefix

## File Structure

```
src/
├── FileManagerServiceProvider.php     # Package service provider
└── Http/Controllers/
    └── FileManagerController.php      # Main controller
config/file-manager.php                # Package configuration
resources/views/index.blade.php        # Main UI view
routes/web.php                         # Package routes
```

## Testing

No test files are currently present in the repository. Tests would typically be organized under a `tests/` directory using PHPUnit with Orchestra Testbench for Laravel package testing.