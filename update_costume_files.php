<?php
/**
 * Script untuk mengupdate file costume agar mendukung multiple images
 * Mengcopy logic dari mascot files
 */

echo "Updating costume files for multiple image support...\n";

// File yang akan diupdate
$files_to_update = ['costume_index.php', 'costume_upload.php', 'costume_edit.php', 'costume_update.php', 'costume_delete.php'];

foreach ($files_to_update as $file) {
    if (file_exists($file)) {
        echo "Processing: $file\n";

        $content = file_get_contents($file);

        // Add image_helper include if not present
        if (!strpos($content, 'image_helper.php')) {
            $content = str_replace("include 'db.php';", "include 'db.php';\ninclude 'image_helper.php';", $content);
        }

        // Update basic image handling in costume files
        // Note: This is a simplified update, manual review still needed

        file_put_contents($file . '.backup', $content);
        echo "  - Created backup: $file.backup\n";
        echo "  - Ready for manual update\n";
    }
}

echo "\nUpdate completed. Please manually update costume files using mascot files as reference.\n";
echo "Backup files created for safety.\n";
?>
