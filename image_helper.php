<?php
/**
 * Helper functions untuk menangani multiple images
 * dengan backward compatibility untuk single image format lama
 */

/**
 * Parse image data dari database (support both old string format and new JSON format)
 * @param string|null $imageData - Data dari database
 * @return array - Array of image filenames
 */
function parseImageData($imageData)
{
    if (empty($imageData)) {
        return [];
    }

    // Try to decode as JSON first
    $decoded = json_decode($imageData, true);
    if (is_array($decoded)) {
        return $decoded;
    }

    // If not JSON, treat as single image (old format)
    return [$imageData];
}

/**
 * Get first image from image data
 * @param string|null $imageData - Data dari database
 * @return string|null - First image filename or null
 */
function getFirstImage($imageData)
{
    $images = parseImageData($imageData);
    return !empty($images) ? $images[0] : null;
}

/**
 * Get image count
 * @param string|null $imageData - Data dari database
 * @return int - Number of images
 */
function getImageCount($imageData)
{
    return count(parseImageData($imageData));
}

/**
 * Convert image array to JSON for database storage
 * @param array $images - Array of image filenames
 * @return string|null - JSON string or null
 */
function imagesToJson($images)
{
    return !empty($images) ? json_encode($images) : null;
}

/**
 * Generate Fancybox gallery HTML for multiple images
 * @param string|null $imageData - Data dari database
 * @param string $folder - Upload folder (projects or materials)
 * @param string $galleryName - Fancybox gallery name
 * @param string $projectName - Project name for caption
 * @param string $imageType - Type of image (Project or Material)
 * @return string - HTML output
 */
function generateImageGallery($imageData, $folder, $galleryName, $projectName, $imageType = 'Image')
{
    $images = parseImageData($imageData);

    if (empty($images)) {
        return '<span class="text-muted">No Image</span>';
    }

    $html = '<div class="image-gallery position-relative d-inline-block">';

    foreach ($images as $index => $image) {
        $caption = htmlspecialchars($projectName) . ' - ' . $imageType . ' ' . ($index + 1);
        $imagePath = "uploads/{$folder}/" . htmlspecialchars($image);

        $html .=
            '<a href="' .
            $imagePath .
            '" 
                     data-fancybox="' .
            $galleryName .
            '" 
                     data-caption="' .
            $caption .
            '"
                     ' .
            ($index === 0 ? '' : 'style="display:none;"') .
            '>';

        if ($index === 0) {
            $html .= '<img src="' . $imagePath . '" width="60" height="60" class="rounded" style="object-fit: cover; cursor: pointer; transition: transform 0.2s ease;">';
        }

        $html .= '</a>';
    }

    // Badge di dalam gambar - hanya tampil jika lebih dari 1 gambar
    if (count($images) > 1) {
        $html .= '<span class="image-count-badge position-absolute">' . count($images) . '</span>';
    }

    $html .= '</div>';

    return $html;
}
?>
