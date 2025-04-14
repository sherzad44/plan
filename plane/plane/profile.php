<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <title>پڕۆفایل | گەشتی ئاسمانی</title>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --primary-color: #3d5cb8;
            --primary-color-dark: #334c99;
            --text-dark: #0f172a;
            --text-light: #64748b;
            --extra-light: #f1f5f9;
            --white: #ffffff;
            --max-width: 1200px;
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f6f8ff 0%, #ffffff 100%);
        }

        .section__container {
            max-width: var(--max-width);
            margin: auto;
            padding: 5rem 1rem;
        }

        .profile-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(107, 115, 255, 0.2);
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            position: relative;
            background: linear-gradient(45deg, var(--primary-color), var(--primary-color-dark));
            padding: 5px;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-info {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
        }

        .info-group {
            margin-bottom: 1.5rem;
        }

        .info-label {
            color: var(--text-light);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-value {
            padding: 1rem;
            background: var(--extra-light);
            border-radius: 10px;
            color: var(--text-dark);
        }

        .gradient-btn {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-color-dark));
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            margin-top: 1rem;
        }

        .gradient-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(61, 92, 184, 0.3);
        }

        .section__header {
            font-size: 2.5rem;
            font-weight: 600;
            line-height: 3rem;
            color: var(--text-dark);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
    </style>
</head>
<body>
    <div class="section__container">
        <h1 class="section__header">
            <i class="fas fa-user-circle"></i>
            پڕۆفایلی من
        </h1>

        <div class="profile-container">
           
            <div class="profile-info">
                <div class="info-group">
                    <div class="info-label">
                        <i class="fas fa-user"></i>
                        ناوی تەواو
                    </div>
                    <div class="info-value"><?php echo htmlspecialchars($user['fullname'] ?? ''); ?></div>
                </div>

                <div class="info-group">
                    
                    
                </div>

                <div class="info-group">
                    <div class="info-label">
                        <i class="fas fa-phone"></i>
                        ژمارەی مۆبایل
                    </div>
                    <div class="info-value"><?php echo htmlspecialchars($user['phone'] ?? ''); ?></div>
                </div>

                <button class="gradient-btn">
                    <i class="fas fa-edit"></i>
                    دەستکاری زانیارییەکان
                </button>
            </div>
        </div>
    </div>
</body>
</html>
