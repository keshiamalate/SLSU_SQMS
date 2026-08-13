@extends('student.questionnaire.layout')

@section('q-content')
<div class="card-header-area">
    <div class="header-icon"><i class="bi bi-person"></i></div>
    <div>
        <p class="card-title">Personal Information</p>
        <p class="card-subtitle">Your residency details are used for LGU and government scholarship matching.</p>
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

<form method="POST" action="{{ route('student.questionnaire.store', ['step' => 3]) }}">
    @csrf

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Province of Residence <span class="text-danger">*</span></label>
            <input type="text" name="province_of_residence"
                class="form-control @error('province_of_residence') is-invalid @enderror"
                placeholder="e.g. Southern Leyte"
                value="{{ old('province_of_residence', $saved['province_of_residence'] ?? '') }}" />
            @error('province_of_residence')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Municipality / City <span class="text-danger">*</span></label>
            <input type="text" name="municipality_of_residence"
                class="form-control @error('municipality_of_residence') is-invalid @enderror"
                placeholder="e.g. Maasin City"
                value="{{ old('municipality_of_residence', $saved['municipality_of_residence'] ?? '') }}" />
            @error('municipality_of_residence')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="is_slsu_resident" value="1" class="form-check-input" id="slsuResident"
                    {{ old('is_slsu_resident', $saved['is_slsu_resident'] ?? '') ? 'checked' : '' }} />
                <label class="form-check-label" for="slsuResident" style="font-size:.875rem;">
                    I am a resident of the municipality where SLSU - Tomas Oppus is located.
                </label>
            </div>
        </div>
    </div>

    <div class="q-footer">
        <a href="{{ route('student.questionnaire.show', ['step' => 2]) }}" class="btn-prev">
            <i class="bi bi-arrow-left"></i> Previous
        </a>
        <span class="step-counter">STEP 3 OF 4</span>
        <button type="submit" class="btn-next">
            Continue <i class="bi bi-arrow-right"></i>
        </button>
    </div>
</form>
@endsection
