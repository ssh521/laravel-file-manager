# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel package (`ssh521/laravel-file-manager`) that provides a simple file manager interface for Laravel applications. It offers drag & drop upload, folder management, file preview, and security features.

## Architecture

### Core Components
- **Service Provider**: `src/FileManagerServiceProvider.php` - Handles package registration, route loading, view loading, and asset publishing
- **Controller**: `src/Http/Controllers/FileManagerController.php` - Main controller handling file operations (index, upload, createFolder, delete)
- **Configuration**: `config/file-manager.php` - Comprehensive configuration for routes, storage, security, UI, and features
- **View**: `resources/views/index.blade.php` - Tailwind CSS 4.0 interface for file management
- **Routes**: `routes/web.php` - Package route definitions

### Key Features
- **Folder Management**: Create, delete, and navigate directories with breadcrumb navigation
- **File Upload**: Drag & drop interface with multi-file support and MIME type validation
- **Image Preview**: Real-time preview of image files with responsive sizing
- **Security**: Path traversal protection, file extension filtering, CSRF protection
- **Customization**: Configurable routes, storage paths, file restrictions, and UI text
- **Authentication**: Configurable middleware support for access control
- **Modern UI**: Tailwind CSS 4.0 interface with responsive design and smooth interactions

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

## UI Framework

The package uses **Tailwind CSS 4.0** for styling, providing a modern and responsive interface. Key UI components include:

### Styling Architecture
- **Tailwind CSS 4.0**: Latest version loaded via CDN for quick setup
- **Font Awesome 6**: Icons for file types, actions, and navigation
- **Custom CSS**: Minimal custom styles for drag & drop interactions
- **No Bootstrap dependency**: Completely migrated from Bootstrap 5

### Layout System
- **Responsive Grid**: Uses Tailwind's `grid` system with `lg:grid-cols-3` layout
- **Mobile-First**: Stacks components vertically on small screens
- **Container-Based**: Centered layout with proper spacing

### UI Components
- **File Table**: Clean table with hover states and proper spacing using `divide-y` utilities
- **Upload Area**: Drag & drop zone with visual feedback (`dragover` states)
- **Action Buttons**: Modern button styles with proper focus/hover states
- **Modal Dialog**: Custom modal using Tailwind utilities (no Bootstrap Modal)
- **Breadcrumb Navigation**: Clean breadcrumb with proper separators

### Color Scheme
- **Primary**: Blue (`blue-600`, `blue-700`) for main actions
- **Success**: Green (`green-600`) for positive actions
- **Danger**: Red (`red-600`) for destructive actions
- **Neutral**: Gray palette for backgrounds and secondary elements
- **Status Colors**: Yellow for folders, green for images, gray for files

### Interactive Elements
- **Checkboxes**: Styled with `rounded border-gray-300 text-blue-600`
- **Buttons**: Consistent padding, rounded corners, focus rings
- **Hover States**: Subtle background changes on interactive elements
- **Loading States**: Disabled button states with opacity changes

## Testing

No test files are currently present in the repository. Tests would typically be organized under a `tests/` directory using PHPUnit with Orchestra Testbench for Laravel package testing.