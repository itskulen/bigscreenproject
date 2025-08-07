<?php
// Set HTTP status code 500
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>500 - Internal Server Error</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/x-icon" href="favicon.ico">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <style>
            body {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%);
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
                color: #dc2626;
                margin-bottom: 20px;
                display: block;
            }

            .error-code {
                font-size: 3rem;
                font-weight: 800;
                color: #dc2626;
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

            .btn-home {
                background: linear-gradient(135deg, #dc2626, #b91c1c);
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
                box-shadow: 0 2px 10px rgba(220, 38, 38, 0.3);
            }

            .btn-home:hover {
                background: linear-gradient(135deg, #b91c1c, #991b1b);
                box-shadow: 0 3px 10px rgba(220, 38, 38, 0.4);
                color: #fff;
                text-decoration: none;
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
            }
        </style>
    </head>

    <body>
        <div class="error-container">
            <i class="bi bi-exclamation-octagon error-icon"></i>

            <div class="error-code">500</div>
            <div class="error-title">Internal Server Error</div>

            <div class="error-desc">
                Something went wrong on our server.<br>
                Please try again later or contact the administrator if the problem persists.
            </div>

            <a href="index.php" class="btn-home">
                <i class="bi bi-house"></i>
                Go Home
            </a>
        </div>
    </body>

</html>
