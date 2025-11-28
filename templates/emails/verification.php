<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(to right, #2563eb, #4f46e5);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }
        .button:hover {
            background: #1d4ed8;
        }
        .footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to Simple Strategic Plans</h1>
    </div>
    <div class="content">
        <p>Hello <?= h($user['first_name'] ?? 'there') ?>,</p>

        <p>Thank you for registering with Simple Strategic Plans! To complete your registration and verify your email address, please click the button below:</p>

        <div style="text-align: center;">
            <a href="<?= h($verificationUrl) ?>" class="button">Verify Email Address</a>
        </div>

        <p>Or copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #2563eb;"><?= h($verificationUrl) ?></p>

        <p>This verification link will expire in 24 hours.</p>

        <p>If you didn't create an account with Simple Strategic Plans, please ignore this email.</p>

        <p>Best regards,<br>
        The Simple Strategic Plans Team</p>
    </div>
    <div class="footer">
        <p>This is an automated email from Simple Strategic Plans.</p>
        <p>If you have any questions, please contact us at <?= h(CONTACT_EMAIL) ?></p>
    </div>
</body>
</html>
