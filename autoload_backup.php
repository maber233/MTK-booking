<?php
// Comprehensive autoloader for bu    // Step 1: Load critical base interfaces first (in dependency order)
    $criticalInterfaces = [
        '/src/Zend/Db/src/Exception/ExceptionInterface.php',
        '/src/Zend/Db/src/Adapter/Exception/ExceptionInterface.php',
    ];
    
    $interfaceCount = 0;
    foreach ($criticalInterfaces as $criticalPath) {
        $fullPath = $basePath . $criticalPath;
        if (file_exists($fullPath)) {
            try {
                error_log("AUTOLOADER: Loading critical interface: " . $fullPath);
                require_once $fullPath;
                $interfaceCount++;
            } catch (Exception $e) {
                error_log("AUTOLOADER: Error loading critical interface: " . $e->getMessage());
            }
        }
    }
    
    // Step 2: Load remaining interface files, sorted by directory depth
    $interfaceFiles = [];
    foreach ($allFiles as $file) {
        try {
            // Skip already loaded critical interfaces
            $skipFile = false;
            foreach ($criticalInterfaces as $criticalPath) {
                if (substr($file, -strlen($criticalPath)) === $criticalPath) {
                    $skipFile = true;
                    break;
                }
            }
            if ($skipFile) continue;
            
            $content = file_get_contents($file);
            if (strpos($content, 'interface ') !== false && strpos($content, 'namespace Zend') !== false) {
                // Determine dependency depth by counting directory levels from src/Zend
                $relativePath = str_replace($zendDir . '/', '', $file);
                $depth = substr_count($relativePath, '/');
                $interfaceFiles[$depth][] = $file;
            }
        } catch (Exception $e) {
            error_log("AUTOLOADER: Error checking file: " . $e->getMessage());
        }
    }
    
    // Load remaining interfaces by depth (shallowest first)
    ksort($interfaceFiles);
    foreach ($interfaceFiles as $depth => $files) {
        foreach ($files as $file) {
            try {
                error_log("AUTOLOADER: Loading interface file (depth $depth): " . $file);
                require_once $file;
                $interfaceCount++;
            } catch (Exception $e) {
                error_log("AUTOLOADER: Error loading interface: " . $e->getMessage());
            }
        }
    }mework components
// Strategy: Load all interfaces first, then all classes to avoid dependency issues

// Define the base path - hardcoded for Docker container
$basePath = '/var/www/html';

// Debug: Log the calculated base path
error_log("AUTOLOADER: Base path set to: " . $basePath);
error_log("AUTOLOADER: Current dir (__DIR__) is: " . __DIR__);
error_log("AUTOLOADER: PHP version is: " . phpversion());

// Function to recursively find all PHP files
function findAllPHPFiles($dir, $pattern = '*.php') {
    $files = [];
    try {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
    } catch (Exception $e) {
        error_log("AUTOLOADER: Error scanning directory: " . $e->getMessage());
    }
    return $files;
}

// Load all interfaces first (files containing "interface")
$zendDir = $basePath . '/src/Zend';
if (is_dir($zendDir)) {
    error_log("AUTOLOADER: Zend directory found, loading all interfaces first");
    
    $allFiles = findAllPHPFiles($zendDir);
    error_log("AUTOLOADER: Found " . count($allFiles) . " PHP files");
    
    // Step 1: Load all interface files first
    $interfaceCount = 0;
    foreach ($allFiles as $file) {
        try {
            $content = file_get_contents($file);
            if (strpos($content, 'interface ') !== false && strpos($content, 'namespace Zend') !== false) {
                error_log("AUTOLOADER: Loading interface file: " . $file);
                require_once $file;
                $interfaceCount++;
            }
        } catch (Exception $e) {
            error_log("AUTOLOADER: Error loading interface file $file: " . $e->getMessage());
        }
    }
    error_log("AUTOLOADER: Loaded $interfaceCount interface files");
    
    // Step 2: Load all remaining class files
    $classCount = 0;
    foreach ($allFiles as $file) {
        try {
            $content = file_get_contents($file);
            if (strpos($content, 'class ') !== false && strpos($content, 'namespace Zend') !== false) {
                // Skip if already loaded
                if (!in_array($file, get_included_files())) {
                    error_log("AUTOLOADER: Loading class file: " . $file);
                    require_once $file;
                    $classCount++;
                }
            }
        } catch (Exception $e) {
            error_log("AUTOLOADER: Error loading class file $file: " . $e->getMessage());
        }
    }
    error_log("AUTOLOADER: Loaded $classCount class files");
    
    error_log("AUTOLOADER: Finished loading all Zend files");
} else {
    error_log("AUTOLOADER: ERROR - Zend directory not found at: " . $zendDir);
}

// Check if critical classes are now available
$criticalClasses = [
    'Zend\\Mvc\\Application',
    'Zend\\ServiceManager\\ServiceManager',
    'Zend\\EventManager\\EventManager',
];

foreach ($criticalClasses as $class) {
    if (class_exists($class, false)) {
        error_log("AUTOLOADER: SUCCESS - $class is available");
    } else {
        error_log("AUTOLOADER: WARNING - $class is NOT available");
    }
}
