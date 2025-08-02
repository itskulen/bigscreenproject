<?php
require 'db.php';
require 'image_helper.php';

echo "Testing HTML output for first project...\n\n";

// Get first project
$stmt = $pdo->query("SELECT * FROM gallery WHERE category = 'mascot' LIMIT 1");
$row = $stmt->fetch();

if ($row) {
    echo "Project: {$row['project_name']}\n";
    echo "Project Image: {$row['project_image']}\n";
    echo "Material Image: {$row['material_image']}\n\n";

    // Test project image HTML
    echo "=== PROJECT IMAGE HTML ===\n";
    $projectImages = parseImageData($row['project_image']);

    if (!empty($projectImages)) {
        echo 'Found ' . count($projectImages) . " project images\n";
        foreach ($projectImages as $imgIndex => $image) {
            echo "Image $imgIndex: uploads/projects/" . htmlspecialchars($image) . "\n";
        }

        echo "\nHTML Output:\n";
        if ($imgIndex === 0) {
            echo '<img src="uploads/projects/' . htmlspecialchars($projectImages[0]) . '" width="100" class="rounded">' . "\n";
        }
    }

    // Test material image HTML
    echo "\n=== MATERIAL IMAGE HTML ===\n";
    $materialImages = parseImageData($row['material_image']);

    if (!empty($materialImages)) {
        echo 'Found ' . count($materialImages) . " material images\n";
        foreach ($materialImages as $imgIndex => $image) {
            echo "Image $imgIndex: uploads/materials/" . htmlspecialchars($image) . "\n";
        }

        echo "\nHTML Output:\n";
        echo '<img src="uploads/materials/' . htmlspecialchars($materialImages[0]) . '" alt="No Submission Notes yet" style="width: 100%; height: 150px; object-fit: contain; background-color: #f8f9fa; cursor: pointer;">' . "\n";
    }
}
?>
