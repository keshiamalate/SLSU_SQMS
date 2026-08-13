@extends('student.questionnaire.layout')

@section('q-content')
<div class="card-header-area">
    <div class="header-icon"><i class="bi bi-award"></i></div>
    <div>
        <p class="card-title">Special Qualifications</p>
        <p class="card-subtitle">Check all that apply. Declare any scholarships you currently receive.</p>
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

<form method="POST" action="{{ route('student.questionnaire.store', ['step' => 4]) }}">
    @csrf

    <div class="row g-3">

        {{-- Special category flags --}}
        <div class="col-12">
            <label class="form-label fw-semibold">Special Categories</label>
            <div class="border rounded-3 p-3" style="background:#f9fafb;">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_athlete" value="1" class="form-check-input" id="athlete"
                                {{ isset($saved['is_athlete']) ? 'checked' : '' }} />
                            <label class="form-check-label" for="athlete">
                                <i class="bi bi-trophy text-warning me-1"></i> Varsity Athlete
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_student_leader" value="1" class="form-check-input" id="leader"
                                {{ isset($saved['is_student_leader']) ? 'checked' : '' }} />
                            <label class="form-check-label" for="leader">
                                <i class="bi bi-people text-primary me-1"></i> Student Leader / Officer
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_pwd" value="1" class="form-check-input" id="pwd"
                                {{ isset($saved['is_pwd']) ? 'checked' : '' }} />
                            <label class="form-check-label" for="pwd">
                                <i class="bi bi-heart text-danger me-1"></i> Person with Disability (PWD)
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_indigenous_people" value="1" class="form-check-input" id="ip"
                                {{ isset($saved['is_indigenous_people']) ? 'checked' : '' }} />
                            <label class="form-check-label" for="ip">
                                <i class="bi bi-flag text-success me-1"></i> Indigenous People (IP)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Existing scholarships --}}
        <div class="col-12">
            <label class="form-label fw-semibold">Currently Received Scholarships</label>
            <p class="text-muted" style="font-size:.8rem;">
                Declare any scholarship you are currently receiving. Leave blank if none.
            </p>
            <div id="existingList">
                <div class="row g-2 mb-2 existing-row">
                    <div class="col-md-6">
                        <input type="text" name="existing_scholarships[0][name]" class="form-control"
                            placeholder="Scholarship name" />
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="existing_scholarships[0][granting_body]" class="form-control"
                            placeholder="Granting body (e.g. CHED)" />
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addRow()">
                <i class="bi bi-plus me-1"></i>Add another
            </button>
        </div>

        <div class="col-12">
            <div class="alert alert-warning rounded-3" style="font-size:.8rem;">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Failure to declare an existing scholarship may result in disqualification from concurrent awards.
            </div>
        </div>
    </div>

    <div class="q-footer">
        <a href="{{ route('student.questionnaire.show', ['step' => 3]) }}" class="btn-prev">
            <i class="bi bi-arrow-left"></i> Previous
        </a>
        <span class="step-counter">STEP 4 OF 4</span>
        <button type="submit" class="btn-next" style="background:#16a34a;">
            Submit Profile <i class="bi bi-check-lg"></i>
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let rowCount = 1;
    function addRow() {
        const list = document.getElementById('existingList');
        const div  = document.createElement('div');
        div.className = 'row g-2 mb-2 existing-row';
        div.innerHTML = `
            <div class="col-md-6">
                <input type="text" name="existing_scholarships[${rowCount}][name]" class="form-control" placeholder="Scholarship name" />
            </div>
            <div class="col-md-5">
                <input type="text" name="existing_scholarships[${rowCount}][granting_body]" class="form-control" placeholder="Granting body" />
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.existing-row').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>`;
        list.appendChild(div);
        rowCount++;
    }
</script>
@endpush
