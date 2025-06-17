<?php

/*
 * RoundCube Auto-Login Configuration
 * Place this file in your RoundCube installation config directory
 */

// Basic RoundCube configuration for Laravel integration
$config = [];

// General configuration
$config['db_dsnw'] = 'mysql://roundcube:password@localhost/roundcube';
$config['log_driver'] = 'file';
$config['temp_dir'] = '/tmp';

// IMAP/SMTP Configuration for Stalwart Mail
$config['default_host'] = [
    'ssl://mail.yourdomain.com:993' => 'IMAP SSL',
];

$config['smtp_server'] = 'ssl://mail.yourdomain.com';
$config['smtp_port'] = 465;
$config['smtp_user'] = '%u';
$config['smtp_pass'] = '%p';

// Security and session settings
$config['des_key'] = 'your-roundcube-des-key-here';
$config['session_lifetime'] = 60; // minutes
$config['ip_check'] = false; // Disable IP checking for auto-login

// Plugin configuration
$config['plugins'] = [
    'archive',
    'zipdownload',
    'password',
    'managesieve',
    'laravel_autologin', // Custom plugin for Laravel integration
];

// Auto-login configuration
$config['laravel_autologin_enabled'] = true;
$config['laravel_autologin_secret'] = env('WEBMAIL_ENCRYPTION_KEY', 'your-shared-secret-key');
$config['laravel_autologin_token_lifetime'] = 300; // 5 minutes

// Interface settings
$config['language'] = 'en_US';
$config['skin'] = 'elastic';
$config['date_format'] = 'Y-m-d';
$config['time_format'] = 'H:i';

// Performance settings
$config['enable_caching'] = true;
$config['message_cache_lifetime'] = '10d';

// Security headers
$config['force_https'] = true;
$config['use_https'] = true;

return $config;
