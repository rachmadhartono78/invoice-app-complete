<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }

        .otp-code {
            background-color: #f0f9ff;
            border: 2px dashed #2563eb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }

        .otp-code h2 {
            margin: 0 0 10px 0;
            color: #1e40af;
            font-size: 32px;
            letter-spacing: 8px;
            font-weight: bold;
        }

        .otp-label {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            text-align: center;
        }

        .button:hover {
            background-color: #1d4ed8;
        }

        .divider {
            text-align: center;
            margin: 30px 0;
            color: #9ca3af;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background-color: #e5e7eb;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .info-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .info-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .content {
            color: #4b5563;
            font-size: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p style="color: #6b7280; margin-top: 10px;">Verifikasi Email Anda</p>
        </div>

        <div class="greeting">
            Halo <strong>{{ $userName }}</strong>,
        </div>

        <div class="content">
            <p>Terima kasih telah mendaftar! Untuk menyelesaikan pendaftaran Anda, silakan verifikasi alamat email
                Anda menggunakan salah satu cara berikut:</p>

            <div class="otp-code">
                <div class="otp-label">Kode Verifikasi OTP:</div>
                <h2>{{ $otpCode }}</h2>
                <p style="margin: 10px 0 0 0; color: #6b7280; font-size: 13px;">Kode ini berlaku selama 5 menit</p>
            </div>

            <div class="divider">ATAU</div>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button" style="color: #ffffff">
                    Klik untuk Verifikasi Email
                </a>
            </div>

            <div class="info-box">
                <p><strong>Catatan Keamanan:</strong> Jika Anda tidak melakukan pendaftaran akun ini, abaikan email
                    ini. Jangan bagikan kode OTP kepada siapa pun.</p>
            </div>

            <p style="margin-top: 30px;">Jika tombol tidak berfungsi, salin dan tempel link berikut di browser Anda:
            </p>
            <p style="word-break: break-all; color: #2563eb; font-size: 12px;">{{ $verificationUrl }}</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p style="margin-top: 10px;">Email otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>

</html>
