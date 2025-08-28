<?php

namespace Ssh521\LaravelFileManager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class FileManagerController extends Controller
{
    private $storagePath;
    private $publicPath;

    public function __construct()
    {
        $this->storagePath = storage_path(config('file-manager.storage_path', 'app/public'));
        $this->publicPath = config('file-manager.public_path', 'storage');
    }

    public function index(Request $request)
    {
        $currentPath = $request->get('path', '');
        $fullPath = $this->storagePath . '/' . $currentPath;
        
        if (!File::exists($fullPath) || !File::isDirectory($fullPath)) {
            $fullPath = $this->storagePath;
            $currentPath = '';
        }

        $items = [];
        $files = File::allFiles($fullPath);
        $directories = File::directories($fullPath);

        // 디렉토리 추가
        foreach ($directories as $directory) {
            $relativePath = str_replace($this->storagePath . '/', '', $directory);
            $items[] = [
                'name' => basename($directory),
                'type' => 'directory',
                'path' => $relativePath,
                'size' => null,
                'modified' => File::lastModified($directory),
            ];
        }

        // 파일 추가
        foreach ($files as $file) {
            if (dirname($file->getPathname()) === $fullPath) {
                $relativePath = str_replace($this->storagePath . '/', '', $file->getPathname());
                $items[] = [
                    'name' => $file->getFilename(),
                    'type' => 'file',
                    'path' => $relativePath,
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                    'extension' => $file->getExtension(),
                    'url' => asset($this->publicPath . '/' . $relativePath),
                    'is_image' => in_array(strtolower($file->getExtension()), 
                        config('file-manager.image_extensions', ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']))
                ];
            }
        }

        // 정렬: 디렉토리 먼저, 그 다음 파일
        usort($items, function ($a, $b) {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'directory' ? -1 : 1;
            }
            return strcasecmp($a['name'], $b['name']);
        });

        $breadcrumbs = $this->generateBreadcrumbs($currentPath);

        return view('file-manager::index', compact('items', 'currentPath', 'breadcrumbs'));
    }

    public function upload(Request $request)
    {
        $maxFileSize = config('file-manager.max_file_size', 10240); // KB
        
        $request->validate([
            'files.*' => "required|file|max:{$maxFileSize}",
            'path' => 'nullable|string'
        ]);

        $uploadPath = $request->get('path', '');
        $uploadedFiles = [];
        $allowedMimes = config('file-manager.allowed_mimes', []);

        foreach ($request->file('files') as $file) {
            // Check allowed MIME types if configured
            if (!empty($allowedMimes) && !in_array($file->getMimeType(), $allowedMimes)) {
                continue;
            }

            $filename = $this->generateUniqueFilename($file->getClientOriginalName(), $uploadPath);
            $file->storeAs($uploadPath, $filename, 'public');
            $uploadedFiles[] = $filename;
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles) . '개 파일이 업로드되었습니다.',
            'files' => $uploadedFiles
        ]);
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9\-_\s]+$/',
            'path' => 'nullable|string'
        ]);

        $currentPath = $request->get('path', '');
        $folderName = $request->get('name');
        $fullPath = $this->storagePath . '/' . $currentPath . '/' . $folderName;

        if (File::exists($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => '이미 존재하는 폴더명입니다.'
            ]);
        }

        File::makeDirectory($fullPath, 0755, true);

        return response()->json([
            'success' => true,
            'message' => '폴더가 생성되었습니다.'
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);

        $path = $request->get('path');
        $fullPath = $this->storagePath . '/' . $path;

        // Security check: prevent directory traversal
        if (!$this->isPathSafe($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => '허용되지 않는 경로입니다.'
            ]);
        }

        if (!File::exists($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => '파일 또는 폴더를 찾을 수 없습니다.'
            ]);
        }

        if (File::isDirectory($fullPath)) {
            File::deleteDirectory($fullPath);
            $message = '폴더가 삭제되었습니다.';
        } else {
            File::delete($fullPath);
            $message = '파일이 삭제되었습니다.';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    private function generateBreadcrumbs($path)
    {
        $breadcrumbs = [['name' => config('file-manager.root_name', 'Storage'), 'path' => '']];
        
        if (empty($path)) {
            return $breadcrumbs;
        }

        $segments = explode('/', $path);
        $currentPath = '';

        foreach ($segments as $segment) {
            if (!empty($segment)) {
                $currentPath .= ($currentPath ? '/' : '') . $segment;
                $breadcrumbs[] = ['name' => $segment, 'path' => $currentPath];
            }
        }

        return $breadcrumbs;
    }

    private function generateUniqueFilename($originalName, $path)
    {
        $fullPath = $this->storagePath . '/' . $path . '/' . $originalName;
        
        if (!File::exists($fullPath)) {
            return $originalName;
        }

        $info = pathinfo($originalName);
        $basename = $info['filename'];
        $extension = isset($info['extension']) ? '.' . $info['extension'] : '';
        $counter = 1;

        do {
            $newFilename = $basename . '_' . $counter . $extension;
            $fullPath = $this->storagePath . '/' . $path . '/' . $newFilename;
            $counter++;
        } while (File::exists($fullPath));

        return $newFilename;
    }

    private function isPathSafe($path)
    {
        $realPath = realpath($path);
        $storagePath = realpath($this->storagePath);
        
        return $realPath && $storagePath && str_starts_with($realPath, $storagePath);
    }
}