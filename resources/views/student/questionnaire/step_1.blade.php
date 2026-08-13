@extends('student.questionnaire.layout')

@section('q-content')
<div class="card-header-area">
    <div class="header-icon"><i class="bi bi-book"></i></div>
    <div>
        <p class="card-title">Academic Background</p>
        <p class="card-subtitle">Please provide your current academic performance and enrollment details.</p>
    </div>
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

<form method="POST" action="{{ route('student.questionnaire.store', ['step' => 1]) }}">
    @csrf

    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Degree / Course <span class="text-danger">*</span></label>
            <input type="text" name="course" class="form-control @error('course') is-invalid @enderror"
                placeholder="e.g. BS in Information Technology"
                value="{{ old('course', $saved['course'] ?? '') }}" />
            @error('course')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Year Level <span class="text-danger">*</span></label>
            <select name="year_level" class="form-select @error('year_level') is-invalid @enderror">
                <option value="">Select</option>
                @foreach([1,2,3,4,5] as $yr)
                    <option value="{{ $yr }}" {{ old('year_level', $saved['year_level'] ?? '') == $yr ? 'selected' : '' }}>
                        Year {{ $yr }}
                    </option>
                @endforeach
            </select>
            @error('year_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Current Weighted GPA <span class="text-danger">*</span></label>
            <input type="number" name="cumulative_gpa" step="0.01" min="1.00" max="3.00" class="form-control @error('cumulative_gpa') is-invalid @enderror" placeholder="1.00 – 3.00" value="{{ old('cumulative_gpa', $saved['cumulative_gpa'] ?? '') }}" />
            <div class="form-text">Enter your average from the last semester.</div>
            @error('cumulative_gpa')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Semester <span class="text-danger">*</span></label>
            <select name="semester" class="form-select @error('semester') is-invalid @enderror">
                <option value="">Select</option>
                <option value="1" {{ old('semester', $saved['semester'] ?? '') == 1 ? 'selected' : '' }}>1st Semester</option>
                <option value="2" {{ old('semester', $saved['semester'] ?? '') == 2 ? 'selected' : '' }}>2nd Semester</option>
            </select>
            @error('semester')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Enrollment Status <span class="text-danger">*</span></label>
            <select name="enrollment_status" class="form-select">
                @foreach(['regular','irregular','transferee'] as $status)
                    <option value="{{ $status }}" {{ old('enrollment_status', $saved['enrollment_status'] ?? 'regular') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Academic Honors</label>
            <input type="text" name="academic_honors" class="form-control"
                placeholder="e.g. Dean's Lister (optional)"
                value="{{ old('academic_honors', $saved['academic_honors'] ?? '') }}" />
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="has_failing_grade" value="1" class="form-check-input" id="failCheck"
                    {{ old('has_failing_grade', $saved['has_failing_grade'] ?? '') ? 'checked' : '' }} />
                <label class="form-check-label" for="failCheck" style="font-size:.875rem;">
                    I have a failing grade in one or more subjects this semester.
                </label>
            </div>
        </div>
    </div>

    <div class="q-footer">
        <span class="step-counter">STEP 1 OF 4</span>
        <button type="submit" class="btn-next">
            Continue <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
