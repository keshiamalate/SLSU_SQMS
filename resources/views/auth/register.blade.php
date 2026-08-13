@extends('layouts.app')
@section('title', 'Register')

@push('styles')
    <style>
        body {
            background: #0d2b55;
        }

        .register-wrapper {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* Left panel */
        .left-panel {
            background: var(--sm-navy);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            color: #fff;
        }

        .left-panel h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: .25rem;
        }

        .left-panel .subtitle {
            opacity: .75;
            margin-bottom: 2.5rem;
            font-size: .95rem;
        }

        .feature-pill {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            padding: .6rem 1.2rem;
            margin-bottom: .75rem;
            width: 100%;
            max-width: 340px;
            display: flex;
            align-items: center;
            gap: .75rem;
            font-size: .9rem;
        }

        .feature-pill i {
            color: #60a5fa;
        }

        /* Right panel */
        .right-panel {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            overflow-y: auto;
        }

        .register-card {
            width: 100%;
            max-width: 460px;
        }

        .register-card h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #111;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            font-size: .875rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1.5px solid #d1d5db;
            padding: .6rem 1rem;
            font-size: .875rem;
        }

        .form-control:focus {
            border-color: var(--sm-accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .btn-register {
            background: var(--sm-navy);
            color: #fff;
            border-radius: 8px;
            padding: .75rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            border: none;
        }

        .btn-register:hover {
            background: var(--sm-accent);
            color: #fff;
        }

        @media (max-width: 768px) {
            .register-wrapper {
                grid-template-columns: 1fr;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                padding: 2rem 1.25rem;
                min-height: 100vh;
            }

            .register-card {
                max-width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="register-wrapper">

        {{-- Left: Branding --}}
        <div class="left-panel">
            <h1>SmartMatch</h1>
            <p class="subtitle">SLSU Scholarship System</p>
            <div class="feature-pill"><i class="bi bi-stars"></i> AI-Powered Eligibility Matching</div>
            <div class="feature-pill"><i class="bi bi-shield-check"></i> Secure Data Management</div>
            <div class="feature-pill"><i class="bi bi-bell"></i> Real-time Application Tracking</div>
            <p class="mt-4" style="opacity:.4;font-size:.75rem;">SOUTHERN LEYTE STATE UNIVERSITY • {{ date('Y') }}</p>
        </div>

        {{-- Right: Registration Form --}}
        <div class="right-panel">
            <div class="register-card">
                <h2>Create Account</h2>
                <p class="text-muted mb-4" style="font-size:.875rem;">
                    Register to access scholarship matching.
                </p>

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                                <li style="font-size:.875rem;">{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store') }}">
                    @csrf

                    {{-- Student ID --}}
                    <div class="mb-3">
                        <label class="form-label">Student ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-person-badge text-muted"></i>
                            </span>
                            <input type="text" name="institutional_id"
                                class="form-control border-start-0 @error('institutional_id') is-invalid @enderror"
                                placeholder="e.g. 24-00001" value="{{ old('institutional_id') }}" autofocus required />
                            @error('institutional_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Enter your SLSU student ID.</div>
                    </div>

                    {{-- Name --}}
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name"
                                class="form-control @error('first_name') is-invalid @enderror" placeholder="Juan"
                                value="{{ old('first_name') }}" required />
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" placeholder="Optional"
                                value="{{ old('middle_name') }}" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name"
                                class="form-control @error('last_name') is-invalid @enderror" placeholder="Dela Cruz"
                                value="{{ old('last_name') }}" required />
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                            <input type="email" name="email"
                                class="form-control border-start-0 @error('email') is-invalid @enderror"
                                placeholder="juan@email.com" value="{{ old('email') }}" required />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-lock text-muted"></i>
                            </span>
                            <input type="password" name="password" id="password"
                                class="form-control border-start-0 @error('password') is-invalid @enderror"
                                placeholder="Minimum 8 characters" required />
                            <button class="btn btn-outline-secondary border-start-0" type="button"
                                onclick="togglePwd('password','eye1')">
                                <i class="bi bi-eye" id="eye1"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-4">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-lock-fill text-muted"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="password2"
                                class="form-control border-start-0" placeholder="Re-enter password" required />
                            <button class="btn btn-outline-secondary border-start-0" type="button"
                                onclick="togglePwd('password2','eye2')">
                                <i class="bi bi-eye" id="eye2"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-register mb-3">
                        <i class="bi bi-person-plus me-2"></i>Create Account
                    </button>

                    <p class="text-center text-muted" style="font-size:.875rem;">
                        Already have an account?
                        <a href="{{ route('login') }}" style="color:var(--sm-accent);font-weight:600;">Sign In</a>
                    </p>

                    <div class="mt-3 pt-3 border-top text-center">
                        <p style="font-size:.75rem;color:#9ca3af;">
                            By registering, you agree to our
                            <a href="#" style="color:var(--sm-accent);">Privacy Policy</a>.
                            Your data is protected under RA 10173.
                        </p>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function togglePwd(inputId, iconId) {
            const pwd = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
            icon.className = pwd.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        }
    </script>
@endpush
