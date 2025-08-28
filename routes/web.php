<?php

use Illuminate\Support\Facades\Route;
use Ssh521\LaravelFileManager\Http\Controllers\FileManagerController;

Route::prefix(config('file-manager.route.prefix', 'file-manager'))
    ->name(config('file-manager.route.name', 'file-manager') . '.')
    ->middleware(config('file-manager.route.middleware', ['web']))
    ->group(function () {
        Route::get('/', [FileManagerController::class, 'index'])->name('index');
        Route::post('/upload', [FileManagerController::class, 'upload'])->name('upload');
        Route::post('/create-folder', [FileManagerController::class, 'createFolder'])->name('create-folder');
        Route::delete('/delete', [FileManagerController::class, 'delete'])->name('delete');
    });