<?php
require 'db.php';
require 'image_helper.php';

// Get first few projects
$stmt = $pdo->query("SELECT * FROM gallery WHERE category = 'mascot' LIMIT 3");
$projects = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>

    <head>
        <title>Debug Images</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    </head>

    <body>
        <div class="container mt-4">
            <h2>Debug: Multiple Images Test</h2>

            <?php foreach ($projects as $index => $row): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Project: <?= htmlspecialchars($row['project_name']) ?></h5>
                    <small>ID: <?= $row['id'] ?></small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Project Images:</h6>
                            <p><strong>Raw Data:</strong> <?= htmlspecialchars($row['project_image']) ?></p>
                            <?php 
                        $projectImages = parseImageData($row['project_image']);
                        echo "<p><strong>Parsed Count:</strong> " . count($projectImages) . "</p>";
                        
                        if (!empty($projectImages)): ?>
                            <div class="position-relative">
                                <?php foreach ($projectImages as $imgIndex => $image): ?>
                                <p>Image <?= $imgIndex + 1 ?>: <?= htmlspecialchars($image) ?></p>
                                <?php if ($imgIndex === 0): ?>
                                <img src="uploads/projects/<?= htmlspecialchars($image) ?>" width="200"
                                    class="border rounded mb-2" alt="Project Image"
                                    onerror="this.style.border='2px solid red'; this.alt='IMAGE MISSING: <?= htmlspecialchars($image) ?>';">
                                <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if (count($projectImages) > 1): ?>
                                <span class="badge bg-primary"><?= count($projectImages) ?> images</span>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-muted">No project images</p>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <h6>Material Images:</h6>
                            <p><strong>Raw Data:</strong> <?= htmlspecialchars($row['material_image']) ?></p>
                            <?php 
                        $materialImages = parseImageData($row['material_image']);
                        echo "<p><strong>Parsed Count:</strong> " . count($materialImages) . "</p>";
                        
                        if (!empty($materialImages)): ?>
                            <div class="position-relative">
                                <?php foreach ($materialImages as $imgIndex => $image): ?>
                                <p>Image <?= $imgIndex + 1 ?>: <?= htmlspecialchars($image) ?></p>
                                <?php if ($imgIndex === 0): ?>
                                <img src="uploads/materials/<?= htmlspecialchars($image) ?>" width="200"
                                    class="border rounded mb-2" alt="Material Image"
                                    onerror="this.style.border='2px solid red'; this.alt='IMAGE MISSING: <?= htmlspecialchars($image) ?>';">
                                <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if (count($materialImages) > 1): ?>
                                <span class="badge bg-primary"><?= count($materialImages) ?> images</span>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <p class="text-muted">No material images</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </body>

</html>
