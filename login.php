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
        $error = 'Invalid username or password!';
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .login-container {
                background: #fff;
                padding: 36px 32px 28px 32px;
                border-radius: 18px;
                box-shadow: 0 8px 32px rgba(139, 92, 246, 0.10), 0 1.5px 8px rgba(0, 0, 0, 0.06);
                width: 100%;
                max-width: 370px;
                position: relative;
                animation: fadeIn 0.7s;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: none;
                }
            }

            .login-container h2 {
                margin-bottom: 18px;
                text-align: center;
                font-weight: 700;
                color: #7c3aed;
                letter-spacing: 1px;
            }

            .form-label {
                font-weight: 500;
                color: #6b7280;
            }

            .input-group-text {
                background: #f3f4f6;
                border: none;
                color: #7c3aed;
            }

            .form-control:focus {
                border-color: #8b5cf6;
                box-shadow: 0 0 0 2px #a78bfa33;
            }

            .btn-primary {
                background: linear-gradient(90deg, #8b5cf6, #7c3aed);
                border: none;
                font-weight: 600;
                letter-spacing: 0.5px;
                transition: background 0.2s;
            }

            .btn-primary:hover {
                background: linear-gradient(90deg, #7c3aed, #8b5cf6);
            }

            .show-password {
                cursor: pointer;
                color: #a78bfa;
                font-size: 1.2em;
            }

            .error {
                color: #dc3545;
                font-size: 0.97em;
                text-align: center;
                margin-bottom: 10px;
                margin-top: -5px;
            }

            @media (max-width: 500px) {
                .login-container {
                    padding: 22px 8px 18px 8px;
                }
            }
        </style>
    </head>

    <body>
        <div class="login-container shadow">
            <h2><i class="bi bi-person-circle me-2"></i>Login Admin</h2>
            <?php if (!empty($error)) : ?>
            <div class="error"><?= $error ?></div>
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
                <button type="submit" class="btn btn-primary w-100 mt-2">Login</button>
            </form>
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
