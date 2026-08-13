@extends('layouts.app')
@section('title', 'Add Scholarship')

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
            color: #374151;
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
                    <a href="{{ route('admin.scholarships.index') }}" class="text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                    <span class="text-muted">/</span>
                    <h5 class="fw-bold mb-0">Add New Scholarship</h5>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li style="font-size:.875rem;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.scholarships.store') }}">
                    @csrf

                    {{-- Basic Info --}}
                    <div class="form-card">
                        <h6><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Scholarship Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="e.g. CHED Tulong Dunong Program" value="{{ old('name') }}" />
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Scholarship Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                    placeholder="e.g. CHED-TDP-2025" value="{{ old('code') }}" />
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">Select category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Funding Source</label>
                                <input type="text" name="funding_source" class="form-control"
                                    placeholder="e.g. CHED Regional Office" value="{{ old('funding_source') }}" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"
                                    placeholder="Brief description of this scholarship program...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Benefit Details --}}
                    <div class="form-card">
                        <h6><i class="bi bi-cash me-2"></i>Benefit Details</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Benefit Type <span class="text-danger">*</span></label>
                                <select name="benefit_type" class="form-select">
                                    @foreach(['cash' => 'Cash Stipend', 'tuition_waiver' => 'Tuition Waiver', 'both' => 'Both', 'other' => 'Other'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('benefit_type') == $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Allowance Amount (₱)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">₱</span>
                                    <input type="number" name="monthly_allowance" class="form-control"
                                        placeholder="e.g. 10000" value="{{ old('monthly_allowance') }}" />
                                </div>
                                <div class="form-text">Leave blank if non-cash benefit.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Allowance Period</label>
                                <select name="allowance_period" class="form-select">
                                    <option value="monthly" {{ old('allowance_period') == 'monthly' ? 'selected' : '' }}>Per
                                        Month</option>
                                    <option value="per_semester" {{ old('allowance_period') == 'per_semester' ? 'selected' : '' }}>Per Semester</option>
                                    <option value="per_year" {{ old('allowance_period') == 'per_year' ? 'selected' : '' }}>Per
                                        Year</option>
                                    <option value="one_time" {{ old('allowance_period') == 'one_time' ? 'selected' : '' }}>
                                        One-Time</option>
                                </select>
                                <div class="form-text">e.g. TES gives ₱10,000 per semester.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Additional Benefit Details</label>
                                <input type="text" name="benefit_details" class="form-control"
                                    placeholder="e.g. Includes book allowance and transportation"
                                    value="{{ old('benefit_details') }}" />
                            </div>
                        </div>
                    </div>

                    {{-- Application Window --}}
                    <div class="form-card">
                        <h6><i class="bi bi-calendar3 me-2"></i>Application Window &amp; Slots</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Opening Date</label>
                                <input type="date" name="application_open_at" class="form-control"
                                    value="{{ old('application_open_at') }}" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Closing Date</label>
                                <input type="date" name="application_close_at" class="form-control"
                                    value="{{ old('application_close_at') }}" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Available Slots</label>
                                <input type="number" name="slots_available" class="form-control" min="1"
                                    placeholder="Leave blank = unlimited" value="{{ old('slots_available') }}" />
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="allows_concurrent" value="1" class="form-check-input"
                                        id="concurrent" {{ old('allows_concurrent') ? 'checked' : '' }} />
                                    <label class="form-check-label" for="concurrent" style="font-size:.875rem;">
                                        Allow concurrent scholarship (student can hold this along with another scholarship)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Eligibility Criteria --}}
                    <div class="form-card">
                        <h6><i class="bi bi-funnel me-2"></i>Eligibility Criteria</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">GPA Requirement
                                    <span class="text-muted" style="font-size:.75rem;">(student must be ≤ this)</span>
                                </label>
                                <input type="number" name="min_gpa" step="0.01" min="1.00" max="3.00"
                                    class="form-control" placeholder="e.g. 1.75"
                                    value="{{ old('min_gpa') }}" />
                                <div class="form-text">e.g. 1.75 means student must have 1.75 or better.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Lowest Acceptable GPA
                                    <span class="text-muted" style="font-size:.75rem;">(student must be ≥ this)</span>
                                </label>
                                <input type="number" name="max_gpa" step="0.01" min="1.00" max="3.00"
                                    class="form-control" placeholder="e.g. 3.00"
                                    value="{{ old('max_gpa') }}" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Max Annual Family Income (₱)</label>
                                <input type="number" name="max_annual_income" class="form-control"
                                    placeholder="e.g. 400000"
                                    value="{{ old('max_annual_income') }}" />
                            </div>
                            <div class="col-12">
                                <label class="form-label">Required Year Levels</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach([1,2,3,4,5] as $yr)
                                        <div class="form-check">
                                            <input type="checkbox" name="required_year_levels[]"
                                                value="{{ $yr }}" class="form-check-input"
                                                {{ is_array(old('required_year_levels')) && in_array($yr, old('required_year_levels')) ? 'checked' : '' }} />
                                            <label class="form-check-label">Year {{ $yr }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Special Requirements</label>
                                <div class="row g-2">
                                    @foreach([
                                        'no_failing_grade'                => 'No failing grade',
                                        'requires_4ps'                    => '4Ps Beneficiary',
                                        'requires_slsu_residency'         => 'SLSU Resident',
                                        'requires_athlete'                => 'Varsity Athlete',
                                        'requires_student_leader'         => 'Student Leader',
                                        'requires_pwd'                    => 'Person with Disability',
                                        'requires_indigenous_people'      => 'Indigenous People (IP)',
                                        'requires_philippine_citizenship' => 'Philippine Citizen',
                                        'requires_active_enrollment'      => 'Actively Enrolled',
                                    ] as $field => $label)
                                        <div class="col-md-4">
                                            <div class="form-check" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:.6rem 1rem;">
                                                <input type="checkbox" name="{{ $field }}" value="1"
                                                    class="form-check-input"
                                                    {{ old($field) ? 'checked' : '' }} />
                                                <label class="form-check-label" style="font-size:.875rem;">{{ $label }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Additional Requirements</label>
                                <textarea name="additional_requirements" class="form-control" rows="2"
                                    placeholder="Any other requirements not listed above...">{{ old('additional_requirements') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-save">
                            <i class="bi bi-check-lg me-1"></i>Save Scholarship
                        </button>
                        <a href="{{ route('admin.scholarships.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
