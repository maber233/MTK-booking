<?php
/**
 * Database Setup Script - Import ep3-bs.sql
 */

// Set up error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Database Setup</h1>";

// Change to app directory
chdir(dirname(__DIR__));
echo "<p>Working directory: " . getcwd() . "</p>";

// Database connection using environment variables
$dsn = 'mysql:unix_socket=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4';
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

echo "<h2>1. Database Connection</h2>";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Connected to database</p>";
} catch (PDOException $e) {
    echo "<p>❌ Connection failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>2. SQL File Check</h2>";

$sqlFile = 'data/db/ep3-bs.sql';
if (!file_exists($sqlFile)) {
    echo "<p>❌ SQL file not found: $sqlFile</p>";
    exit;
}

echo "<p>✅ SQL file found: $sqlFile</p>";
echo "<p>File size: " . number_format(filesize($sqlFile)) . " bytes</p>";

echo "<h2>3. Import Database Structure</h2>";

try {
    $sqlContent = file_get_contents($sqlFile);
    
    echo "<p>📄 SQL content preview:</p>";
    echo "<pre>" . htmlspecialchars(substr($sqlContent, 0, 500)) . "...</pre>";
    
    // Clean up the SQL content
    $sqlContent = preg_replace('/--.*$/m', '', $sqlContent); // Remove comments
    $sqlContent = preg_replace('/\/\*.*?\*\//s', '', $sqlContent); // Remove block comments
    
    // Split SQL into individual statements (more robust)
    $statements = [];
    $currentStatement = '';
    $lines = explode("\n", $sqlContent);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        $currentStatement .= $line . "\n";
        
        // Check if statement ends with semicolon
        if (substr($line, -1) === ';') {
            $statements[] = trim($currentStatement);
            $currentStatement = '';
        }
    }
    
    // Add any remaining statement
    if (!empty(trim($currentStatement))) {
        $statements[] = trim($currentStatement);
    }
    
    echo "<p>📊 Found " . count($statements) . " SQL statements</p>";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $index => $statement) {
        $statement = trim($statement);
        if (empty($statement)) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $successCount++;
            echo "<p>✅ Statement " . ($index + 1) . ": " . substr($statement, 0, 50) . "...</p>";
        } catch (PDOException $e) {
            echo "<p>❌ Error in statement " . ($index + 1) . ": " . substr($statement, 0, 50) . "...</p>";
            echo "<p>Error: " . $e->getMessage() . "</p>";
            $errorCount++;
        }
    }
    
    echo "<p>✅ Import completed</p>";
    echo "<p>Successful statements: $successCount</p>";
    echo "<p>Errors: $errorCount</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Import failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>4. Verify Tables</h2>";

try {
    $result = $pdo->query('SHOW TABLES');
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>✅ Tables created: " . count($tables) . "</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p>❌ Error checking tables: " . $e->getMessage() . "</p>";
}

echo "<h2>5. Next Steps</h2>";
echo "<p>The database structure has been imported. You can now:</p>";
echo "<ul>";
echo "<li>Access the main application to continue with setup</li>";
echo "<li>The application should now proceed to the setup wizard</li>";
echo "</ul>";
?>
