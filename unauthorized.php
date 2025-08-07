<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Unauthorized Access</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ef 0%, #f8fafc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .unauth-container {
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 36px 28px;
            text-align: center;
            max-width: 370px;
            width: 100%;
            animation: fadeIn 0.7s;
        }
        .unauth-icon {
            font-size: 2.7rem;
            color: #eab308;
            margin-bottom: 12px;
        }
        .unauth-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 10px;
        }
        .unauth-desc {
            color: #6b7280;
            font-size: 1rem;
            margin-bottom: 24px;
        }
        .btn-back {
            background: linear-gradient(135deg, #64748b, #94a3b8);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(100,116,139,0.08);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .btn-back:hover {
            background: linear-gradient(135deg, #475569, #64748b);
            color: #fff;
            text-decoration: none;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px);}
            to { opacity: 1; transform: translateY(0);}
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="unauth-container">
        <div class="unauth-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        <div class="unauth-title">Access Denied</div>
        <div class="unauth-desc">
            You do not have permission to access this page.<br>
            Please login with the correct account.
        </div>
        <a href="login.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Login
        </a>