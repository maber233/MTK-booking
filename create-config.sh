#!/bin/bash

# Create local.php with database configuration from environment variables
cat > config/autoload/local.php << 'EOF'
<?php
return [
    'db' => [
        'database' => $_ENV['DB_NAME'] ?? 'railway',
        'username' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'hostname' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? 3306,
    ],
    'mail' => [
        'type' => 'sendmail',
        'address' => 'info@bookings.example.com',
    ],
    'i18n' => [
        'locale' => 'en-US',
        'timezone' => 'Europe/Berlin',
    ],
];
EOF
