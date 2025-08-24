<?php
/**
 * SQL File Diagnostic Script
 */

// Set up error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>SQL File Diagnostic</h1>";

// Change to app directory
chdir(dirname(__DIR__));

$sqlFile = 'data/db/ep3-bs.sql';

echo "<h2>1. Raw File Content (first 1000 chars)</h2>";
$content = file_get_contents($sqlFile);
echo "<pre>" . htmlspecialchars(substr($content, 0, 1000)) . "</pre>";

echo "<h2>2. File Lines Analysis</h2>";
$lines = explode("\n", $content);
echo "<p>Total lines: " . count($lines) . "</p>";

echo "<h3>First 20 lines:</h3>";
echo "<ol>";
for ($i = 0; $i < min(20, count($lines)); $i++) {
    $line = trim($lines[$i]);
    echo "<li>" . htmlspecialchars($line) . "</li>";
}
echo "</ol>";

echo "<h2>3. SQL Statement Detection</h2>";

// Method 1: Simple semicolon split
$statements1 = array_filter(array_map('trim', explode(';', $content)));
echo "<p>Method 1 (simple split): " . count($statements1) . " statements</p>";

// Method 2: Line-by-line parsing
$statements2 = [];
$currentStatement = '';
foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || substr($line, 0, 2) === '--') continue;
    
    $currentStatement .= $line . " ";
    
    if (substr($line, -1) === ';') {
        $statements2[] = trim($currentStatement);
        $currentStatement = '';
    }
}
echo "<p>Method 2 (line parsing): " . count($statements2) . " statements</p>";

echo "<h3>First 5 statements from Method 2:</h3>";
for ($i = 0; $i < min(5, count($statements2)); $i++) {
    echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
    echo "<strong>Statement " . ($i + 1) . ":</strong><br>";
    echo "<pre>" . htmlspecialchars($statements2[$i]) . "</pre>";
    echo "</div>";
}

echo "<h2>4. CREATE TABLE Detection</h2>";
$createTables = [];
foreach ($statements2 as $stmt) {
    if (stripos($stmt, 'CREATE TABLE') !== false) {
        $createTables[] = $stmt;
    }
}
echo "<p>CREATE TABLE statements found: " . count($createTables) . "</p>";

echo "<h3>CREATE TABLE statements:</h3>";
foreach ($createTables as $i => $stmt) {
    echo "<div style='border: 1px solid #green; margin: 10px; padding: 10px;'>";
    echo "<strong>CREATE TABLE " . ($i + 1) . ":</strong><br>";
    echo "<pre>" . htmlspecialchars(substr($stmt, 0, 200)) . "...</pre>";
    echo "</div>";
}

echo "<h2>5. Test Single CREATE TABLE</h2>";

if (!empty($createTables)) {
    // Test database connection
    $dsn = 'mysql:unix_socket=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4';
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];
    
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p>✅ Connected to database</p>";
        
        // Try to execute the first CREATE TABLE
        $testStatement = $createTables[0];
        echo "<p>Testing statement:</p>";
        echo "<pre>" . htmlspecialchars($testStatement) . "</pre>";
        
        try {
            $pdo->exec($testStatement);
            echo "<p>✅ Statement executed successfully!</p>";
            
            // Check if table was created
            $result = $pdo->query('SHOW TABLES');
            $tables = $result->fetchAll(PDO::FETCH_COLUMN);
            echo "<p>Tables now in database: " . count($tables) . "</p>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            
        } catch (PDOException $e) {
            echo "<p>❌ Statement failed: " . $e->getMessage() . "</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ No CREATE TABLE statements found!</p>";
}

?>
