<?php
/**
 * Debug Zend Database Adapter Configuration
 */

// Set up error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Zend Database Adapter Debug</h1>";

// Change to app directory
chdir(dirname(__DIR__));
echo "<p>Working directory: " . getcwd() . "</p>";

// Load autoloader
require 'vendor/autoload.php';

// Load config
echo "<h2>1. Configuration Loading</h2>";

try {
    $config = include 'config/autoload/global.php';
    echo "<p>✅ Global config loaded</p>";
    echo "<pre>" . print_r($config['db'], true) . "</pre>";
} catch (Exception $e) {
    echo "<p>❌ Global config failed: " . $e->getMessage() . "</p>";
}

try {
    $localConfig = include 'config/autoload/local.php';
    echo "<p>✅ Local config loaded</p>";
    echo "<pre>" . print_r($localConfig['db'], true) . "</pre>";
} catch (Exception $e) {
    echo "<p>❌ Local config failed: " . $e->getMessage() . "</p>";
}

// Merge configs
echo "<h2>2. Configuration Merging</h2>";
$dbConfig = array_merge($config['db'], $localConfig['db']);
echo "<pre>" . print_r($dbConfig, true) . "</pre>";

// Test Zend adapter creation
echo "<h2>3. Zend Adapter Creation</h2>";

try {
    $adapter = new \Zend\Db\Adapter\Adapter($dbConfig);
    echo "<p>✅ Zend adapter created successfully</p>";
    
    // Test connection
    echo "<h2>4. Connection Test</h2>";
    $connection = $adapter->getDriver()->getConnection();
    $connection->connect();
    echo "<p>✅ Connection successful</p>";
    
    // Test query
    echo "<h2>5. Query Test</h2>";
    $result = $adapter->query('SHOW TABLES', \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
    $tables = $result->toArray();
    echo "<p>✅ Query successful. Tables found: " . count($tables) . "</p>";
    echo "<pre>" . print_r($tables, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ Adapter/Connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Error details:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>6. Environment Variables</h2>";
echo "<p>DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NOT SET') . "</p>";
echo "<p>DB_NAME: " . ($_ENV['DB_NAME'] ?? 'NOT SET') . "</p>";
echo "<p>DB_USER: " . ($_ENV['DB_USER'] ?? 'NOT SET') . "</p>";
echo "<p>DB_PASSWORD: " . (isset($_ENV['DB_PASSWORD']) ? '[SET - length: ' . strlen($_ENV['DB_PASSWORD']) . ']' : 'NOT SET') . "</p>";
?>
