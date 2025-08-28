<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>File Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .file-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .file-item:hover {
            background-color: #f8f9fa;
        }
        .file-item.selected {
            background-color: #e3f2fd;
        }
        .preview-image {
            max-width: 100%;
            max-height: 200px;
        }
        .breadcrumb-item a {
            text-decoration: none;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            padding: 2rem;
            text-align: center;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }
        .upload-area.dragover {
            border-color: #0d6efd;
            background-color: #e3f2fd;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2><i class="fas fa-folder-open"></i> {{ config('file-manager.title', 'File Manager') }}</h2>
                    @if(config('file-manager.back_route'))
                        <div>
                            <a href="{{ route(config('file-manager.back_route')) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> {{ config('file-manager.back_text', '돌아가기') }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        @foreach($breadcrumbs as $breadcrumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ route(config('file-manager.route.name', 'file-manager') . '.index', ['path' => $breadcrumb['path']]) }}">
                                        {{ $breadcrumb['name'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>

                <div class="row">
                    <div class="col-md-8">
                        <!-- Upload Area -->
                        <div class="upload-area mb-4" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="mb-2">파일을 드래그하여 업로드하거나 클릭하여 선택하세요</p>
                            <input type="file" id="fileInput" multiple style="display: none;">
                            <button class="btn btn-primary" onclick="document.getElementById('fileInput').click();">
                                <i class="fas fa-plus"></i> 파일 선택
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mb-3">
                            <button class="btn btn-success" onclick="showCreateFolderModal()">
                                <i class="fas fa-folder-plus"></i> 폴더 생성
                            </button>
                            <button class="btn btn-danger" onclick="deleteSelected()" id="deleteBtn" disabled>
                                <i class="fas fa-trash"></i> 삭제
                            </button>
                        </div>

                        <!-- File List -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>이름</th>
                                        <th>크기</th>
                                        <th>수정일</th>
                                        <th>작업</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($currentPath))
                                        <tr>
                                            <td></td>
                                            <td>
                                                <a href="{{ route(config('file-manager.route.name', 'file-manager') . '.index', ['path' => dirname($currentPath) === '.' ? '' : dirname($currentPath)]) }}" class="text-decoration-none">
                                                    <i class="fas fa-level-up-alt text-secondary"></i> ..
                                                </a>
                                            </td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                    @endif
                                    
                                    @forelse($items as $item)
                                        <tr class="file-item" data-path="{{ $item['path'] }}" data-type="{{ $item['type'] }}">
                                            <td>
                                                <input type="checkbox" class="file-checkbox" value="{{ $item['path'] }}">
                                            </td>
                                            <td>
                                                @if($item['type'] === 'directory')
                                                    <a href="{{ route(config('file-manager.route.name', 'file-manager') . '.index', ['path' => $item['path']]) }}" class="text-decoration-none">
                                                        <i class="fas fa-folder text-warning"></i> {{ $item['name'] }}
                                                    </a>
                                                @else
                                                    <span onclick="selectFile('{{ $item['path'] }}', '{{ $item['name'] }}', '{{ $item['url'] ?? '' }}', {{ $item['is_image'] ? 'true' : 'false' }})">
                                                        @if($item['is_image'])
                                                            <i class="fas fa-image text-success"></i>
                                                        @else
                                                            <i class="fas fa-file text-secondary"></i>
                                                        @endif
                                                        {{ $item['name'] }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['type'] === 'file')
                                                    {{ number_format($item['size'] / 1024, 1) }} KB
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ date('Y-m-d H:i', $item['modified']) }}</td>
                                            <td>
                                                @if($item['type'] === 'file' && isset($item['url']))
                                                    <a href="{{ $item['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                @endif
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteItem('{{ $item['path'] }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">폴더가 비어있습니다</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- File Preview -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">미리보기</h5>
                            </div>
                            <div class="card-body" id="previewArea">
                                <p class="text-muted text-center">파일을 선택하면 미리보기가 표시됩니다</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div class="modal fade" id="createFolderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">폴더 생성</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="folderName" class="form-label">폴더명</label>
                        <input type="text" class="form-control" id="folderName" placeholder="폴더명을 입력하세요">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">취소</button>
                    <button type="button" class="btn btn-primary" onclick="createFolder()">생성</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
            new bootstrap.Modal(document.getElementById('createFolderModal')).show();
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
                bootstrap.Modal.getInstance(document.getElementById('createFolderModal')).hide();
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
            
            let content = `<h6>${name}</h6>`;
            
            if (isImage && url) {
                content += `<img src="${url}" class="preview-image" alt="${name}">`;
            }
            
            content += `
                <div class="mt-3">
                    <small class="text-muted">경로: ${path}</small>
                    ${url ? `<br><small class="text-muted">URL: <a href="${url}" target="_blank">${url}</a></small>` : ''}
                </div>
            `;
            
            previewArea.innerHTML = content;
        }
    </script>
</body>
</html>