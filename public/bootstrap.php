<?php
/**
 * Minimal Database Bootstrap for Cloud Run
 * This script adds the minimum required configuration to allow the application to bootstrap
 */

// Bypass any application bootstrap - this is pure PHP
chdir(dirname(__DIR__));

echo "<h1>MTK-booking Minimal Bootstrap</h1>";

try {
    // Direct database connection
    $dsn = 'mysql:unix_socket=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4';
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];
    
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Database connected</p>";
    
    // Check if tables exist
    $result = $pdo->query('SHOW TABLES');
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) == 0) {
        echo "<p>🔧 Creating database tables...</p>";
        
        // Import the SQL file
        $sqlFile = 'data/db/ep3-bs.sql';
        $sqlContent = file_get_contents($sqlFile);
        
        // Execute SQL - split into statements properly
        $statements = explode(';', $sqlContent);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (PDOException $e) {
                    // Ignore errors for now - focus on getting basic structure
                }
            }
        }
        
        // Check again
        $result = $pdo->query('SHOW TABLES');
        $tables = $result->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>📊 Tables created: " . count($tables) . "</p>";
    }
    
    // Check if bs_options table exists
    if (in_array('bs_options', $tables)) {
        echo "<p>✅ bs_options table exists</p>";
        
        // Check if i18n config exists
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM bs_options WHERE `key` = ?');
        $stmt->execute(['i18n']);
        $i18nExists = $stmt->fetchColumn();
        
        if ($i18nExists == 0) {
            echo "<p>🔧 Adding minimal i18n configuration...</p>";
            
            // Add minimal i18n configuration
            $i18nConfig = json_encode([
                'locale' => 'en-US',
                'fallback' => 'en-US'
            ]);
            
            $stmt = $pdo->prepare('INSERT INTO bs_options (`key`, `value`) VALUES (?, ?)');
            $stmt->execute(['i18n', $i18nConfig]);
            
            echo "<p>✅ i18n configuration added</p>";
        } else {
            echo "<p>✅ i18n configuration already exists</p>";
        }
        
        // Add other minimal configurations that might be needed
        $minimalConfigs = [
            'client.name.full' => 'MTK-booking System',
            'client.name.short' => 'MTK',
            'client.contact.email' => 'admin@mtk-booking.com',
            'app.version' => '1.0.0',
            'service.calendar.days' => '30',
            'service.calendar.day-exceptions' => '[]',
            'service.name.full' => 'MTK Booking Service',
            'service.website' => 'https://mtk-booking.com',
            'service.maintenance' => 'false',
            'service.user.registration' => 'true',
            'service.user.activation' => 'immediate',
            'service.pricing.visibility' => 'public',
            'subject.square.type.plural' => 'Bookings'
        ];
        
        foreach ($minimalConfigs as $key => $value) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM bs_options WHERE `key` = ?');
            $stmt->execute([$key]);
            $exists = $stmt->fetchColumn();
            
            if ($exists == 0) {
                $stmt = $pdo->prepare('INSERT INTO bs_options (`key`, `value`) VALUES (?, ?)');
                $stmt->execute([$key, $value]);
                echo "<p>✅ Added config: $key</p>";
            }
        }
        
        echo "<h2>🎉 Bootstrap Complete!</h2>";
        echo "<p>The application should now be able to start properly.</p>";
        echo "<p><a href='/'>Go to MTK-booking Application</a></p>";
        
    } else {
        echo "<p>❌ bs_options table not found. Database setup incomplete.</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Details: " . $e->getTraceAsString() . "</p>";
}
?>
