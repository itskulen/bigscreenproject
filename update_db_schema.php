<?php
include 'db.php';

try {
    echo "<h3>🔧 Database Schema Update</h3>\n";

    // Check current column types
    $stmt = $pdo->query("SHOW COLUMNS FROM gallery LIKE 'project_image'");
    $projectImageCol = $stmt->fetch();

    $stmt = $pdo->query("SHOW COLUMNS FROM gallery LIKE 'material_image'");
    $materialImageCol = $stmt->fetch();

    echo "<p><strong>Current Types:</strong></p>\n";
    echo "<ul>\n";
    echo '<li>project_image: ' . $projectImageCol['Type'] . "</li>\n";
    echo '<li>material_image: ' . $materialImageCol['Type'] . "</li>\n";
    echo "</ul>\n";

    // Update column types to TEXT to handle longer JSON data
    if (strpos($projectImageCol['Type'], 'varchar') !== false) {
        echo "<p>🔄 Updating project_image column to TEXT...</p>\n";
        $pdo->exec('ALTER TABLE gallery MODIFY COLUMN project_image TEXT');
        echo "<p>✅ project_image updated to TEXT</p>\n";
    } else {
        echo "<p>✅ project_image already TEXT type</p>\n";
    }

    if (strpos($materialImageCol['Type'], 'varchar') !== false) {
        echo "<p>🔄 Updating material_image column to TEXT...</p>\n";
        $pdo->exec('ALTER TABLE gallery MODIFY COLUMN material_image TEXT');
        echo "<p>✅ material_image updated to TEXT</p>\n";
    } else {
        echo "<p>✅ material_image already TEXT type</p>\n";
    }

    // Verify changes
    $stmt = $pdo->query("SHOW COLUMNS FROM gallery LIKE 'project_image'");
    $projectImageCol = $stmt->fetch();

    $stmt = $pdo->query("SHOW COLUMNS FROM gallery LIKE 'material_image'");
    $materialImageCol = $stmt->fetch();

    echo "<p><strong>Updated Types:</strong></p>\n";
    echo "<ul>\n";
    echo '<li>project_image: ' . $projectImageCol['Type'] . "</li>\n";
    echo '<li>material_image: ' . $materialImageCol['Type'] . "</li>\n";
    echo "</ul>\n";

    echo "<p>🎉 <strong>Database schema update completed!</strong></p>\n";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>\n";
}
?>
