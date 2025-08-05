#!/bin/bash

echo "🔧 Applying database fixes for email activation..."

# Create a PHP script to execute the SQL fix
cat > fix-db.php << 'PHPEOF'
<?php
// Apply database fixes for email activation
$config = include(__DIR__ . '/config/autoload/local.php');

$host = $config['db']['hostname'];
$port = $config['db']['port'];
$database = $config['db']['database'];
$username = $config['db']['username'];
$password = $config['db']['password'];

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database\n";
    
    // Fix user activation setting
    $stmt = $pdo->prepare("INSERT INTO bs_options (`key`, `value`, `locale`) VALUES ('service.user.activation', 'email', NULL) ON DUPLICATE KEY UPDATE `value` = 'email'");
    $stmt->execute();
    
    echo "✅ Set service.user.activation = 'email'\n";
    
    // Set other essential defaults if they don't exist
    $defaults = [
        'service.user.registration' => 'true',
        'client.name.short' => 'MTK',
        'client.name.full' => 'MTK Booking System',
        'service.name.full' => 'Booking System',
        'client.contact.email' => 'info@bookings.example.com',
        'service.website' => 'https://mtk-booking-production.up.railway.app'
    ];
    
    foreach ($defaults as $key => $value) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO bs_options (`key`, `value`, `locale`) VALUES (?, ?, NULL)");
        $stmt->execute([$key, $value]);
    }
    
    echo "✅ Applied default configuration options\n";
    echo "🎉 Database fixes completed successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
}
PHPEOF

# Execute the PHP script
php fix-db.php

echo "✅ Email activation fix applied!"
