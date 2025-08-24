<?php
/**
 * Simple MySQL Import Script
 */

// Set up error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>MySQL Direct Import</h1>";

// Change to app directory
chdir(dirname(__DIR__));

// Get database connection details from environment
$host = $_ENV['DB_HOST'] ?? '';
$dbname = $_ENV['DB_NAME'] ?? '';
$username = $_ENV['DB_USER'] ?? '';
$password = $_ENV['DB_PASSWORD'] ?? '';

echo "<h2>1. Environment Check</h2>";
echo "<p>DB_HOST: $host</p>";
echo "<p>DB_NAME: $dbname</p>";
echo "<p>DB_USER: $username</p>";
echo "<p>DB_PASSWORD: " . (empty($password) ? 'NOT SET' : '[SET]') . "</p>";

echo "<h2>2. MySQL Command Construction</h2>";

// For Cloud SQL Unix socket, we need to use the socket path
$socketPath = $host; // This is the Unix socket path
$sqlFile = 'data/db/ep3-bs.sql';

// Construct mysql command
$cmd = sprintf(
    'mysql --socket=%s --user=%s --password=%s --database=%s < %s 2>&1',
    escapeshellarg($socketPath),
    escapeshellarg($username),
    escapeshellarg($password),
    escapeshellarg($dbname),
    escapeshellarg($sqlFile)
);

echo "<p>Command (password hidden):</p>";
$displayCmd = str_replace($password, '[HIDDEN]', $cmd);
echo "<pre>$displayCmd</pre>";

echo "<h2>3. Execute Import</h2>";

// Check if SQL file exists
if (!file_exists($sqlFile)) {
    echo "<p>❌ SQL file not found: $sqlFile</p>";
    exit;
}

echo "<p>✅ SQL file found</p>";

// Execute the command
$output = [];
$returnCode = 0;
exec($cmd, $output, $returnCode);

echo "<p>Return code: $returnCode</p>";

if ($returnCode === 0) {
    echo "<p>✅ Import completed successfully!</p>";
} else {
    echo "<p>❌ Import failed</p>";
}

echo "<h3>Command output:</h3>";
if (empty($output)) {
    echo "<p>No output (this is normal for successful imports)</p>";
} else {
    echo "<pre>" . implode("\n", $output) . "</pre>";
}

echo "<h2>4. Verify Import</h2>";

// Test connection and check tables
try {
    $dsn = 'mysql:unix_socket=' . $host . ';dbname=' . $dbname . ';charset=utf8mb4';
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Database connection successful</p>";
    
    // Check tables
    $result = $pdo->query('SHOW TABLES');
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>✅ Tables in database: " . count($tables) . "</p>";
    
    if (count($tables) > 0) {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
        // Check if bs_options table exists and has data
        if (in_array('bs_options', $tables)) {
            $result = $pdo->query('SELECT COUNT(*) FROM bs_options');
            $count = $result->fetchColumn();
            echo "<p>✅ bs_options table has $count rows</p>";
        }
    } else {
        echo "<p>⚠️ No tables found - import may have failed</p>";
    }
    
} catch (PDOException $e) {
    echo "<p>❌ Database verification failed: " . $e->getMessage() . "</p>";
}

echo "<h2>5. Alternative: Manual Steps</h2>";
echo "<p>If the automatic import failed, you can try these manual steps:</p>";
echo "<ol>";
echo "<li>Connect to your Cloud SQL instance via Cloud Console</li>";
echo "<li>Use the 'Import' function in the Cloud SQL interface</li>";
echo "<li>Upload the SQL file: <code>data/db/ep3-bs.sql</code></li>";
echo "</ol>";

?>
