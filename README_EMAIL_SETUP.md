# Email Setup Guide

This guide explains how to configure email sending for Simple Strategic Plans using the Outlook email account.

## Email Account

- **Email Address:** simplestrategicplanning@outlook.com
- **SMTP Server:** smtp-mail.outlook.com
- **Port:** 587 (TLS)

## Configuration

### 1. Update Your `.env` File

Add the following to your `.env` file:

```env
# Email Configuration (Outlook/Office365)
SMTP_HOST=smtp-mail.outlook.com
SMTP_PORT=587
SMTP_USER=simplestrategicplanning@outlook.com
SMTP_PASS=your_outlook_password_here
SMTP_FROM_EMAIL=simplestrategicplanning@outlook.com
SMTP_FROM_NAME=Simple Strategic Plans
CONTACT_EMAIL=simplestrategicplanning@outlook.com
```

**Important:** Replace `your_outlook_password_here` with the actual password for the Outlook account.

### 2. Outlook App Password (Recommended)

For better security, Microsoft recommends using an "App Password" instead of your regular password:

1. Go to https://account.microsoft.com/security
2. Sign in with simplestrategicplanning@outlook.com
3. Enable two-factor authentication if not already enabled
4. Go to "Advanced security options"
5. Create a new "App password"
6. Use this app password in your `.env` file instead of your regular password

### 3. Testing Email

The system will automatically use SMTP if credentials are configured. If SMTP fails, it will fall back to PHP's `mail()` function.

To test email sending:
1. Register a new user account
2. Check the user's email inbox for the verification email
3. Check server error logs if emails aren't being sent

## Email Templates

Email templates are stored in `templates/emails/`:
- `verification.php` - Email verification template

## Troubleshooting

### Emails Not Sending

1. **Check SMTP credentials** - Ensure the password in `.env` is correct
2. **Check firewall** - Port 587 must be open
3. **Check Outlook security** - May need to enable "Less secure app access" or use App Password
4. **Check error logs** - Look in PHP error log for SMTP connection errors

### Using PHPMailer (Optional)

For more reliable email sending, you can install PHPMailer via Composer:

```bash
composer require phpmailer/phpmailer
```

The system will automatically use PHPMailer if it's available, otherwise it will use the built-in SMTP socket implementation.

## Contact Email

The contact email address (`simplestrategicplanning@outlook.com`) is automatically used in:
- Terms of Service page
- Privacy Policy page
- Cookie Policy page
- Email footers

This ensures users can easily contact you with questions or concerns.
