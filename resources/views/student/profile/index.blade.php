@extends('layouts.student')
@section('title', 'My Profile')

@push('styles')
<style>
    .profile-card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; padding:1.5rem; margin-bottom:1.25rem; }
    .profile-card h6 { font-weight:700; color:var(--sm-navy); padding-bottom:.75rem; border-bottom:1px solid #e5e7eb; margin-bottom:1.25rem; }
    .form-label { font-weight:500; font-size:.875rem; color:#374151; }
    .form-control, .form-select { border-radius:8px; border:1.5px solid #d1d5db; font-size:.875rem; }
    .form-control:focus { border-color:var(--sm-accent); box-shadow:0 0 0 3px rgba(37,99,235,.15); }
    .btn-save { background:var(--sm-navy); color:#fff; border:none; border-radius:8px; padding:.6rem 1.5rem; font-weight:600; font-size:.875rem; }
    .btn-save:hover { background:var(--sm-accent); color:#fff; }
    .info-label { font-size:.72rem; text-transform:uppercase; font-weight:600; color:#6b7280; margin-bottom:.2rem; }
    .info-value { font-size:.925rem; font-weight:500; color:#111; }
    .tab-nav .nav-link { color:#6b7280; font-weight:500; border:none; padding:.65rem 1.25rem; border-bottom:2px solid transparent; font-size:.875rem; }
    .tab-nav .nav-link.active { color:var(--sm-navy); border-bottom-color:var(--sm-navy); }
    .avatar-lg { width:72px; height:72px; border-radius:50%; background:var(--sm-navy); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.75rem; font-weight:700; flex-shrink:0; }
</style>
@endpush

@section('content')
<div class="p-3 p-md-4">

    @foreach(['success','error'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg === 'error' ? 'danger' : $msg }} alert-dismissible fade show rounded-3 mb-3">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    @if($errors->any())
        <div class="alert alert-danger rounded-3 mb-3">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)
                    <li style="font-size:.875rem;">{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <div class="avatar-lg">
            {{ strtoupper(substr($user->first_name,0,1).substr($user->last_name,0,1)) }}
        </div>
        <div>
            <h4 class="fw-bold mb-0">{{ $user->full_name }}</h4>
            <div class="text-muted" style="font-size:.875rem;">
                {{ $user->institutional_id }} &nbsp;•&nbsp; {{ $user->email }}
            </div>
            <span class="badge text-bg-primary mt-1" style="font-size:.72rem;">Student</span>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav tab-nav border-bottom mb-4">
        <li class="nav-item">
            <a class="nav-link {{ session('active_tab') !== 'security' ? 'active' : '' }}"
               href="#personal" data-bs-toggle="tab">
                <i class="bi bi-person me-1"></i>Personal Info
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ session('active_tab') === 'security' ? 'active' : '' }}"
               href="#security" data-bs-toggle="tab">
                <i class="bi bi-shield-lock me-1"></i>Security
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#academic" data-bs-toggle="tab">
                <i class="bi bi-mortarboard me-1"></i>Academic Profile
            </a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- Personal Info --}}
        <div class="tab-pane fade {{ session('active_tab') !== 'security' ? 'show active' : '' }}" id="personal">
            <div class="row g-3">
                <div class="col-lg-8">
                    <form method="POST" action="{{ route('student.profile.personal') }}">
                        @csrf @method('PATCH')
                        <div class="profile-card">
                            <h6><i class="bi bi-person me-2"></i>Personal Information</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" class="form-control"
                                        value="{{ old('first_name', $user->first_name) }}" required />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="middle_name" class="form-control"
                                        value="{{ old('middle_name', $user->middle_name) }}" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" class="form-control"
                                        value="{{ old('last_name', $user->last_name) }}" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email) }}" required />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Student ID</label>
                                    <input type="text" class="form-control"
                                        value="{{ $user->institutional_id }}" disabled />
                                    <div class="form-text">Student ID cannot be changed. Contact the scholarship office if there is an error.</div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn-save">
                            <i class="bi bi-check-lg me-1"></i>Save Changes
                        </button>
                    </form>
                </div>
                <div class="col-lg-4">
                    <div class="profile-card">
                        <h6><i class="bi bi-info-circle me-2"></i>Account Summary</h6>
                        <div class="mb-3">
                            <div class="info-label">Registered</div>
                            <div class="info-value">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Last Login</div>
                            <div class="info-value">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'N/A' }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Consent Status</div>
                            <div class="info-value">
                                @if($user->hasValidConsent())
                                    <span class="text-success"><i class="bi bi-check-circle me-1"></i>Signed</span>
                                @else
                                    <span class="text-danger"><i class="bi bi-x-circle me-1"></i>Not signed</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="info-label">Profile Status</div>
                            <div class="info-value">
                                @if($profile?->isComplete())
                                    <span class="text-success"><i class="bi bi-check-circle me-1"></i>Complete</span>
                                @else
                                    <span class="text-warning"><i class="bi bi-exclamation-circle me-1"></i>Incomplete</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Security --}}
        <div class="tab-pane fade {{ session('active_tab') === 'security' ? 'show active' : '' }}" id="security">
            <div class="row g-3">
                <div class="col-lg-6">
                    <form method="POST" action="{{ route('student.profile.password') }}">
                        @csrf @method('PATCH')
                        <div class="profile-card">
                            <h6><i class="bi bi-key me-2"></i>Change Password</h6>
                            <div class="mb-3">
                                <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                <input type="password" name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror" />
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Minimum 8 characters" />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" />
                            </div>
                            <div class="alert alert-info rounded-3" style="font-size:.8rem;">
                                <i class="bi bi-info-circle me-2"></i>
                                Use a strong password with at least 8 characters.
                            </div>
                        </div>
                        <button type="submit"
                                class="btn btn-sm btn-outline-danger px-4"
                                style="border-radius:8px;font-weight:600;">
                            <i class="bi bi-shield-check me-1"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Academic Profile --}}
        <div class="tab-pane fade" id="academic">
            @if($profile)
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="profile-card">
                            <h6><i class="bi bi-mortarboard me-2"></i>Academic Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-label">Course / Degree</div>
                                    <div class="info-value">{{ $profile->course }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Year Level</div>
                                    <div class="info-value">Year {{ $profile->year_level }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Semester</div>
                                    <div class="info-value">
                                        {{ $profile->semester }}{{ $profile->semester == 1 ? 'st' : 'nd' }} Sem
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Cumulative GPA</div>
                                    <div class="info-value" style="font-size:1.1rem;font-weight:700;color:var(--sm-navy);">
                                        {{ $profile->cumulative_gpa }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Enrollment Status</div>
                                    <div class="info-value">{{ ucfirst($profile->enrollment_status) }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-label">Failing Grade</div>
                                    <div class="info-value">{{ $profile->has_failing_grade ? 'Yes' : 'No' }}</div>
                                </div>
                                @if($profile->academic_honors)
                                    <div class="col-12">
                                        <div class="info-label">Academic Honors</div>
                                        <div class="info-value">{{ $profile->academic_honors }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="profile-card">
                            <h6><i class="bi bi-wallet2 me-2"></i>Financial Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-label">Annual Family Income</div>
                                    <div class="info-value">₱{{ number_format($profile->annual_family_income) }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Income Bracket</div>
                                    <div class="info-value">
                                        {{ $profile->income_bracket }} —
                                        {{ \App\Models\StudentProfile::bracketLabel($profile->income_bracket) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-label">Dependents</div>
                                    <div class="info-value">{{ $profile->number_of_dependents }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">4Ps Beneficiary</div>
                                    <div class="info-value">{{ $profile->is_4ps_beneficiary ? 'Yes' : 'No' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="profile-card">
                            <h6><i class="bi bi-geo-alt me-2"></i>Personal &amp; Qualifications</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-label">Municipality</div>
                                    <div class="info-value">{{ $profile->municipality_of_residence }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Province</div>
                                    <div class="info-value">{{ $profile->province_of_residence }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="info-label">Special Categories</div>
                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        @if($profile->is_athlete)
                                            <span class="badge text-bg-primary">Varsity Athlete</span>
                                        @endif
                                        @if($profile->is_student_leader)
                                            <span class="badge text-bg-info">Student Leader</span>
                                        @endif
                                        @if($profile->is_pwd)
                                            <span class="badge text-bg-warning text-dark">PWD</span>
                                        @endif
                                        @if($profile->is_indigenous_people)
                                            <span class="badge text-bg-success">Indigenous People (IP)</span>
                                        @endif
                                        @if(!$profile->is_athlete && !$profile->is_student_leader && !$profile->is_pwd && !$profile->is_indigenous_people)
                                            <span class="text-muted" style="font-size:.875rem;">None declared</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="profile-card text-center" style="background:var(--sm-light);">
                            <i class="bi bi-pencil-square fs-2 mb-2" style="color:var(--sm-navy);"></i>
                            <h6 class="fw-bold">Need to update your profile?</h6>
                            <p class="text-muted" style="font-size:.825rem;">
                                To update your academic or financial information, re-run the questionnaire.
                            </p>
                            <a href="{{ route('student.questionnaire.show', ['step' => 1]) }}"
                               class="btn btn-sm text-white w-100"
                               style="background:var(--sm-navy);border-radius:8px;font-weight:600;">
                                <i class="bi bi-arrow-repeat me-1"></i>Update Questionnaire
                            </a>
                        </div>

                        @if($user->existingScholarships->count())
                            <div class="profile-card mt-3">
                                <h6><i class="bi bi-award me-2"></i>Declared Scholarships</h6>
                                @foreach($user->existingScholarships as $es)
                                    <div class="mb-2 pb-2 border-bottom">
                                        <div class="fw-semibold" style="font-size:.875rem;">{{ $es->scholarship_name }}</div>
                                        @if($es->granting_body)
                                            <div class="text-muted" style="font-size:.775rem;">{{ $es->granting_body }}</div>
                                        @endif
                                        @if($es->is_exclusive)
                                            <span class="badge text-bg-warning text-dark" style="font-size:.65rem;">Exclusive</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x fs-1 text-muted d-block mb-3"></i>
                    <h5 class="fw-bold">No academic profile yet</h5>
                    <p class="text-muted">Complete the questionnaire to fill your academic profile.</p>
                    <a href="{{ route('student.questionnaire.show', ['step' => 1]) }}"
                       class="btn text-white px-4"
                       style="background:var(--sm-navy);border-radius:8px;font-weight:600;">
                        <i class="bi bi-pencil-square me-1"></i>Fill Questionnaire
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
