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
        'type' => 'sendmail',
        'address' => 'info@bookings.example.com',
    ],
    'i18n' => [
        'locale' => 'en-US',
        'timezone' => 'Europe/Berlin',
        'choice' => [
            'en-US' => 'English (US)',
            'de-DE' => 'Deutsch',
            'fr-FR' => 'Français',
        ],
    ],
];
EOF
