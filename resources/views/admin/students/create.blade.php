@extends('layouts.app')
@section('title', 'Add Student')

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

        .form-control:focus {
            border-color: var(--sm-accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .btn-save {
            background: var(--sm-navy);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .65rem 1.5rem;
            font-weight: 600;
        }

        .btn-save:hover {
            background: var(--sm-accent);
            color: #fff;
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
                    <a href="{{ route('admin.students.index') }}" class="text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                    <span class="text-muted">/</span>
                    <h5 class="fw-bold mb-0">Add New Student</h5>
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

                <form method="POST" action="{{ route('admin.students.store') }}">
                    @csrf
                    <div class="form-card">
                        <h6><i class="bi bi-person me-2"></i>Student Account Details</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}"
                                    required />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" class="form-control"
                                    value="{{ old('middle_name') }}" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}"
                                    required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Student ID <span class="text-danger">*</span></label>
                                <input type="text" name="institutional_id" class="form-control"
                                    placeholder="e.g. 24-00001" value="{{ old('institutional_id') }}" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required />
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-save">
                            <i class="bi bi-person-plus me-1"></i>Create Student Account
                        </button>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
