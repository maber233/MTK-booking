#!/bin/bash

# Create local.php with database configuration from environment variables
cat > config/autoload/local.php << 'EOF'
<?php
return [
    'db' => [
        'database' => $_ENV['DATABASE_NAME'] ?? 'railway',
        'username' => $_ENV['DATABASE_USER'] ?? 'root',
        'password' => $_ENV['DATABASE_PASSWORD'] ?? '',
        'hostname' => $_ENV['DATABASE_HOST'] ?? 'localhost',
        'port' => $_ENV['DATABASE_PORT'] ?? 3306,
    ],
    'mail' => [
        'type' => $_ENV['MAIL_TYPE'] ?? 'file',
        'address' => $_ENV['MAIL_FROM'] ?? 'info@bookings.example.com',
        
        // SMTP settings (used when MAIL_TYPE is 'smtp' or 'smtp-tls')
        'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
        'user' => $_ENV['MAIL_USER'] ?? '',
        'pw' => $_ENV['MAIL_PASSWORD'] ?? '',
        'port' => $_ENV['MAIL_PORT'] ?? 'auto',
        'auth' => $_ENV['MAIL_AUTH'] ?? 'plain',
    ],
    'i18n' => [
        'locale' => 'sv-SE',
        'timezone' => 'Europe/Stockholm',
        'choice' => [
            'en-US' => 'English',
            'sv-SE' => 'Svenska',
        ],
    ],
];
EOF
