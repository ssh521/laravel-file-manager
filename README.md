# Laravel File Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ssh521/laravel-file-manager.svg?style=flat-square)](https://packagist.org/packages/ssh521/laravel-file-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/ssh521/laravel-file-manager.svg?style=flat-square)](https://packagist.org/packages/ssh521/laravel-file-manager)

A simple and elegant file manager for Laravel applications with drag & drop upload, folder management, and file preview capabilities.

## Features

- 📁 **Folder Management**: Create, delete, and navigate through directories
- 📤 **File Upload**: Drag & drop or click to upload multiple files
- 🖼️ **Image Preview**: Built-in preview for image files
- 🗂️ **File Operations**: Delete files and folders with batch operations
- 🔒 **Security**: Path traversal protection and configurable restrictions
- 🎨 **Bootstrap UI**: Clean and responsive interface
- ⚙️ **Configurable**: Extensive configuration options
- 🌐 **Localization Ready**: Easy to customize text and labels

## Screenshots

![File Manager Interface](screenshot.png)

## Installation

You can install the package via composer:

```bash
composer require ssh521/laravel-file-manager
```

The package will automatically register itself.

### Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=file-manager-config
```

### Publish Views (Optional)

```bash
php artisan vendor:publish --tag=file-manager-views
```

## Usage

### Basic Usage

The file manager will be automatically available at `/file-manager` route.

You can also create a link to the file manager in your application:

```php
<a href="{{ route('file-manager.index') }}" class="btn btn-primary">
    Open File Manager
</a>
```

### Configuration

After publishing the configuration file, you can customize the behavior in `config/file-manager.php`:

```php
return [
    // Storage paths
    'storage_path' => 'app/public',
    'public_path' => 'storage',

    // Route configuration
    'route' => [
        'prefix' => 'file-manager',
        'name' => 'file-manager',
        'middleware' => ['web'],
    ],

    // UI configuration
    'title' => 'File Manager',
    'back_route' => 'admin.dashboard', // Optional back button
    'back_text' => 'Back to Dashboard',

    // File upload limits
    'max_file_size' => 10240, // KB
    'allowed_mimes' => [], // Empty = allow all

    // Security
    'forbidden_extensions' => ['php', 'js', 'html'],
];
```

### Integration with Your Application

#### Add Navigation Link

```php
// In your admin panel or navigation
<a href="{{ route('file-manager.index') }}" class="nav-link">
    <i class="fas fa-folder"></i> File Manager
</a>
```

#### Custom Back Button

Set the back route in your config:

```php
'back_route' => 'admin.dashboard',
'back_text' => 'Back to Dashboard',
```

#### Middleware Protection

Add authentication middleware:

```php
'route' => [
    'middleware' => ['web', 'auth', 'admin'],
],
```

## Configuration Options

### Storage Configuration

```php
'storage_path' => 'app/public',  // Laravel storage path
'public_path' => 'storage',      // Public URL path
```

### Route Configuration

```php
'route' => [
    'prefix' => 'admin/files',       // Custom URL prefix
    'name' => 'admin.files',         // Route name prefix
    'middleware' => ['web', 'auth'], // Route middleware
],
```

### File Upload Restrictions

```php
'max_file_size' => 5120,  // 5MB in KB
'allowed_mimes' => [
    'image/jpeg',
    'image/png', 
    'application/pdf',
],
'forbidden_extensions' => ['php', 'exe', 'bat'],
```

### UI Customization

```php
'title' => 'My File Manager',
'root_name' => 'Files',
'back_route' => 'dashboard',
'back_text' => 'Go Back',
```

## Security Features

- **Path Traversal Protection**: Prevents access outside storage directory
- **File Type Restrictions**: Configurable forbidden extensions
- **CSRF Protection**: All forms protected with CSRF tokens
- **Folder Name Validation**: Prevents malicious folder names

## API Endpoints

The package provides these endpoints:

- `GET /file-manager` - File manager interface
- `POST /file-manager/upload` - Upload files
- `POST /file-manager/create-folder` - Create new folder
- `DELETE /file-manager/delete` - Delete files/folders

## Customization

### Custom Views

Publish the views and modify them:

```bash
php artisan vendor:publish --tag=file-manager-views
```

Views will be published to `resources/views/vendor/file-manager/`

### Custom Styling

The package uses Bootstrap 5 and Font Awesome icons. You can override styles by publishing the views and adding custom CSS.

### Extending the Controller

You can extend the FileManagerController to add custom functionality:

```php
<?php

namespace App\Http\Controllers;

use Ssh521\LaravelFileManager\Http\Controllers\FileManagerController as BaseController;

class CustomFileManagerController extends BaseController
{
    public function index(Request $request)
    {
        // Add custom logic
        return parent::index($request);
    }
}
```

## Requirements

- PHP ^8.2
- Laravel ^11.0|^12.0
- Bootstrap 5 (included via CDN)
- Font Awesome 6 (included via CDN)

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@example.com instead of using the issue tracker.

## Credits

- [SSH521](https://github.com/ssh521)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Changelog

### v1.0.0
- Initial release
- Basic file management functionality
- Bootstrap 5 interface
- Configurable options