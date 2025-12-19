<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            margin: 0;
            padding: 0;
            width: 100% !important;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f3f4f6;
            padding-bottom: 40px;
        }

        .content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #0d9488;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .body {
            padding: 40px 30px;
        }

        .body p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #4b5563;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background-color: #0d9488;
            color: #ffffff !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #0f766e;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            margin: 5px 0;
        }

        @media only screen and (max-width: 600px) {
            .content {
                width: 100% !important;
                border-radius: 0;
            }

            .header {
                padding: 20px;
            }

            .body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="center">
                    <div style="height: 40px;"></div>
                    <div class="content">
                        <div class="header">
                            <h1>JobHub</h1>
                        </div>
                        <div class="body">
                            <p><strong>{{ $greeting }}</strong></p>
                            @foreach($lines as $line)
                                <p>{{ $line }}</p>
                            @endforeach

                            @if(isset($actionUrl) && isset($actionText))
                                <div class="button-container">
                                    <a href="{{ $actionUrl }}" class="button" target="_blank"
                                        style="color: #ffffff;">{{ $actionText }}</a>
                                </div>
                            @endif

                            <p>Thank you for using our platform!</p>

                            @if(isset($actionUrl))
                                <p style="margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                                    <small style="color: #6b7280; font-size: 12px;">If you're having trouble clicking the
                                        button, copy and paste the URL below into your web browser:</small><br>
                                    <a href="{{ $actionUrl }}"
                                        style="color: #0d9488; font-size: 12px; word-break: break-all;">{{ $actionUrl }}</a>
                                </p>
                            @endif
                        </div>
                        <div class="footer">
                            <p>&copy; {{ date('Y') }} JobHub. All rights reserved.</p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>