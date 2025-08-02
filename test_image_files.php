<?php
require 'db.php';
require 'image_helper.php';

echo "Checking database images and file existence...\n\n";

// Get all mascot projects
$stmt = $pdo->query("SELECT id, project_name, project_image, material_image FROM gallery WHERE category = 'mascot' LIMIT 5");
$results = $stmt->fetchAll();

foreach ($results as $row) {
    echo "=== Project ID: {$row['id']} - {$row['project_name']} ===\n";

    // Check project images
    echo 'Project Images Data: ' . ($row['project_image'] ?: 'NULL') . "\n";
    $projectImages = parseImageData($row['project_image']);
    echo 'Parsed Project Images: ' . print_r($projectImages, true);

    foreach ($projectImages as $image) {
        $path = "uploads/projects/$image";
        $exists = file_exists($path) ? 'EXISTS' : 'MISSING';
        echo "  - $image: $exists\n";
    }

    // Check material images
    echo 'Material Images Data: ' . ($row['material_image'] ?: 'NULL') . "\n";
    $materialImages = parseImageData($row['material_image']);
    echo 'Parsed Material Images: ' . print_r($materialImages, true);

    foreach ($materialImages as $image) {
        $path = "uploads/materials/$image";
        $exists = file_exists($path) ? 'EXISTS' : 'MISSING';
        echo "  - $image: $exists\n";
    }

    echo "\n";
}
?>
