<?php
/**
 * Database Connection Test for Cloud Run
 */

error_log("DB TEST: Starting database connection test");

// Load environment variables (same as application)
$dbHost = $_ENV['DB_HOST'] ?? '/cloudsql/mtk-booking-system:europe-west1:mtk-db';
$dbName = $_ENV['DB_NAME'] ?? 'mtk_booking';
$dbUser = $_ENV['DB_USER'] ?? 'mtk_booking';
$dbPassword = $_ENV['DB_PASSWORD'] ?? '';

echo "<h1>Database Connection Test</h1>";
echo "<h2>Environment Variables:</h2>";
echo "<ul>";
echo "<li><strong>DB_HOST:</strong> " . htmlspecialchars($dbHost) . "</li>";
echo "<li><strong>DB_NAME:</strong> " . htmlspecialchars($dbName) . "</li>";
echo "<li><strong>DB_USER:</strong> " . htmlspecialchars($dbUser) . "</li>";
echo "<li><strong>DB_PASSWORD:</strong> " . (empty($dbPassword) ? 'EMPTY!' : '[SET - length: ' . strlen($dbPassword) . ']') . "</li>";
echo "</ul>";

echo "<h2>Connection Test Results:</h2>";

// Test 1: Check if socket file exists
echo "<h3>1. Socket File Test</h3>";
if (strpos($dbHost, '/cloudsql/') === 0) {
    $socketExists = file_exists($dbHost);
    echo "<p><strong>Socket path:</strong> $dbHost</p>";
    echo "<p><strong>Socket exists:</strong> " . ($socketExists ? "✅ YES" : "❌ NO") . "</p>";
    
    if (!$socketExists) {
        echo "<p style='color: red;'><strong>ERROR:</strong> Cloud SQL socket not found. This means the Cloud Run service is not properly connected to Cloud SQL.</p>";
    }
} else {
    echo "<p>Using TCP connection: $dbHost</p>";
}

// Test 2: PDO Connection Test
echo "<h3>2. PDO Connection Test</h3>";
try {
    // Build DSN
    if (strpos($dbHost, '/cloudsql/') === 0) {
        // Unix socket connection
        $dsn = "mysql:unix_socket=$dbHost;dbname=$dbName;charset=utf8mb4";
    } else {
        // TCP connection
        $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    }
    
    echo "<p><strong>DSN:</strong> " . htmlspecialchars($dsn) . "</p>";
    
    $pdo = new PDO($dsn, $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p style='color: green;'>✅ <strong>SUCCESS:</strong> Connected to database!</p>";
    
    // Test 3: Basic Query
    echo "<h3>3. Database Query Test</h3>";
    
    // Check database version
    $stmt = $pdo->query('SELECT VERSION() as version');
    $version = $stmt->fetch();
    echo "<p><strong>MySQL Version:</strong> " . htmlspecialchars($version['version']) . "</p>";
    
    // Check current database
    $stmt = $pdo->query('SELECT DATABASE() as db');
    $currentDb = $stmt->fetch();
    echo "<p><strong>Current Database:</strong> " . htmlspecialchars($currentDb['db']) . "</p>";
    
    // List tables
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll();
    echo "<p><strong>Tables in database:</strong> " . count($tables) . "</p>";
    
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars(array_values($table)[0]) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ Database is empty (no tables found). This explains why the application wants to run setup.</p>";
    }
    
    $pdo = null;
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ <strong>CONNECTION FAILED:</strong></p>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Error Code:</strong> " . htmlspecialchars($e->getCode()) . "</p>";
}

echo "<h2>Summary</h2>";
echo "<p>This test checks if the Cloud Run service can connect to Cloud SQL using the same configuration as the MTK-booking application.</p>";

error_log("DB TEST: Database connection test completed");
?>
