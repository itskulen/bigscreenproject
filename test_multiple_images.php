<?php
/**
 * Quick test script untuk memverifikasi fungsi multiple image upload
 */

require 'db.php';
require 'image_helper.php';

echo "Testing multiple image helper functions...\n\n";

// Test 1: Parse JSON array
$jsonData = '["image1.jpg", "image2.jpg", "image3.jpg"]';
$parsed = parseImageData($jsonData);
echo "Test 1 - Parse JSON array:\n";
echo "Input: $jsonData\n";
echo 'Output: ' . print_r($parsed, true) . "\n";

// Test 2: Parse single string (old format)
$stringData = 'single_image.jpg';
$parsed2 = parseImageData($stringData);
echo "Test 2 - Parse single string:\n";
echo "Input: $stringData\n";
echo 'Output: ' . print_r($parsed2, true) . "\n";

// Test 3: Get first image
$firstImage = getFirstImage($jsonData);
echo "Test 3 - Get first image:\n";
echo "Input: $jsonData\n";
echo "First image: $firstImage\n\n";

// Test 4: Get image count
$count = getImageCount($jsonData);
echo "Test 4 - Get image count:\n";
echo "Input: $jsonData\n";
echo "Count: $count\n\n";

// Test 5: Convert array to JSON
$imageArray = ['test1.jpg', 'test2.jpg'];
$jsonResult = imagesToJson($imageArray);
echo "Test 5 - Convert array to JSON:\n";
echo 'Input: ' . print_r($imageArray, true);
echo "Output: $jsonResult\n\n";

// Test 6: Check database migration results
echo "Test 6 - Check database migration results:\n";
$stmt = $pdo->query('SELECT id, project_name, project_image, material_image FROM gallery LIMIT 3');
$results = $stmt->fetchAll();

foreach ($results as $row) {
    echo "ID: {$row['id']}, Project: {$row['project_name']}\n";
    echo '  Project Images: ' . getImageCount($row['project_image']) . " images\n";
    echo '  Material Images: ' . getImageCount($row['material_image']) . " images\n";
    echo '  First Project Image: ' . (getFirstImage($row['project_image']) ?: 'No image') . "\n";
    echo '  First Material Image: ' . (getFirstImage($row['material_image']) ?: 'No image') . "\n\n";
}

echo "All tests completed successfully!\n";
?>
