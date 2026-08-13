<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 2rem;
        }

        .container {
            max-width: 580px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .header {
            background: #0d2b55;
            padding: 1.5rem 2rem;
        }

        .header h1 {
            color: #fff;
            font-size: 1.2rem;
            margin: 0;
        }

        .header p {
            color: rgba(255, 255, 255, .7);
            font-size: .85rem;
            margin: .25rem 0 0;
        }

        .body {
            padding: 2rem;
            color: #374151;
            line-height: 1.7;
            font-size: .95rem;
        }

        .footer {
            background: #f9fafb;
            padding: 1rem 2rem;
            text-align: center;
            font-size: .75rem;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>SmartMatch — SLSU Scholarship System</h1>
            <p>Notification for {{ $user->full_name }}</p>
        </div>
        <div class="body">
            <h2 style="font-size:1.1rem;color:#0d2b55;margin-top:0;">{{ $subject }}</h2>
            <p>Dear {{ $user->first_name }},</p>
            <p>{{ $body }}</p>
            <p style="margin-top:2rem;">
                <a href="{{ url('/') }}" style="background:#0d2b55;color:#fff;padding:.65rem 1.25rem;border-radius:8px;text-decoration:none;font-weight:600;font-size:.9rem;">
                    Open SmartMatch
                </a>
            </p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Southern Leyte State University. SmartMatch Scholarship System.<br>
            This is an automated message. Please do not reply to this email.
        </div>
    </div>
</body>

</html>
