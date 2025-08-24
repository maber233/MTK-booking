<?php

/**
 * Local Configuration Override for Cloud Run
 *
 * This file provides database configuration using environment variables
 * for deployment on Google Cloud Run with Cloud SQL.
 */

return [
    'db' => [
        'driver'         => 'Pdo_Mysql',
        'dsn'            => 'mysql:unix_socket=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4',
        'username'       => $_ENV['DB_USER'],
        'password'       => $_ENV['DB_PASSWORD'],
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ],
    ],
    'mail' => [
        'type' => $_ENV['MAIL_TYPE'] ?? 'sendmail',
        'address' => $_ENV['MAIL_ADDRESS'] ?? 'info@mtk-booking.com',
        'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
        'user' => $_ENV['MAIL_USER'] ?? '',
        'pw' => $_ENV['MAIL_PASSWORD'] ?? '',
        'port' => $_ENV['MAIL_PORT'] ?? 'auto',
        'auth' => $_ENV['MAIL_AUTH'] ?? 'plain',
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
