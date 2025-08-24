<?php
echo "<h2>PHP Extensions Check</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

$required_extensions = ['intl', 'pdo_mysql', 'mysqli'];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✅ {$ext} is loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ {$ext} is NOT loaded</p>";
    }
}

echo "<h3>All Loaded Extensions:</h3>";
$extensions = get_loaded_extensions();
sort($extensions);
echo "<ul>";
foreach ($extensions as $extension) {
    echo "<li>{$extension}</li>";
}
echo "</ul>";

if (extension_loaded('intl')) {
    echo "<h3>Intl Extension Info:</h3>";
    echo "<p>ICU Version: " . INTL_ICU_VERSION . "</p>";
    echo "<p>ICU Data Version: " . INTL_ICU_DATA_VERSION . "</p>";
}
?>
