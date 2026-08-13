@extends('layouts.app')
@section('title', 'Data Privacy Consent')

@push('styles')
    <style>
        body {
            background: #1a1a2e;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem 1rem;
        }

        .consent-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            width: 100%;
            max-width: 560px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .ra-badge {
            background: #f0f4ff;
            border-radius: 10px;
            padding: .75rem 1rem;
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            margin-bottom: 1.5rem;
            font-size: .875rem;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-weight: 600;
            margin-bottom: .5rem;
        }

        .section-title i {
            color: #10b981;
        }

        .consent-list {
            padding-left: 1rem;
        }

        .consent-list li {
            font-size: .875rem;
            color: #4b5563;
            margin-bottom: .25rem;
        }

        .check-area {
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 1rem;
            margin: 1.5rem 0 1rem;
        }

        .btn-proceed {
            background: var(--sm-navy);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: .8rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
        }

        .btn-proceed:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .btn-proceed:not(:disabled):hover {
            background: var(--sm-accent);
        }

        @media (max-width: 480px) {
            .consent-card {
                padding: 1.25rem;
                border-radius: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="consent-card">
        <h4 class="text-center fw-bold" style="color:var(--sm-navy);">Data Privacy Consent</h4>
        <p class="text-center text-muted mb-3" style="font-size:.875rem;">SmartMatch Scholarship Qualification &amp;
            Matching System</p>

        <div class="ra-badge">
            <i class="bi bi-info-circle-fill text-primary mt-1"></i>
            <span><strong>Republic Act No. 10173</strong> — In compliance with the Data Privacy Act of 2012, SLSU is
                committed to protecting your personal information.</span>
        </div>

        <p class="text-muted mb-3" style="font-size:.875rem;font-style:italic;">
            By using SmartMatch, you authorize SLSU to collect, process, and store your personal and academic data for
            scholarship evaluation and matching.
        </p>

        <div class="mb-3">
            <div class="section-title"><i class="bi bi-check-circle-fill"></i> Information We Collect</div>
            <ul class="consent-list">
                <li>Personal Identifiable Information (Full Name, Date of Birth, Residency)</li>
                <li>Academic Records (GPA, Course, Year Level, Enrollment Status)</li>
                <li>Financial Background (Household Income, Number of Dependents)</li>
                <li>Special Qualifications (PWD status, IP affiliation, Solo Parent, etc.)</li>
            </ul>
        </div>

        <div class="mb-3">
            <div class="section-title"><i class="bi bi-check-circle-fill"></i> How We Use Your Data</div>
            <ul class="consent-list">
                <li>To determine your eligibility for university-funded and external scholarships.</li>
                <li>To generate a personalized list of matched scholarship opportunities.</li>
                <li>To notify you of scholarship deadlines, decisions, and updates.</li>
            </ul>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-3">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('consent.store') }}">
            @csrf
            <div class="check-area">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="consent" name="consent" value="1"
                        onchange="document.getElementById('proceedBtn').disabled = !this.checked" />
                    <label class="form-check-label fw-semibold" for="consent">
                        I have read and agree to the Data Privacy Terms and Conditions.
                    </label>
                </div>
                <p class="text-muted mt-1 mb-0" style="font-size:.8rem;padding-left:1.5rem;">
                    Your consent is required to proceed with the scholarship matching questionnaire.
                </p>
            </div>
            <button type="submit" class="btn-proceed" id="proceedBtn" disabled>
                <i class="bi bi-shield-check me-2"></i>Proceed to Questionnaire
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center mt-3">
            @csrf
            <button type="submit" class="btn btn-link text-muted" style="font-size:.875rem;">
                <i class="bi bi-arrow-left me-1"></i>Back to Login
            </button>
        </form>
    </div>
@endsection
