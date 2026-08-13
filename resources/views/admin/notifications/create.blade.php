@extends('layouts.app')
@section('title', 'Send Notification')

@push('styles')
    <style>
        body {
            background: #f0f2f5;
        }

        .page-wrapper {
            display: flex;
        }

        .content-area {
            margin-left: 240px;
            flex: 1;
            min-height: 100vh;
        }

        .topbar {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .form-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.75rem;
            margin-bottom: 1.25rem;
        }

        .form-card h6 {
            font-weight: 700;
            color: var(--sm-navy);
            margin-bottom: 1.25rem;
            padding-bottom: .75rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .form-label {
            font-weight: 500;
            font-size: .9rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1.5px solid #d1d5db;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--sm-accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .btn-send {
            background: var(--sm-navy);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .65rem 1.5rem;
            font-weight: 600;
        }

        .btn-send:hover {
            background: var(--sm-accent);
            color: #fff;
        }

        .template-btn {
            background: #f8fafc;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: .5rem 1rem;
            font-size: .825rem;
            cursor: pointer;
            transition: .15s;
        }

        .template-btn:hover {
            border-color: var(--sm-accent);
            color: var(--sm-accent);
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
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        @include('layouts.sidebar_admin')

        <div class="content-area">
            <div class="topbar">
                <span style="font-weight:700;">SmartMatch</span>
                <div class="d-flex align-items-center gap-2">
                    <div style="text-align:right;line-height:1.2;">
                        <div style="font-weight:600;font-size:.9rem;">{{ auth()->user()->full_name }}</div>
                        <div style="font-size:.75rem;color:#6b7280;">{{ auth()->user()->role->name }}</div>
                    </div>
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}</div>
                </div>
            </div>

            <div class="p-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <a href="{{ route('admin.notifications.index') }}" class="text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                    <span class="text-muted">/</span>
                    <h5 class="fw-bold mb-0">Compose Notification</h5>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                                <li style="font-size:.875rem;">{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.notifications.store') }}">
                    @csrf

                    {{-- Quick templates --}}
                    @if($templates->count())
                        <div class="form-card">
                            <h6><i class="bi bi-lightning me-2"></i>Quick Templates</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($templates as $tmpl)
                                    <button type="button" class="template-btn"
                                        onclick="applyTemplate('{{ addslashes($tmpl->subject) }}', '{{ addslashes($tmpl->body) }}')">
                                        {{ $tmpl->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="form-card">
                        <h6><i class="bi bi-people me-2"></i>Recipients &amp; Channel</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Send To <span class="text-danger">*</span></label>
                                <select name="recipient_group" class="form-select">
                                    @foreach($recipientGroups as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Channel <span class="text-danger">*</span></label>
                                <select name="channel" class="form-select">
                                    <option value="in_app">In-App Only</option>
                                    <option value="email">Email Only</option>
                                    <option value="both">Both (In-App + Email)</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Related Scholarship (Optional)</label>
                                <select name="scholarship_id" class="form-select">
                                    <option value="">— None —</option>
                                    @foreach($scholarships as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-card">
                        <h6><i class="bi bi-envelope me-2"></i>Message</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" id="notifSubject"
                                    class="form-control @error('subject') is-invalid @enderror"
                                    placeholder="e.g. Application Deadline Reminder" value="{{ old('subject') }}" />
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message Body <span class="text-danger">*</span></label>
                                <textarea name="body" id="notifBody" rows="6"
                                    class="form-control @error('body') is-invalid @enderror"
                                    placeholder="Write your message here...">{{ old('body') }}</textarea>
                                @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-send">
                            <i class="bi bi-send me-1"></i>Send Notification
                        </button>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function applyTemplate(subject, body) {
            document.getElementById('notifSubject').value = subject;
            document.getElementById('notifBody').value = body;
        }
    </script>
@endpush
