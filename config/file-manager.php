<?php

return [

    /*
    |--------------------------------------------------------------------------
    | File Manager Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Laravel File Manager
    | package. You can customize the behavior and appearance of the file manager.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the storage paths and settings for file management.
    |
    */
    'storage_path' => 'app/public',
    'public_path' => 'storage',

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routing options for the file manager.
    |
    */
    'route' => [
        'prefix' => 'file-manager',
        'name' => 'file-manager',
        'middleware' => ['web'],
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the user interface options.
    |
    */
    'title' => 'File Manager',
    'root_name' => 'Storage',
    'back_route' => null, // e.g., 'admin.dashboard'
    'back_text' => '돌아가기',

    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configure file upload restrictions and behavior.
    |
    */
    'max_file_size' => 10240, // KB (10MB)
    'allowed_mimes' => [
        // Leave empty to allow all file types
        // Example: ['image/jpeg', 'image/png', 'application/pdf']
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Extensions
    |--------------------------------------------------------------------------
    |
    | Define which file extensions should be treated as images for preview.
    |
    */
    'image_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Configure security options for file management.
    |
    */
    'forbidden_extensions' => ['php', 'js', 'html', 'htm'],
    'folder_name_pattern' => '/^[a-zA-Z0-9\-_\s]+$/',

    /*
    |--------------------------------------------------------------------------
    | Feature Toggles
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features of the file manager.
    |
    */
    'features' => [
        'upload' => true,
        'create_folder' => true,
        'delete' => true,
        'rename' => false, // Not implemented yet
        'move' => false,   // Not implemented yet
    ],

];