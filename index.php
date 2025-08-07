<?php
// filepath: c:\laragon\www\bigscreenproject\index.php
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>TvScreen Project Hub</title>
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .main-container {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                padding: 40px;
                text-align: center;
                max-width: 500px;
                width: 90%;
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

            .logo-section {
                margin-bottom: 30px;
            }

            .logo-section img {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                border: 3px solid rgba(102, 126, 234, 0.3);
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
            }

            .title {
                font-size: 2rem;
                font-weight: 700;
                background: linear-gradient(135deg, #667eea, #764ba2);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin: 20px 0 10px 0;
            }

            .subtitle {
                color: #6b7280;
                font-size: 1.1rem;
                margin-bottom: 30px;
            }

            .btn-project {
                padding: 15px 30px;
                font-size: 1.1rem;
                font-weight: 600;
                border-radius: 12px;
                border: none;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin: 0 10px;
                min-width: 180px;
                justify-content: center;
            }

            .btn-mascot {
                background: linear-gradient(135deg, #8b5cf6, #7c3aed);
                color: white;
                box-shadow: 0 3px 10px rgba(139, 92, 246, 0.3);
            }

            .btn-mascot:hover {
                background: linear-gradient(135deg, #7c3aed, #6d28d9);
                box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
                color: white;
            }

            .btn-costume {
                background: linear-gradient(135deg, #10b981, #059669);
                color: white;
                box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);
            }

            .btn-costume:hover {
                background: linear-gradient(135deg, #059669, #047857);
                box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
                color: white;
            }

            .divider {
                margin: 20px 0;
                font-size: 1.2rem;
                color: #9ca3af;
                font-weight: 500;
            }

            .admin-link {
                margin-top: 25px;
                padding-top: 25px;
                border-top: 1px solid rgba(0, 0, 0, 0.1);
            }

            .btn-admin {
                background: linear-gradient(135deg, #6b7280, #4b5563);
                color: white;
                padding: 10px 25px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.1s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-admin:hover {
                background: linear-gradient(135deg, #4b5563, #374151);
                color: white;
            }

            @media (max-width: 576px) {
                .main-container {
                    padding: 30px 20px;
                }

                .title {
                    font-size: 1.6rem;
                }

                .btn-project {
                    min-width: 150px;
                    padding: 12px 20px;
                    font-size: 1rem;
                    margin: 5px 0;
                }

                .divider {
                    margin: 15px 0;
                }
            }
        </style>
    </head>

    <body>
        <div class="main-container">
            <div class="logo-section">
                <img src="uploads/ccm.png" alt="CCM Logo">
            </div>

            <h1 class="title">TvScreen Project Hub</h1>
            <p class="subtitle">Choose your project department to get started</p>

            <div class="project-buttons">
                <a href="mascot_index.php" class="btn-project btn-mascot">
                    <i class="bi bi-emoji-smile"></i>
                    Mascot Projects
                </a>

                <div class="divider">or</div>

                <a href="costume_index.php" class="btn-project btn-costume">
                    <i class="bi bi-palette"></i>
                    Costume Projects
                </a>
            </div>

            <div class="admin-link">
                <a href="login.php" class="btn-admin">
                    <i class="bi bi-shield-check"></i>
                    Admin Login
                </a>
            </div>
        </div>
    </body>

</html>
