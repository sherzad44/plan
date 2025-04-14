<?php
session_start();
require_once 'config/Database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $fullname = $_POST['fullname'];
    
    $database = new Database();
    $conn = $database->connect();
    
    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        $error = 'ئەم ناوە پێشتر بەکارهاتووە';
    } elseif ($password !== $confirm_password) {
        $error = 'وشە نهێنییەکان یەک ناگرنەوە';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, fullname, role) VALUES (?, ?, ?, 'user')");
        
        if ($stmt->execute([$username, $hashed_password, $fullname])) {
            $success = 'هەژمارەکەت بە سەرکەوتوویی دروستکرا';
            header("refresh:2;url=login.php");
        } else {
            $error = 'هەڵەیەک ڕوویدا، تکایە دووبارە هەوڵبدەوە';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خۆتۆمارکردن | گەشتی ئاسمانی</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --gradient-primary: linear-gradient(135deg, #0F2027 0%, #203A43 50%, #2C5364 100%);
            --gradient-secondary: linear-gradient(45deg, #1e3c72 0%, #000000 100%);
            --shadow-primary: 0 10px 30px rgba(15, 32, 39, 0.3);
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-primary);
            padding: 2rem;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-primary);
            animation: slideUp 0.5s ease-out;
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header i {
            font-size: 3rem;
            background: var(--gradient-secondary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group i {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #1e3c72;
        }

        .form-control {
            width: 100%;
            padding: 1rem 2.5rem 1rem 1rem;
            border: 2px solid rgba(30, 60, 114, 0.1);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
            outline: none;
            transform: translateY(-2px);
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            background: var(--gradient-secondary);
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-primary);
        }

        .message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.1);
            color: #ff0000;
        }

        .success-message {
            background: rgba(0, 255, 0, 0.1);
            color: #008000;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-link a {
            color: #1e3c72;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: #000000;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h1>خۆتۆمارکردن</h1>
        </div>

        <?php if ($error): ?>
            <div class="message error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success-message">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="fullname" class="form-control" placeholder="ناوی تەواو" required>
            </div>

            <div class="form-group">
                <i class="fas fa-user-tag"></i>
                <input type="text" name="username" class="form-control" placeholder="ناوی بەکارهێنەر" required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="وشەی نهێنی" required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" class="form-control" placeholder="دووبارەکردنەوەی وشەی نهێنی" required>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-user-plus"></i>
                خۆتۆمارکردن
            </button>
        </form>

        <div class="login-link">
            <p>هەژمارت هەیە؟ <a href="login.php">چوونەژوورەوە</a></p>
        </div>
    </div>
</body>
</html>
