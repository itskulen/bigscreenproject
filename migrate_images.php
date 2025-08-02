<?php
/**
 * Migration script untuk mengkonversi single image format ke multiple image format
 * Script ini akan mengkonversi data yang ada dari string biasa ke JSON array
 */

require 'db.php';

echo "Starting migration from single image format to multiple image format...\n";

try {
    // Get all records
    $stmt = $pdo->query('SELECT id, project_image, material_image FROM gallery');
    $records = $stmt->fetchAll();

    $updated = 0;

    foreach ($records as $record) {
        $needUpdate = false;
        $projectImage = $record['project_image'];
        $materialImage = $record['material_image'];

        // Check if project_image needs conversion
        if (!empty($projectImage)) {
            $decoded = json_decode($projectImage, true);
            if (!is_array($decoded)) {
                // Not JSON, convert to array
                $projectImage = json_encode([$projectImage]);
                $needUpdate = true;
            }
        }

        // Check if material_image needs conversion
        if (!empty($materialImage)) {
            $decoded = json_decode($materialImage, true);
            if (!is_array($decoded)) {
                // Not JSON, convert to array
                $materialImage = json_encode([$materialImage]);
                $needUpdate = true;
            }
        }

        // Update if needed
        if ($needUpdate) {
            $updateStmt = $pdo->prepare('UPDATE gallery SET project_image = ?, material_image = ? WHERE id = ?');
            $updateStmt->execute([$projectImage, $materialImage, $record['id']]);
            $updated++;
            echo "Updated record ID: {$record['id']}\n";
        }
    }

    echo "Migration completed successfully!\n";
    echo "Total records updated: $updated\n";
} catch (Exception $e) {
    echo 'Error during migration: ' . $e->getMessage() . "\n";
}
?>
