<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank you for contacting us</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #f3f6fb;
            font-family: Arial, Helvetica, sans-serif;
            color: #374151;
            line-height: 1.6;
            padding: 40px 20px;
        }

        .wrapper { max-width: 650px; margin: 0 auto; }

        .card {
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .header {
            background-color: #2563eb;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            color: #ffffff;
            text-align: center;
            padding: 32px 24px;
        }

        .header h1 { font-size: 28px; margin-bottom: 8px; }
        .header p { color: rgba(255,255,255,.9); font-size: 15px; }

        .content { padding: 32px; }

        .content p { margin-bottom: 16px; color: #4b5563; }
        .content .greeting { font-size: 18px; color: #111827; font-weight: bold; }

        .message-box {
            margin: 24px 0;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            padding: 20px;
        }

        .message-box .label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .message-box .subject { color: #111827; font-weight: bold; margin-bottom: 12px; }
        .message-box .body { white-space: pre-line; color: #4b5563; margin: 0; }

        .footer {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
        }

        .footer strong { color: #111827; }

        @media (max-width: 600px) {
            body { padding: 20px 12px; }
            .content { padding: 24px 20px; }
            .header { padding: 24px 20px; }
            .header h1 { font-size: 24px; }
        }
    </style>
</head>

<body>
<div class="wrapper">
    <div class="card">

        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>We received your message</p>
        </div>

        <div class="content">
            <p class="greeting">Dear {{ $contactMail->name }},</p>

            <p>
                Thank you for contacting {{ config('app.name') }}. We have received your message
                and our team will get back to you as soon as possible.
            </p>

            <div class="message-box">
                <span class="label">Your subject</span>
                <div class="subject">{{ $contactMail->subject }}</div>

                <span class="label">Your message</span>
                <p class="body">{{ $contactMail->mail_message }}</p>
            </div>

            <p>
                If your matter is urgent, you can reach us directly at
                {{ config('custom.office_phone') }}.
            </p>

            <p>
                Best regards,<br>
                <strong>{{ config('custom.owner_name') }}</strong><br>
                {{ config('app.name') }}
            </p>

            <div class="footer">
                <strong>{{ config('app.name') }}</strong><br>
                {{ config('custom.office_address') }}<br>
                Email: {{ config('custom.office_email') }}<br>
                Phone: {{ config('custom.office_phone') }}<br><br>
                This is an automated confirmation. Please do not reply to this email.
            </div>
        </div>
    </div>
</div>
</body>
</html>
