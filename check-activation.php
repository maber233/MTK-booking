<?php
/**
 * Quick script to check and optionally fix user activation setting
 */

// Load the application configuration
require_once __DIR__ . '/config/application.php';

use Base\Manager\OptionManager;

$serviceManager = $application->getServiceManager();
$optionManager = $serviceManager->get('Base\Manager\OptionManager');

echo "=== EP-3 User Activation Configuration Check ===\n\n";

$currentActivation = $optionManager->get('service.user.activation', 'NOT_SET');
echo "Current activation setting: " . $currentActivation . "\n\n";

echo "Available options:\n";
echo "  - immediate     : Users activated immediately after registration\n";
echo "  - manual        : Manual activation via backend (no emails)\n"; 
echo "  - manual-email  : Manual activation + admin notification email\n";
echo "  - email         : Automatic activation via user email (FIXES your issue)\n\n";

if ($currentActivation !== 'email') {
    echo "❌ ISSUE IDENTIFIED: Email activation is disabled.\n";
    echo "   This is why users can't request activation emails.\n\n";
    
    if (isset($argv[1]) && $argv[1] === '--fix') {
        echo "🔧 FIXING: Setting activation to 'email'...\n";
        $optionManager->set('service.user.activation', 'email');
        echo "✅ FIXED: User activation is now set to 'email'\n";
        echo "   Users can now request activation emails!\n";
    } else {
        echo "To fix this automatically, run:\n";
        echo "php check-activation.php --fix\n";
    }
} else {
    echo "✅ Configuration is correct for email activation.\n";
    echo "   The issue might be elsewhere (check email transport config).\n";
}

echo "\n=== End of Check ===\n";
