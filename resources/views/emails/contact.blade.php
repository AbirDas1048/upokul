<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f3f6fb;
            font-family: Arial, Helvetica, sans-serif;
            color: #374151;
            line-height: 1.6;
            padding: 40px 20px;
        }

        .wrapper {
            max-width: 650px;
            margin: 0 auto;
        }

        .card {
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .header {
            background-color: #2563eb; /* Fallback */
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            color: #ffffff;
            text-align: center;
            padding: 32px 24px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .header p {
            color: rgba(255,255,255,.9);
            font-size: 15px;
        }

        .content {
            padding: 32px;
        }

        .intro {
            margin-bottom: 28px;
            color: #4b5563;
        }

        .info-group {
            margin-bottom: 18px;
        }

        .label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6b7280;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .value {
            color: #111827;
            font-size: 15px;
        }

        .message-box {
            margin-top: 24px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            padding: 20px;
        }

        .message-box h3 {
            margin-bottom: 12px;
            color: #111827;
            font-size: 18px;
        }

        .message-box p {
            white-space: pre-line;
            color: #4b5563;
        }

        .footer {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
        }

        .footer strong {
            color: #111827;
        }

        @media (max-width: 600px) {

            body {
                padding: 20px 12px;
            }

            .content {
                padding: 24px 20px;
            }

            .header {
                padding: 24px 20px;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<div class="wrapper">

    <div class="card">

        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>New Contact Form Submission</p>
        </div>

        <div class="content">

            <p class="intro">
                You have received a new contact form submission from your website.
            </p>

            <div class="info-group">
                <span class="label">Name</span>
                <div class="value">{{ $contactMail->name }}</div>
            </div>

            <div class="info-group">
                <span class="label">Email Address</span>
                <div class="value">{{ $contactMail->email }}</div>
            </div>

            <div class="info-group">
                <span class="label">Phone Number</span>
                <div class="value">{{ $contactMail->phone ?: 'N/A' }}</div>
            </div>

            <div class="info-group">
                <span class="label">Subject</span>
                <div class="value">{{ $contactMail->subject }}</div>
            </div>

            <div class="message-box">
                <h3>Message</h3>

                <p>{{ $contactMail->mail_message }}</p>
            </div>

            <div class="footer">
                <strong>{{ config('app.name') }}</strong><br>

                {{ config('custom.office_address') }}<br>

                Email: {{ config('custom.office_email') }}<br>

                Phone: {{ config('custom.office_phone') }}<br><br>

                This email was automatically generated from the website contact form.
            </div>
        </div>
    </div>
</div>
</body>
</html>
