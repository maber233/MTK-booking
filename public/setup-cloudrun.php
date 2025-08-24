<?php
/**
 * ep-3 Bookingsystem Setup Tool for Cloud Run
 *
 * This tool helps to initialize and setup the database on Cloud Run.
 */

ob_start();

chdir(dirname(__DIR__));

/**
 * Quickly check the current PHP version.
 */
if (version_compare(PHP_VERSION, '8.1.0') < 0) {
    exit('PHP 8.1+ is required (currently running PHP ' . PHP_VERSION . ')');
}

/**
 * Quickly check if the intl extension is installed.
 */
if (! extension_loaded('intl')) {
	exit('The PHP <a href="http://php.net/manual/de/book.intl.php">intl extension</a> is required but not installed. '
	   . 'Please contact your web hosting provider to get this one fixed.');
}

/**
 * We are using composer (getcomposer.org) to install and autoload the dependencies.
 * Composer will create the entire vendor directory for us, including the autoloader.
 */
$autoloader = 'vendor/autoload.php';

if (! is_readable($autoloader)) {

    $charon = 'module/Base/Charon.php';

    if (! is_readable($charon)) {
        exit('Base module not found');
    }

    /**
     * Display an informative error page.
     */
    require $charon;

    Base\Charon::carry('application', 'installation', 1);
}

/**
 * Load and prepare the autoloader.
 */
require $autoloader;

/**
 * Initialize our PHP environment.
 */
$init = 'config/init.php';

if (! is_readable($init)) {
    exit('Please rename <b>config/init.php.dist</b> to <b>config/init.php</b> and edit its options accordingly');
}

require $init;

// Simple database setup without full application bootstrap
echo "<!DOCTYPE html>";
echo "<html><head><title>Database Setup</title></head><body>";
echo "<h1>MTK-booking Database Setup</h1>";

try {
    // Direct database setup using our known working connection
    $dsn = 'mysql:unix_socket=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4';
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];
    
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Database connected successfully</p>";
    
    // Check if tables already exist
    $result = $pdo->query('SHOW TABLES');
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<p>✅ Database already set up with " . count($tables) . " tables</p>";
        echo "<p><a href='/'>Go to application</a></p>";
    } else {
        echo "<p>⚙️ Setting up database tables...</p>";
        
        // Import SQL file
        $sqlFile = 'data/db/ep3-bs.sql';
        if (file_exists($sqlFile)) {
            $sqlContent = file_get_contents($sqlFile);
            
            // Simple import - execute the whole file at once
            $pdo->exec($sqlContent);
            
            // Verify
            $result = $pdo->query('SHOW TABLES');
            $tables = $result->fetchAll(PDO::FETCH_COLUMN);
            
            if (count($tables) > 0) {
                echo "<p>✅ Database setup completed! Created " . count($tables) . " tables</p>";
                echo "<p><a href='/'>Go to application</a></p>";
            } else {
                echo "<p>❌ Database setup failed - no tables created</p>";
            }
        } else {
            echo "<p>❌ SQL file not found: $sqlFile</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
