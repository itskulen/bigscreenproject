<?php
// Set HTTP status code 404
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>404 - Page Not Found</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

            .error-container {
                background: rgba(255, 255, 255, 0.96);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
                padding: 40px 35px;
                text-align: center;
                max-width: 450px;
                width: 90%;
                animation: fadeInUp 0.7s ease-out;
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

            .error-icon {
                font-size: 4rem;
                background: linear-gradient(135deg, #667eea, #764ba2);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin-bottom: 20px;
                display: block;
            }

            .error-code {
                font-size: 3rem;
                font-weight: 800;
                background: linear-gradient(135deg, #667eea, #764ba2);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin-bottom: 15px;
                letter-spacing: 2px;
            }

            .error-title {
                font-size: 1.5rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 15px;
            }

            .error-desc {
                color: #6b7280;
                font-size: 1rem;
                margin-bottom: 30px;
                line-height: 1.6;
            }

            .btn-group {
                display: flex;
                gap: 10px;
                justify-content: center;
                flex-wrap: wrap;
            }

            .btn-home {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: #fff;
                font-weight: 600;
                border: none;
                border-radius: 10px;
                padding: 12px 25px;
                transition: all 0.1s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
            }

            .btn-home:hover {
                background: linear-gradient(135deg, #5a67d8, #6b46c1);
                box-shadow: 0 3px 10px rgba(102, 126, 234, 0.4);
                color: #fff;
                text-decoration: none;
            }

            .btn-back {
                background: transparent;
                color: #6b7280;
                font-weight: 600;
                border: 2px solid #d1d5db;
                border-radius: 10px;
                padding: 10px 23px;
                transition: all 0.1s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-back:hover {
                background: #f3f4f6;
                border-color: #9ca3af;
                color: #374151;
                text-decoration: none;
            }

            .suggestions {
                margin-top: 25px;
                padding-top: 25px;
                border-top: 1px solid rgba(0, 0, 0, 0.1);
            }

            .suggestions h5 {
                color: #374151;
                font-weight: 600;
                margin-bottom: 15px;
            }

            .suggestion-links {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .suggestion-link {
                color: #667eea;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.1s ease;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .suggestion-link:hover {
                color: #5a67d8;
                text-decoration: underline;
            }

            @media (max-width: 576px) {
                .error-container {
                    padding: 30px 25px;
                    margin: 20px;
                }

                .error-code {
                    font-size: 2.5rem;
                }

                .error-title {
                    font-size: 1.3rem;
                }

                .btn-group {
                    flex-direction: column;
                    align-items: center;
                }

                .btn-home,
                .btn-back {
                    width: 100%;
                    justify-content: center;
                }
            }
        </style>
    </head>

    <body>
        <div class="error-container">
            <i class="bi bi-exclamation-triangle error-icon"></i>

            <div class="error-code">404</div>
            <div class="error-title">Page Not Found</div>

            <div class="error-desc">
                The requested URL was not found on this server.<br>
                The page you're looking for might have been removed, renamed, or is temporarily unavailable.
            </div>

            <div class="btn-group">
                <a href="index.php" class="btn-home">
                    <i class="bi bi-house"></i>
                    Go Home
                </a>
                <a href="javascript:history.back()" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    Go Back
                </a>
            </div>

            <div class="suggestions">
                <h5>You might be looking for:</h5>
                <div class="suggestion-links">
                    <a href="costume_index.php" class="suggestion-link">
                        <i class="bi bi-palette"></i>
                        Costume Projects
                    </a>
                    <a href="mascot_index.php" class="suggestion-link">
                        <i class="bi bi-emoji-smile"></i>
                        Mascot Projects
                    </a>
                    <a href="login.php" class="suggestion-link">
                        <i class="bi bi-shield-check"></i>
                        Admin Login
                    </a>
                </div>
            </div>
        </div>
    </body>

</html>
