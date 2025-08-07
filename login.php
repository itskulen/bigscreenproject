<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$user]);
    $data = $stmt->fetch();

    if ($data && password_verify($pass, $data['password'])) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        if ($data['role'] === 'mascot') {
            header('Location: mascot_admin.php');
        } elseif ($data['role'] === 'costume') {
            header('Location: costume_admin.php');
        } else {
            header('Location: unauthorized.php');
        }
        exit();
    } else {
        $_SESSION['login_error'] = 'Invalid username or password!';
        header('Location: login.php');
        exit();
    }
}

// Ambil error dari session dan hapus setelah diambil
$error = '';
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .login-container {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                padding: 40px 35px 35px 35px;
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                width: 100%;
                max-width: 400px;
                position: relative;
                animation: fadeInUp 0.8s ease-out;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .login-container h2 {
                margin-bottom: 25px;
                text-align: center;
                font-weight: 700;
                background: linear-gradient(135deg, #667eea, #764ba2);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                letter-spacing: 0.5px;
                font-size: 1.8rem;
            }

            .form-label {
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
            }

            .input-group-text {
                background: linear-gradient(135deg, #f8fafc, #e2e8f0);
                border: 1px solid #d1d5db;
                color: #6b7280;
            }

            .input-group .form-control {
                border-color: #d1d5db;
                padding: 12px 15px;
                font-size: 0.95rem;
            }

            .input-group .form-control:not(:first-child) {
                border-left: none;
            }

            .input-group .form-control:not(:last-child) {
                border-right: none;
            }

            .input-group:focus-within .input-group-text,
            .input-group:focus-within .form-control {
                border-color: #667eea;
                box-shadow: none;
            }

            .input-group:focus-within {
                box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
                border-radius: 0.375rem;
            }

            .form-control:focus {
                border-color: #667eea;
                box-shadow: none;
            }

            .btn-primary {
                background: linear-gradient(135deg, #667eea, #764ba2);
                border: none;
                font-weight: 600;
                letter-spacing: 0.5px;
                padding: 12px;
                border-radius: 10px;
                transition: all 0.1s ease;
                font-size: 1rem;
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #5a67d8, #6b46c1);
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            }

            .show-password {
                cursor: pointer;
                color: #6b7280;
                font-size: 1.1em;
                transition: color 0.1s ease;
                background: linear-gradient(135deg, #f8fafc, #e2e8f0);
                border: 1px solid #d1d5db;
            }

            .show-password:hover {
                color: #667eea;
            }

            .error {
                background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
                border: 1px solid rgba(239, 68, 68, 0.2);
                color: #dc2626;
                font-size: 0.9rem;
                text-align: center;
                margin-bottom: 15px;
                margin-top: -5px;
                padding: 10px;
                border-radius: 8px;
                font-weight: 500;
            }

            .back-link {
                text-align: center;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid rgba(0, 0, 0, 0.1);
            }

            .back-link a {
                color: #6b7280;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.1s ease;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .back-link a:hover {
                color: #667eea;
            }

            @media (max-width: 500px) {
                .login-container {
                    padding: 30px 25px 25px 25px;
                    margin: 20px;
                }

                .login-container h2 {
                    font-size: 1.6rem;
                }
            }
        </style>
    </head>

    <body>
        <div class="login-container">
            <h2><i class="bi bi-shield-check me-2"></i>Admin Portal</h2>
            <?php if (!empty($error)) : ?>
            <div class="error">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <?= $error ?>
            </div>
            <?php endif; ?>
            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Enter your username" required autofocus>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter your password" required>
                        <span class="input-group-text show-password" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i>
                    Login
                </button>
            </form>

            <div class="back-link">
                <a href="index.php">
                    <i class="bi bi-arrow-left"></i>
                    Back to Home
                </a>
            </div>
        </div>
        <script>
            function togglePassword() {
                const pwd = document.getElementById('password');
                const icon = document.getElementById('toggleIcon');
                if (pwd.type === "password") {
                    pwd.type = "text";
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    pwd.type = "password";
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        </script>
    </body>

</html>
