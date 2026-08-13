@extends('layouts.app')
@section('title', 'Profile Questionnaire — Step ' . $step)

@push('styles')
    <style>
        body {
            background: #0d2b55;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem 1rem;
        }

        .q-wrapper {
            width: 100%;
            max-width: 620px;
        }

        /* Progress steps */
        .steps-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            gap: 0;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }

        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #ffffff30;
            z-index: 0;
        }

        .step-item.done::after,
        .step-item.active::after {
            background: #2563eb;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .9rem;
            z-index: 1;
            position: relative;
            border: 2px solid #ffffff30;
            color: #ffffff60;
            background: transparent;
        }

        .step-item.done .step-circle {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .step-item.active .step-circle {
            background: #fff;
            border-color: #fff;
            color: #0d2b55;
        }

        .step-label {
            font-size: .7rem;
            color: #ffffff60;
            margin-top: .4rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .step-item.active .step-label,
        .step-item.done .step-label {
            color: #fff;
        }

        /* Card */
        .q-card {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .q-card .card-header-area {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .q-card .header-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--sm-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .q-card .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #111;
            margin: 0;
        }

        .q-card .card-subtitle {
            font-size: .85rem;
            color: #6b7280;
            margin: 0;
        }

        /* Form */
        .form-label {
            font-weight: 500;
            color: #374151;
            font-size: .9rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1.5px solid #d1d5db;
            padding: .65rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--sm-accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .form-check-input:checked {
            background-color: var(--sm-accent);
            border-color: var(--sm-accent);
        }

        /* Footer */
        .q-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid #e5e7eb;
        }

        .btn-prev {
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 8px;
            padding: .65rem 1.25rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .btn-prev:hover {
            background: #e5e7eb;
            color: #111;
        }

        .btn-next {
            background: var(--sm-navy);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .65rem 1.5rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .btn-next:hover {
            background: var(--sm-accent);
            color: #fff;
        }

        .step-counter {
            font-size: .85rem;
            color: #6b7280;
            font-weight: 500;
        }

        @media (max-width: 576px) {
            body {
                padding: 1rem .75rem;
            }

            .q-card {
                padding: 1.25rem;
            }

            .steps-bar {
                gap: 0;
            }

            .step-label {
                font-size: .6rem;
            }

            .step-circle {
                width: 34px;
                height: 34px;
                font-size: .8rem;
            }

            .q-footer {
                flex-wrap: wrap;
                gap: .5rem;
            }

            .step-counter {
                width: 100%;
                text-align: center;
                order: -1;
            }
        }
    </style>
@endpush

@section('content')
    <div class="q-wrapper">

        {{-- Step progress bar --}}
        <div class="steps-bar mb-4">
            @php
                $stepLabels = [1 => 'Academic', 2 => 'Financial', 3 => 'Personal', 4 => 'Qualifications'];
                $stepIcons = [1 => 'bi-book', 2 => 'bi-wallet2', 3 => 'bi-person', 4 => 'bi-award'];
            @endphp
            @foreach($stepLabels as $num => $label)
                <div class="step-item {{ $num < $step ? 'done' : ($num == $step ? 'active' : '') }}">
                    <div class="step-circle">
                        @if($num < $step)
                            <i class="bi bi-check-lg"></i>
                        @else
                            <i class="bi {{ $stepIcons[$num] }}"></i>
                        @endif
                    </div>
                    <div class="step-label">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        {{-- Card --}}
        <div class="q-card">
            @yield('q-content')
        </div>

        {{-- Bottom note --}}
        <p class="text-center mt-3" style="color:rgba(255,255,255,0.4);font-size:.75rem;">
            By submitting this profile, you certify that all information provided is accurate and truthful.
        </p>
        <p class="text-center" style="color:rgba(255,255,255,0.3);font-size:.7rem;">
            © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.
        </p>
    </div>
@endsection
