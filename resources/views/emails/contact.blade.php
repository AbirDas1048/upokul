<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>

    <style>
        /* Mobile responsiveness */
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }
            .content {
                padding: 16px !important;
            }
            .heading {
                font-size: 20px !important;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f3f4f6; padding:20px 0;">
    <tr>
        <td align="center">

            <!-- Main Container -->
            <table class="container" width="600" cellpadding="0" cellspacing="0" role="presentation"
                   style="background-color:#ffffff; border-radius:8px; overflow:hidden; width:600px; max-width:100%;">

                <!-- Header -->
                <tr>
                    <td style="background-color:#0ea5e9; padding:20px; text-align:center;">
                        <h2 class="heading" style="margin:0; color:#ffffff; font-size:22px;">
                            New Contact Message
                        </h2>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td class="content" style="padding:24px; color:#374151; font-size:14px; line-height:1.6;">

                        <p style="margin:0 0 12px;">
                            <strong>Name:</strong> {{ $contactMail->name }}
                        </p>

                        <p style="margin:0 0 12px;">
                            <strong>Email:</strong> {{ $contactMail->email }}
                        </p>

                        <p style="margin:0 0 12px;">
                            <strong>Phone No:</strong> {{ $contactMail->phone ?? 'N/A' }}
                        </p>

                        <p style="margin:0 0 12px;">
                            <strong>Subject:</strong> {{ $contactMail->subject }}
                        </p>

                        <hr style="border:none; border-top:1px solid #e5e7eb; margin:20px 0;">

                        <p style="margin:0 0 8px;">
                            <strong>Message:</strong>
                        </p>

                        <p style="margin:0; white-space:pre-line;">
                            {{ $contactMail->mail_message }}
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f9fafb; padding:16px; text-align:center; font-size:12px; color:#6b7280;">
                        This message was sent from the website contact form.
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
