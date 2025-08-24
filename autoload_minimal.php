<?php
// Minimal autoloader for critical Zend interfaces only
// Load only the interfaces that are causing dependency issues

error_log("MINIMAL AUTOLOADER: Starting critical interface loading");

// Define the base path
$basePath = '/var/www/html';

// Load only the specific interfaces that have dependency issues
$criticalFiles = [
    '/src/Zend/Db/src/Exception/ExceptionInterface.php',
    '/src/Zend/Db/src/Adapter/Exception/ExceptionInterface.php',
];

$loadedCount = 0;
foreach ($criticalFiles as $criticalPath) {
    $fullPath = $basePath . $criticalPath;
    if (file_exists($fullPath)) {
        try {
            error_log("MINIMAL AUTOLOADER: Loading: " . $fullPath);
            require_once $fullPath;
            $loadedCount++;
        } catch (Exception $e) {
            error_log("MINIMAL AUTOLOADER: Error loading " . $fullPath . ": " . $e->getMessage());
        }
    } else {
        error_log("MINIMAL AUTOLOADER: File not found: " . $fullPath);
    }
}

error_log("MINIMAL AUTOLOADER: Loaded $loadedCount critical interfaces");
error_log("MINIMAL AUTOLOADER: Completed - letting Composer handle the rest");
