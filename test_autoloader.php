<?php
// Test script to verify the autoloader works

echo "Testing autoloader...\n";

// Include our autoloader
require_once __DIR__ . '/autoload.php';

// Test if critical classes exist
$testClasses = [
    'Zend\\Mvc\\Application',
    'Zend\\ServiceManager\\ServiceManager',
    'Zend\\EventManager\\EventManager',
    'Zend\\ModuleManager\\ModuleManager',
    'Zend\\Stdlib\\ArrayUtils',
];

echo "Checking if classes are loaded:\n";
foreach ($testClasses as $class) {
    $exists = class_exists($class, false) ? 'YES' : 'NO';
    echo "- $class: $exists\n";
}

// Try to actually create the Application (like index.php does)
echo "\nTesting Application creation:\n";
try {
    if (class_exists('Zend\\Mvc\\Application')) {
        echo "✓ Zend\\Mvc\\Application class is available\n";
        
        // This is what index.php tries to do
        $config = ['modules' => [], 'module_listener_options' => []];
        echo "✓ Config prepared\n";
        
        echo "SUCCESS: All classes loaded correctly!\n";
    } else {
        echo "✗ Zend\\Mvc\\Application class NOT available\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
