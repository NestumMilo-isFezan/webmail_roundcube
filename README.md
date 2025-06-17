# RoundCube Webmail Integration for Laravel

This directory contains configuration files and setup instructions for integrating RoundCube webmail with your Laravel application.

## Overview

The integration allows admins to:
1. View a list of created email accounts
2. Click "Check Email" to automatically log into RoundCube as that user
3. Access user emails seamlessly through the web interface

## Flow

```
Admin Dashboard → Mailbox List → Click "Check Email" → Laravel generates secure token → Redirect to RoundCube → Auto-login
```

## Configuration

### 1. Laravel Configuration

Add these environment variables to your `.env` file:

```env
# RoundCube Configuration
WEBMAIL_ROUNDCUBE_URL=http://localhost:8080/roundcube
WEBMAIL_ENCRYPTION_KEY=your-32-character-encryption-key

# Mail Server Settings (Stalwart Mail)
WEBMAIL_IMAP_HOST=mail.yourdomain.com
WEBMAIL_IMAP_PORT=993
WEBMAIL_IMAP_SSL=true
WEBMAIL_SMTP_HOST=mail.yourdomain.com
WEBMAIL_SMTP_PORT=587
WEBMAIL_SMTP_SSL=true
```

### 2. RoundCube Setup

1. **Install RoundCube** in your web server or use Docker (see below)
2. **Copy configuration**: Copy `webmailconfig/roundcube/config.inc.php` to your RoundCube config directory
3. **Install plugin**: Copy `webmailconfig/roundcube/laravel_autologin.php` to `plugins/laravel_autologin/`
4. **Update config**: Modify the configuration file with your actual settings:
   - Database connection
   - IMAP/SMTP server details
   - Encryption key (match with Laravel)

### 3. Docker Setup (Recommended)

The easiest way to get started:

```bash
cd webmailconfig
docker-compose up -d
```

This will start:
- RoundCube on `http://localhost:8080`
- MySQL database for RoundCube

## Security Features

- **Token-based authentication**: Secure tokens with expiration (5 minutes)
- **Encrypted payload**: User credentials are encrypted in transit
- **Domain validation**: Email domain validation
- **Session cleanup**: Automatic cleanup of temporary session data

## Usage

### For Admins

1. Go to Admin → Mailboxes
2. View the list of created email accounts
3. Click "Check Email" to access RoundCube with auto-login
4. RoundCube opens in a new tab with the user already logged in

### For Developers

The system provides these key components:

- **Controller**: `app/Http/Controllers/Admin/Mailbox/MailboxController.php`
- **Configuration**: `config/webmail.php`
- **Routes**: Defined in `routes/admin.php`
- **Frontend**: `resources/js/pages/admin/mailbox/Index.vue`

## API Endpoints

```php
// List all mailboxes
GET /admin/mailbox

// Redirect to RoundCube with auto-login
GET /admin/mailbox/{mailbox}/roundcube
```

## Troubleshooting

### Common Issues

1. **Token expired**: Tokens expire after 5 minutes for security
2. **Wrong encryption key**: Ensure the key matches between Laravel and RoundCube
3. **IMAP/SMTP connection**: Verify Stalwart Mail server is accessible
4. **Plugin not loaded**: Check RoundCube plugin configuration

### Debug Steps

1. Check Laravel logs for token generation errors
2. Check RoundCube logs for authentication issues
3. Verify network connectivity between services
4. Test IMAP/SMTP connections manually

## Production Considerations

1. **Use HTTPS**: Always use HTTPS in production
2. **Strong encryption**: Replace simple XOR encryption with AES-256
3. **Rate limiting**: Implement rate limiting for auto-login requests
4. **Audit logging**: Log all admin access to user mailboxes
5. **Token cleanup**: Implement token cleanup for expired tokens

## File Structure

```
webmailconfig/
├── README.md (this file)
├── docker-compose.yml
└── roundcube/
    ├── config.inc.php
    └── laravel_autologin.php
```

## Quick Start

1. **Setup environment variables** in `.env`
2. **Start RoundCube**: `cd webmailconfig && docker-compose up -d`
3. **Configure RoundCube**: Update the config files with your settings
4. **Test**: Go to `/admin/mailbox` and click "Check Email"

## Next Steps

1. Configure your environment variables
2. Start the Docker containers
3. Update RoundCube configuration with your mail server details
4. Test the auto-login functionality
5. Deploy to production with proper security measures

The integration is now ready! Admins can easily access any user's email account through RoundCube with just one click. 🎉
