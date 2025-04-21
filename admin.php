<?php
session_start();
include 'db.php';
$sql = "SELECT project_name, project_status, project_image, material_image, description, deadline FROM gallery";
$result = $pdo->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Upload</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .drop-zone {
            border: 2px dashed #007bff;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            margin-bottom: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .drop-zone:hover {
            background: #e9ecef;
        }
        .container {
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="container">
<a href="login.php">
    <button type="button" text-align="left">Logout</button>
    
</a>


    <h2 class="text-center">Upload Project</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="project_name">Project Name:</label>
            <input type="text" class="form-control" id="project_name" name="project_name" required>
        </div>

        <div class="mb-3">
    <label for="project_status" class="form-label">Project Status</label>
    <select name="project_status" id="project_status" class="form-select" required>
      <option value="">-- Select Status --</option>
      <option value="Not Started">Not Started</option>
      <option value="In Progress">In Progress</option>
      <option value="Revision">Revision</option>
      <option value="Completed">Completed</option> 
    </select>
  </div>

        <div class="form-group">
            <label>Project Image:</label>
            <div class="drop-zone" onclick="document.getElementById('project_image').click();">
                Click or Drag to Upload Project Image
            </div>
            <input type="file" id="project_image" name="project_image" accept="image/*" style="display:none;" required>
        </div>

        <div class="form-group">
            <label>Material Image:</label>
            <div class="drop-zone" onclick="document.getElementById('material_image').click();">
                Click or Drag to Upload Material Image
            </div>
            <input type="file" id="material_image" name="material_image" accept="image/*" style="display:none;" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="deadline">Deadline:</label>
            <input type="date" textarea class="form-control" id="deadline" name="deadline" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Upload</button>
    </form>
</div>
<hr>
<div class="container mt-5">
    <h2>Daftar Project</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Status</th>
                <th>Project Image</th>
                <th>Material Image</th>
                <th>Description</th>
                <th>Deadline</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
            while($row = $stmt->fetch()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['project_name']) ?></td>
                <td><?= htmlspecialchars($row['project_status']) ?></td>
                <td>
                    <img src="uploads/projects/<?= $row['project_image'] ?>" width="100"><br>
                    <small>Project Image</small>
                </td>
                <td>
                    <img src="uploads/materials/<?= $row['material_image'] ?>" width="100"><br>
                    <small>Material Image</small>
                </td>
                <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                <td><?=htmlspecialchars($row['deadline']) ?></td>
                <td>
             <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
             <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
