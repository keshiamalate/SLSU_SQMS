@extends('layouts.app')
@section('title', 'Reset Password')

@push('styles')
    <style>
        body {
            background: #0d2b55;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .card-box {
            background: #fff;
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 460px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .form-control {
            border-radius: 8px;
            border: 1.5px solid #d1d5db;
            padding: .65rem 1rem;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .btn-submit {
            background: #0d2b55;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .75rem;
            font-weight: 600;
            width: 100%;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: #2563eb;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="card-box">

        <div class="text-center mb-4">
            <div
                style="width:56px;height:56px;background:#0d2b55;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="bi bi-key text-white fs-4"></i>
            </div>
            <h4 class="fw-bold" style="color:#0d2b55;">Reset Password</h4>
            <p class="text-muted" style="font-size:.875rem;">
                Enter your new password below.
            </p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-3 mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $e)
                        <li style="font-size:.875rem;">{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}" />

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label" style="font-size:.875rem;font-weight:600;">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-envelope text-muted"></i>
                    </span>
                    <input type="email" name="email"
                        class="form-control border-start-0 @error('email') is-invalid @enderror"
                        value="{{ old('email', $email) }}" placeholder="your@email.com" required />
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- New Password --}}
            <div class="mb-3">
                <label class="form-label" style="font-size:.875rem;font-weight:600;">New Password</label>
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
                <label class="form-label" style="font-size:.875rem;font-weight:600;">Confirm New Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-lock-fill text-muted"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="password2" class="form-control border-start-0"
                        placeholder="Re-enter new password" required />
                    <button class="btn btn-outline-secondary border-start-0" type="button"
                        onclick="togglePwd('password2','eye2')">
                        <i class="bi bi-eye" id="eye2"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit mb-3">
                <i class="bi bi-check-lg me-2"></i>Reset Password
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" style="font-size:.875rem;color:#2563eb;text-decoration:none;">
                    <i class="bi bi-arrow-left me-1"></i>Back to Login
                </a>
            </div>
        </form>

        <p class="text-center text-muted mt-4 mb-0" style="font-size:.75rem;">
            © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
        </p>
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
