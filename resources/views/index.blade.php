<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>File Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .file-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .file-item:hover {
            background-color: rgb(248 250 252);
        }
        .file-item.selected {
            background-color: rgb(219 234 254);
        }
        .upload-area {
            border: 2px dashed rgb(209 213 219);
            transition: all 0.3s;
        }
        .upload-area.dragover {
            border-color: rgb(59 130 246);
            background-color: rgb(219 234 254);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-folder-open text-blue-600 mr-2"></i>
                        {{ config('file-manager.title', 'File Manager') }}
                    </h2>
                    @if(config('file-manager.back_route'))
                        <div>
                            <a href="{{ route(config('file-manager.back_route')) }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <i class="fas fa-arrow-left mr-2"></i> {{ config('file-manager.back_text', '돌아가기') }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Breadcrumb -->
                <nav class="mb-6" aria-label="breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm">
                        @foreach($breadcrumbs as $breadcrumb)
                            @if($loop->last)
                                <li class="text-gray-500">{{ $breadcrumb['name'] }}</li>
                            @else
                                <li class="flex items-center">
                                    <a href="{{ route(config('file-manager.route.name', 'file-manager') . '.index', ['path' => $breadcrumb['path']]) }}" 
                                       class="text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $breadcrumb['name'] }}
                                    </a>
                                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <!-- Upload Area -->
                        <div class="upload-area mb-6 rounded-lg p-8 text-center bg-gray-50" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                            <p class="mb-4 text-gray-600">파일을 드래그하여 업로드하거나 클릭하여 선택하세요</p>
                            <input type="file" id="fileInput" multiple class="hidden">
                            <button class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" onclick="document.getElementById('fileInput').click();">
                                <i class="fas fa-plus mr-2"></i> 파일 선택
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mb-6 space-x-3">
                            <button class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" onclick="showCreateFolderModal()">
                                <i class="fas fa-folder-plus mr-2"></i> 폴더 생성
                            </button>
                            <button class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed" onclick="deleteSelected()" id="deleteBtn" disabled>
                                <i class="fas fa-trash mr-2"></i> 삭제
                            </button>
                        </div>

                        <!-- File List -->
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="w-12 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">이름</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">크기</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">수정일</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">작업</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @if(!empty($currentPath))
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4"></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route(config('file-manager.route.name', 'file-manager') . '.index', ['path' => dirname($currentPath) === '.' ? '' : dirname($currentPath)]) }}" class="text-blue-600 hover:text-blue-800">
                                                        <i class="fas fa-level-up-alt text-gray-400 mr-2"></i> ..
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">-</td>
                                                <td class="px-6 py-4 text-sm text-gray-500">-</td>
                                                <td class="px-6 py-4 text-sm text-gray-500">-</td>
                                            </tr>
                                        @endif
                                    
                                        @forelse($items as $item)
                                            <tr class="file-item hover:bg-gray-50" data-path="{{ $item['path'] }}" data-type="{{ $item['type'] }}">
                                                <td class="px-6 py-4">
                                                    <input type="checkbox" class="file-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $item['path'] }}">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($item['type'] === 'directory')
                                                        <a href="{{ route(config('file-manager.route.name', 'file-manager') . '.index', ['path' => $item['path']]) }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                                                            <i class="fas fa-folder text-yellow-500 mr-2"></i> {{ $item['name'] }}
                                                        </a>
                                                    @else
                                                        <span class="flex items-center cursor-pointer" onclick="selectFile('{{ $item['path'] }}', '{{ $item['name'] }}', '{{ $item['url'] ?? '' }}', {{ $item['is_image'] ? 'true' : 'false' }})">
                                                            @if($item['is_image'])
                                                                <i class="fas fa-image text-green-500 mr-2"></i>
                                                            @else
                                                                <i class="fas fa-file text-gray-400 mr-2"></i>
                                                            @endif
                                                            {{ $item['name'] }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    @if($item['type'] === 'file')
                                                        {{ number_format($item['size'] / 1024, 1) }} KB
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">{{ date('Y-m-d H:i', $item['modified']) }}</td>
                                                <td class="px-6 py-4 text-sm space-x-2">
                                                    @if($item['type'] === 'file' && isset($item['url']))
                                                        <a href="{{ $item['url'] }}" target="_blank" class="inline-flex items-center px-2 py-1 border border-blue-300 rounded text-xs text-blue-700 bg-blue-50 hover:bg-blue-100">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                    @endif
                                                    <button class="inline-flex items-center px-2 py-1 border border-red-300 rounded text-xs text-red-700 bg-red-50 hover:bg-red-100" onclick="deleteItem('{{ $item['path'] }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">폴더가 비어있습니다</td>
                                            </tr>
                                        @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <!-- File Preview -->
                        <div class="bg-white rounded-lg shadow">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-medium text-gray-900">미리보기</h3>
                            </div>
                            <div class="p-6" id="previewArea">
                                <p class="text-gray-500 text-center">파일을 선택하면 미리보기가 표시됩니다</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div id="createFolderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">폴더 생성</h3>
                <button type="button" class="float-right -mt-1 text-gray-400 hover:text-gray-600" onclick="hideCreateFolderModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label for="folderName" class="block text-sm font-medium text-gray-700 mb-2">폴더명</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="folderName" placeholder="폴더명을 입력하세요">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50" onclick="hideCreateFolderModal()">취소</button>
                <button type="button" class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700" onclick="createFolder()">생성</button>
            </div>
        </div>
    </div>

    <script>
        const currentPath = '{{ $currentPath }}';
        let selectedFiles = [];

        // CSRF 토큰 설정
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // 드래그 앤 드롭 처리
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        // 파일 업로드 처리
        function handleFiles(files) {
            const formData = new FormData();
            
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }
            formData.append('path', currentPath);
            formData.append('_token', csrfToken);

            fetch('{{ route(config('file-manager.route.name', 'file-manager') . '.upload') }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('업로드 실패: ' + data.message);
                }
            })
            .catch(error => {
                alert('업로드 중 오류가 발생했습니다.');
                console.error('Error:', error);
            });
        }

        // 체크박스 처리
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.file-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateDeleteButton();
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('file-checkbox')) {
                updateDeleteButton();
            }
        });

        function updateDeleteButton() {
            const checkedBoxes = document.querySelectorAll('.file-checkbox:checked');
            document.getElementById('deleteBtn').disabled = checkedBoxes.length === 0;
        }

        // 폴더 생성
        function showCreateFolderModal() {
            document.getElementById('createFolderModal').classList.remove('hidden');
            document.getElementById('createFolderModal').classList.add('flex');
        }

        function hideCreateFolderModal() {
            document.getElementById('createFolderModal').classList.add('hidden');
            document.getElementById('createFolderModal').classList.remove('flex');
            document.getElementById('folderName').value = '';
        }

        function createFolder() {
            const folderName = document.getElementById('folderName').value.trim();
            
            if (!folderName) {
                alert('폴더명을 입력하세요.');
                return;
            }

            fetch('{{ route(config('file-manager.route.name', 'file-manager') . '.create-folder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    name: folderName,
                    path: currentPath
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('폴더 생성 실패: ' + data.message);
                }
                hideCreateFolderModal();
            });
        }

        // 파일/폴더 삭제
        function deleteItem(path) {
            if (!confirm('정말 삭제하시겠습니까?')) return;

            fetch('{{ route(config('file-manager.route.name', 'file-manager') . '.delete') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ path: path })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('삭제 실패: ' + data.message);
                }
            });
        }

        function deleteSelected() {
            const checkedBoxes = document.querySelectorAll('.file-checkbox:checked');
            if (checkedBoxes.length === 0) return;

            if (!confirm(`선택된 ${checkedBoxes.length}개 항목을 삭제하시겠습니까?`)) return;

            const promises = Array.from(checkedBoxes).map(checkbox => {
                return fetch('{{ route(config('file-manager.route.name', 'file-manager') . '.delete') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ path: checkbox.value })
                });
            });

            Promise.all(promises).then(() => {
                location.reload();
            });
        }

        // 파일 미리보기
        function selectFile(path, name, url, isImage) {
            const previewArea = document.getElementById('previewArea');
            
            let content = `<h4 class="text-lg font-semibold text-gray-900 mb-3">${name}</h4>`;
            
            if (isImage && url) {
                content += `<img src="${url}" class="w-full max-h-48 object-contain rounded-lg" alt="${name}">`;
            }
            
            content += `
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">경로: <span class="font-mono">${path}</span></p>
                    ${url ? `<p class="text-sm text-gray-600">URL: <a href="${url}" target="_blank" class="text-blue-600 hover:text-blue-800 underline break-all">${url}</a></p>` : ''}
                </div>
            `;
            
            previewArea.innerHTML = content;
        }
    </script>
</body>
</html>