@extends('layouts.app')
@section('title', 'Login')

@push('styles')
    <style>
        body {
            background: #0d2b55;
        }

        .login-wrapper {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

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

        .right-panel {
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }

        .login-card h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111;
        }

        .form-control {
            border-radius: 8px;
            padding: .65rem 1rem;
            border: 1.5px solid #d1d5db;
        }

        .form-control:focus {
            border-color: var(--sm-accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .btn-signin {
            background: var(--sm-navy);
            color: #fff;
            border-radius: 8px;
            padding: .75rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            border: none;
        }

        .btn-signin:hover {
            background: var(--sm-accent);
            color: #fff;
        }

        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .left-panel {
                display: none;
            }

            .right-panel {
                padding: 2rem 1.25rem;
                min-height: 100vh;
            }

            .login-card {
                max-width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="login-wrapper">
        <div class="left-panel">
            <h1>SmartMatch</h1>
            <p class="subtitle">SLSU Scholarship System</p>
            <div class="feature-pill"><i class="bi bi-stars"></i> AI-Powered Eligibility Matching</div>
            <div class="feature-pill"><i class="bi bi-shield-check"></i> Secure Data Management</div>
            <div class="feature-pill"><i class="bi bi-bell"></i> Real-time Application Tracking</div>
            <p class="mt-4" style="opacity:.4;font-size:.75rem;">SOUTHERN LEYTE STATE UNIVERSITY • {{ date('Y') }}</p>
        </div>

        <div class="right-panel">
            <div class="login-card">
                <h2>Welcome Back</h2>
                <p class="text-muted mb-4">Enter your credentials to access your portal</p>

                @if ($errors->any())
                    <div class="alert alert-danger rounded-3">
                        <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info rounded-3">{{ session('info') }}</div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-500">Student ID / Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="bi bi-person text-muted"></i></span>
                            <input type="text" name="institutional_id"
                                class="form-control border-start-0 @error('institutional_id') is-invalid @enderror"
                                placeholder="26-00000" value="{{ old('institutional_id') }}" autofocus required />
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label fw-500">Password</label>
                            <a href="{{ route('password.request') }}"
                                style="font-size:.875rem;color:var(--sm-accent);">Forgot password?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="bi bi-lock text-muted"></i></span>
                            <input type="password" name="password" id="password"
                                class="form-control border-start-0 @error('password') is-invalid @enderror"
                                placeholder="••••••••" required />
                            <button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePwd()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" />
                        <label class="form-check-label text-muted" for="remember" style="font-size:.875rem;">Remember me for
                            30 days</label>
                    </div>
                    <button type="submit" class="btn-signin">
                        Sign In <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                    <p class="text-center mt-4 text-muted" style="font-size:.875rem;">
                        Don't have an account yet?
                        <a href="{{ route('register.show') }}" class="fw-semibold" style="color:var(--sm-accent);">Register
                            Here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePwd() {
            const pwd = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
            icon.className = pwd.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        }
    </script>
@endpush
