@extends('student.questionnaire.layout')

@section('q-content')
<div class="card-header-area">
    <div class="header-icon"><i class="bi bi-wallet2"></i></div>
    <div>
        <p class="card-title">Financial Information</p>
        <p class="card-subtitle">This information determines your eligibility for need-based scholarships.</p>
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

<form method="POST" action="{{ route('student.questionnaire.store', ['step' => 2]) }}">
    @csrf

    <div class="row g-3">
        <div class="col-12">
            <label class="form-label">Annual Family Gross Income <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white">₱</span>
                <input type="number" name="annual_family_income" class="form-control @error('annual_family_income') is-invalid @enderror"
                    placeholder="e.g. 120000"
                    value="{{ old('annual_family_income', $saved['annual_family_income'] ?? '') }}" />
            </div>
            <div class="form-text">Enter total combined income of all family members per year.</div>
            @error('annual_family_income')<div class="text-danger" style="font-size:.875rem;">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Number of Dependents <span class="text-danger">*</span></label>
            <input type="number" name="number_of_dependents" min="0" max="20"
                class="form-control @error('number_of_dependents') is-invalid @enderror"
                placeholder="0"
                value="{{ old('number_of_dependents', $saved['number_of_dependents'] ?? '') }}" />
            <div class="form-text">Include siblings still in school and elderly dependents.</div>
            @error('number_of_dependents')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="is_4ps_beneficiary" value="1" class="form-check-input" id="fourps"
                    {{ old('is_4ps_beneficiary', $saved['is_4ps_beneficiary'] ?? '') ? 'checked' : '' }} />
                <label class="form-check-label" for="fourps" style="font-size:.875rem;">
                    My family is a 4Ps (Pantawid Pamilyang Pilipino Program) beneficiary.
                </label>
            </div>
        </div>
        <div class="col-12">
            <div class="alert alert-info rounded-3" style="font-size:.8rem;">
                <i class="bi bi-info-circle me-2"></i>
                Your income information is encrypted and stored securely. It is only used for scholarship eligibility evaluation.
            </div>
        </div>
    </div>

    <div class="q-footer">
        <a href="{{ route('student.questionnaire.show', ['step' => 1]) }}" class="btn-prev">
            <i class="bi bi-arrow-left"></i> Previous
        </a>
        <span class="step-counter">STEP 2 OF 4</span>
        <button type="submit" class="btn-next">
            Continue <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
