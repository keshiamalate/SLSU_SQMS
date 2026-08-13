@extends('layouts.app')
@section('title', 'Edit Scholarship')

@push('styles')
<style>
    body { background:#f0f2f5; }
    .page-wrapper { display:flex; }
    .content-area { margin-left:240px; flex:1; min-height:100vh; }
    .topbar { height:60px; background:#fff; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; padding:0 1.5rem; position:sticky; top:0; z-index:50; }
    .form-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; padding:1.75rem; margin-bottom:1.25rem; }
    .form-card h6 { font-weight:700; color:var(--sm-navy); margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid #e5e7eb; }
    .form-label { font-weight:500; font-size:.9rem; color:#374151; }
    .form-control, .form-select { border-radius:8px; border:1.5px solid #d1d5db; }
    .form-control:focus, .form-select:focus { border-color:var(--sm-accent); box-shadow:0 0 0 3px rgba(37,99,235,.15); }
    .btn-save { background:var(--sm-navy); color:#fff; border:none; border-radius:8px; padding:.65rem 1.5rem; font-weight:600; }
    .btn-save:hover { background:var(--sm-accent); color:#fff; }
    .criteria-check { background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:.75rem 1rem; }
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
                <h5 class="fw-bold mb-0">Edit: {{ $scholarship->name }}</h5>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
                    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li style="font-size:.875rem;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Basic Info --}}
            <form method="POST" action="{{ route('admin.scholarships.update', $scholarship) }}">
                @csrf @method('PUT')
                <div class="form-card">
                    <h6><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Scholarship Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $scholarship->name) }}" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control"
                                value="{{ old('code', $scholarship->code) }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $scholarship->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Funding Source</label>
                            <input type="text" name="funding_source" class="form-control"
                                value="{{ old('funding_source', $scholarship->funding_source) }}" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Benefit Type</label>
                            <select name="benefit_type" class="form-select">
                                @foreach(['cash'=>'Cash','tuition_waiver'=>'Tuition Waiver','both'=>'Both','other'=>'Other'] as $v => $l)
                                    <option value="{{ $v }}" {{ $scholarship->benefit_type == $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allowance Amount (₱)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">₱</span>
                                <input type="number" name="monthly_allowance" class="form-control"
                                    value="{{ old('monthly_allowance', $scholarship->monthly_allowance) }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allowance Period</label>
                            <select name="allowance_period" class="form-select">
                                <option value="monthly"      {{ $scholarship->allowance_period == 'monthly'      ? 'selected' : '' }}>Per Month</option>
                                <option value="per_semester" {{ $scholarship->allowance_period == 'per_semester' ? 'selected' : '' }}>Per Semester</option>
                                <option value="per_year"     {{ $scholarship->allowance_period == 'per_year'     ? 'selected' : '' }}>Per Year</option>
                                <option value="one_time"     {{ $scholarship->allowance_period == 'one_time'     ? 'selected' : '' }}>One-Time</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Available Slots</label>
                            <input type="number" name="slots_available" class="form-control"
                                value="{{ old('slots_available', $scholarship->slots_available) }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Opening Date</label>
                            <input type="date" name="application_open_at" class="form-control"
                                value="{{ old('application_open_at', $scholarship->application_open_at) }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Closing Date</label>
                            <input type="date" name="application_close_at" class="form-control"
                                value="{{ old('application_close_at', $scholarship->application_close_at) }}" />
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="allows_concurrent" value="1" class="form-check-input" {{ $scholarship->allows_concurrent ? 'checked' : '' }} />
                                <label class="form-check-label" style="font-size:.875rem;">Allow concurrent scholarship</label>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-save mb-4">
                    <i class="bi bi-check-lg me-1"></i>Save Changes
                </button>
            </form>

            {{-- Eligibility Criteria --}}
            <form method="POST" action="{{ route('admin.scholarships.criteria.update', $scholarship) }}">
                @csrf @method('PATCH')
                <div class="form-card">
                    <h6><i class="bi bi-funnel me-2"></i>Eligibility Criteria</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">GPA Requirement
                                <span class="text-muted" style="font-size:.75rem;">(student must be ≥ this)</span>
                            </label>
                            <input type="number" name="min_gpa" step="0.01" min="1.00" max="3.00" class="form-control"
                                placeholder="e.g. 1.75"
                                value="{{ old('min_gpa', $criteria?->min_gpa) }}" />
                            <div class="form-text">e.g. 1.5 means student must have equal to or greater than this gpa.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Lowest Acceptable GPA
                                <span class="text-muted" style="font-size:.75rem;">(student must be ≥ this)</span>
                            </label>
                            <input type="number" name="max_gpa" step="0.01" min="1.00" max="3.00" class="form-control"
                                placeholder="e.g. 3.00"
                                value="{{ old('max_gpa', $criteria?->max_gpa) }}" />
                            <div class="form-text">Usually 3.00. Leave blank if no lower limit.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Max Annual Family Income (₱)</label>
                            <input type="number" name="max_annual_income" class="form-control"
                                placeholder="e.g. 400000"
                                value="{{ old('max_annual_income', $criteria?->max_annual_income) }}" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Required Year Levels</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach([1,2,3,4,5] as $yr)
                                    <div class="form-check">
                                        <input type="checkbox" name="required_year_levels[]" value="{{ $yr }}"
                                            class="form-check-input"
                                            {{ in_array($yr, $criteria?->required_year_levels ?? []) ? 'checked' : '' }} />
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
                                        <div class="criteria-check form-check">
                                            <input type="checkbox" name="{{ $field }}" value="1"
                                                class="form-check-input"
                                                {{ $criteria?->$field ? 'checked' : '' }} />
                                            <label class="form-check-label" style="font-size:.875rem;">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Additional Requirements</label>
                            <textarea name="additional_requirements" class="form-control" rows="2"
                                placeholder="Any other requirements not listed above...">{{ old('additional_requirements', $criteria?->additional_requirements) }}</textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-save">
                    <i class="bi bi-funnel me-1"></i>Save Criteria
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
