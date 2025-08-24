<?php
/**
 * Cloud Run configuration - uses environment variables
 * This file is safe to commit as it contains no secrets
 */

return [
    'db' => [
        'database' => $_ENV['DB_NAME'] ?? 'mtk_booking',
        'username' => $_ENV['DB_USER'] ?? 'mtk_booking',
        'password' => $_ENV['DB_PASSWORD'] ?? '',

        'hostname' => $_ENV['DB_HOST'] ?? '/cloudsql/mtk-booking-system:europe-west1:mtk-db',
        'port' => null,
    ],
    'mail' => [
        'type' => 'sendmail',
        'address' => $_ENV['MAIL_FROM'] ?? 'info@bookings.example.com',

        'host' => '?',
        'user' => '?', 
        'pw' => '?',

        'port' => 'auto',
        'auth' => 'plain',
    ],
    'i18n' => [
        'choice' => [
            'en-US' => 'English',
            'de-DE' => 'Deutsch',
        ],

        'currency' => 'EUR',
        'locale' => 'de-DE',
    ],
];
