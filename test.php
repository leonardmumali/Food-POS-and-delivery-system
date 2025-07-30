<?php
// Simple test file to verify everything is working
echo "<h1>FoodExpress Test Page</h1>";
echo "<p>PHP is working! Version: " . phpversion() . "</p>";

// Test database connection
try {
    require_once 'config/database.php';
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test a simple query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $result = $stmt->fetch();
    echo "<p style='color: green;'>✅ Database query successful! Found " . $result['count'] . " categories.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Test session
session_start();
echo "<p>✅ Session started successfully!</p>";

// Test file permissions
$testFile = 'test_write.txt';
if (file_put_contents($testFile, 'test')) {
    echo "<p style='color: green;'>✅ File write permissions OK!</p>";
    unlink($testFile); // Clean up
} else {
    echo "<p style='color: red;'>❌ File write permissions failed!</p>";
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ul>";
echo "<li><a href='index.php'>Go to Homepage</a></li>";
echo "<li><a href='menu.php'>Browse Menu</a></li>";
echo "<li><a href='login.php'>Login</a></li>";
echo "<li><a href='register.php'>Register</a></li>";
echo "</ul>";
?> 