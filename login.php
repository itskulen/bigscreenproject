<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Ambil data pengguna dari database
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$user]);
    $data = $stmt->fetch();

    if ($data && password_verify($pass, $data['password'])) {
        // Verifikasi password
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $data['username']; // Simpan username ke sesi
        $_SESSION['role'] = $data['role']; // Simpan role pengguna

        // Arahkan pengguna berdasarkan role
        if ($data['role'] === 'mascot') {
            header('Location: mascot_admin.php'); // Halaman untuk mascot_manager
        } elseif ($data['role'] === 'costume') {
            header('Location: costume_admin.php'); // Halaman untuk costume_manager
        } else {
            header('Location: unauthorized.php'); // Jika role tidak dikenali
        }
        exit();
    } else {
        $error = 'Invalid credentials!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login Admin</title>
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background-color: #f8f9fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .login-container {
                background: #ffffff;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 400px;
            }

            .login-container h2 {
                margin-bottom: 20px;
                text-align: center;
            }

            .error {
                color: red;
                font-size: 0.9em;
                text-align: center;
            }
        </style>
    </head>

    <body>
        <div class="login-container">
            <h2>Login Admin</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        placeholder="Enter your username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Enter your password" required>
                </div>
                <?php if (!empty($error)) : ?>
                <div class="error"><?= $error ?></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </body>

</html>
