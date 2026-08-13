@extends('layouts.student')
@section('title', 'Upload Documents')

@push('styles')
    <style>
        body {
            background: #f0f2f5;
        }

        .page-wrapper {
            display: flex;
        }

        .content-area {
            flex: 1;
            min-height: 100vh;
            width: calc(100% - 240px);
            overflow-x: hidden;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--sm-navy);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: .875rem;
            flex-shrink: 0;
        }

        .main-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .main-card h6 {
            font-weight: 700;
            color: var(--sm-navy);
            padding-bottom: .75rem;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 1rem;
            font-size: .9rem;
        }

        .req-doc-item {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            padding: .6rem 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: .85rem;
            flex-wrap: wrap;
        }

        .req-doc-item:last-child {
            border-bottom: none;
        }

        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 1.5rem 1rem;
            text-align: center;
            background: #fafafa;
            transition: .2s;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: var(--sm-accent);
            background: #f0f4ff;
        }

        .doc-row {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            padding: .75rem 0;
            border-bottom: 1px solid #f3f4f6;
            flex-wrap: wrap;
        }

        .doc-row:last-child {
            border-bottom: none;
        }

        .doc-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .v-pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .v-verified {
            background: #f0fdf4;
            color: #16a34a;
        }

        .v-rejected {
            background: #fef2f2;
            color: #dc2626;
        }

        .status-pill {
            font-size: .7rem;
            font-weight: 600;
            padding: .25rem .65rem;
            border-radius: 20px;
            white-space: nowrap;
        }

        .form-control:focus {
            border-color: var(--sm-accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        @media (max-width: 768px) {
            .content-area {
                margin-left: 0;
                width: 100%;
            }

            .sidebar {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="p-3 p-md-4">

        @foreach(['success', 'error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show rounded-3 mb-3">
                    {{ session($msg) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endforeach

        {{-- Breadcrumb --}}
        <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
            <a href="{{ route('student.matching.index') }}" class="text-muted text-decoration-none"
                style="font-size:.875rem;">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
            <span class="text-muted">/</span>
            <h5 class="fw-bold mb-0" style="font-size:1rem;">Upload Documents</h5>
        </div>

        {{-- Scholarship info --}}
        <div class="main-card">
            <h6><i class="bi bi-award me-2"></i>{{ $application->scholarship->name }}</h6>
            <div class="d-flex flex-wrap gap-2" style="font-size:.825rem;">
                <span class="text-muted">
                    <i class="bi bi-tag me-1"></i>{{ $application->scholarship->category->name }}
                </span>
                <span class="text-muted">
                    <i class="bi bi-cash me-1"></i>{{ $application->scholarship->formatted_allowance }}
                </span>
                <span>
                    Status: <strong>{{ ucfirst(str_replace('_', ' ', $application->status)) }}</strong>
                </span>
            </div>
        </div>

        <div class="row g-3">

            {{-- Left column --}}
            <div class="col-12 col-lg-7">

                {{-- Required documents --}}
                @if($application->scholarship->requiredDocuments->count())
                    <div class="main-card">
                        <h6><i class="bi bi-list-check me-2"></i>Required Documents</h6>
                        @foreach($application->scholarship->requiredDocuments as $req)
                            <div class="req-doc-item">
                                <i class="bi bi-file-earmark-text text-muted"
                                    style="font-size:1.1rem;flex-shrink:0;margin-top:2px;"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $req->document_name }}</div>
                                    @if($req->description)
                                        <div class="text-muted" style="font-size:.775rem;">{{ $req->description }}</div>
                                    @endif
                                </div>
                                @if($req->is_mandatory)
                                    <span class="badge text-bg-danger" style="font-size:.65rem;">Required</span>
                                @else
                                    <span class="badge text-bg-secondary" style="font-size:.65rem;">Optional</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Upload form --}}
                <div class="main-card">
                    <h6><i class="bi bi-cloud-upload me-2"></i>Upload a Document</h6>

                    <form method="POST" action="{{ route('student.documents.store', $application) }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" style="font-size:.875rem;font-weight:600;">
                                Document Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="document_name"
                                class="form-control @error('document_name') is-invalid @enderror"
                                placeholder="e.g. Certificate of Indigency" value="{{ old('document_name') }}" required />
                            <div class="form-text">Describe what this document is.</div>
                            @error('document_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="font-size:.875rem;font-weight:600;">
                                File <span class="text-danger">*</span>
                            </label>
                            <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                                <i class="bi bi-cloud-arrow-up fs-3 text-muted d-block mb-1"></i>
                                <div class="fw-semibold" style="font-size:.875rem;">Click to browse</div>
                                <div class="text-muted mt-1" style="font-size:.775rem;">PDF, JPG, PNG — Max 5MB</div>
                                <div id="fileNameDisplay" class="mt-2 text-primary"
                                    style="font-size:.8rem;word-break:break-all;"></div>
                            </div>
                            <input type="file" id="fileInput" name="document" accept=".pdf,.jpg,.jpeg,.png" class="d-none"
                                onchange="showFileName(this)" />
                            @error('document')
                                <div class="text-danger mt-1" style="font-size:.825rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-sm text-white w-100 w-sm-auto"
                            style="background:var(--sm-navy);border-radius:8px;font-weight:600;padding:.6rem 1.5rem;">
                            <i class="bi bi-upload me-1"></i>Upload Document
                        </button>
                    </form>
                </div>

            </div>

            {{-- Right column --}}
            <div class="col-12 col-lg-5">

                {{-- Uploaded documents --}}
                <div class="main-card">
                    <h6>
                        <i class="bi bi-folder2-open me-2"></i>Uploaded Documents
                        <span class="badge text-bg-secondary ms-1">{{ $application->documents->count() }}</span>
                    </h6>

                    @forelse($application->documents as $doc)
                        <div class="doc-row">
                            <div class="doc-icon" style="background:#f0f4ff;flex-shrink:0;">
                                <i class="bi bi-{{ str_contains($doc->mime_type, 'pdf') ? 'file-earmark-pdf' : 'file-earmark-image' }}"
                                    style="color:var(--sm-accent);font-size:1rem;"></i>
                            </div>
                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="fw-semibold text-truncate" style="font-size:.825rem;max-width:100%;">
                                    {{ $doc->original_filename }}
                                </div>
                                <div class="text-muted" style="font-size:.72rem;">
                                    {{ number_format($doc->file_size_bytes / 1024, 1) }} KB
                                    &nbsp;•&nbsp;
                                    {{ \Carbon\Carbon::parse($doc->uploaded_at)->diffForHumans() }}
                                </div>
                                <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                    @php
                                        $vc = match ($doc->verification_status) {
                                            'verified' => 'v-verified',
                                            'rejected' => 'v-rejected',
                                            default => 'v-pending',
                                        };
                                    @endphp
                                    <span class="status-pill {{ $vc }}">
                                        {{ ucfirst($doc->verification_status) }}
                                    </span>
                                    <a href="{{ route('student.documents.download', $doc) }}"
                                        class="btn btn-sm btn-outline-secondary py-0 px-2 mt-1" style="font-size:.7rem;">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    @if($doc->verification_status !== 'verified')
                                        <form method="POST" action="{{ route('student.documents.destroy', $doc) }}"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"
                                                style="font-size:.7rem;" onclick="return confirm('Remove this document?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($doc->verification_status === 'rejected' && $doc->rejection_reason)
                            <div class="alert alert-danger py-2 px-3 mb-2 rounded-3" style="font-size:.78rem;">
                                <i class="bi bi-exclamation-circle me-1"></i>
                                Rejected: {{ $doc->rejection_reason }}
                            </div>
                        @endif

                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-folder2 fs-3 d-block mb-2"></i>
                            No documents uploaded yet.
                        </div>
                    @endforelse
                </div>

                {{-- Tips --}}
                <div class="main-card" style="background:#f8fafc;">
                    <h6 style="font-size:.85rem;">
                        <i class="bi bi-lightbulb me-2 text-warning"></i>Upload Tips
                    </h6>
                    <ul style="font-size:.78rem;color:#374151;padding-left:1.1rem;margin:0;line-height:1.8;">
                        <li>Make sure documents are clear and readable.</li>
                        <li>PDF is preferred for multi-page documents.</li>
                        <li>JPG or PNG for certificates and IDs.</li>
                        <li>Maximum file size is 5MB per document.</li>
                        <li>Rejected documents can be re-uploaded after correction.</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="text-center text-muted py-3" style="font-size:.72rem;border-top:1px solid #e5e7eb;">
        © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showFileName(input) {
            const display = document.getElementById('fileNameDisplay');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const size = (file.size / 1024).toFixed(1);
                display.innerHTML = '<i class="bi bi-file-earmark-check me-1"></i>' + file.name + ' (' + size + ' KB)';
            }
        }
    </script>
@endpush
