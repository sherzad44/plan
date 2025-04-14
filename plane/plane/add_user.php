<?php
session_start();
require_once 'config/Database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role]);

    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <title>زیادکردنی بەکارهێنەر | گەشتی ئاسمانی</title>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --gradient-start: #0a192f;
            --gradient-end: #1e3a8a;
            --text-dark: #0a192f;
            --text-light: #64748b;
            --extra-light: #f8fafc;
            --white: #ffffff;
            --sidebar-width: 250px;
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--extra-light);
        }

       
        

       
        .nav-links {
            list-style: none;
        }

        .nav-links li {
            margin-bottom: 0.875rem;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem;
            color: var(--white);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .main-content {
          margin: auto; 
            padding: 2rem;
            width: calc(100% - var(--sidebar-width));
        }

        .add-form {
            background: var(--white);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--gradient-end);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(to right, var(--gradient-start), var(--gradient-end));
            color: var(--white);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: linear-gradient(to right, var(--gradient-end), var(--gradient-start));
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--text-light);
            margin-right: 1rem;
        }

        .btn-secondary:hover {
            background: var(--text-dark);
        }
    </style>
</head>
<body>
    
        

        <div class="main-content">
            <div class="add-form">
                <h2>زیادکردنی بەکارهێنەر</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>ناو</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>ئیمەیڵ</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>وشەی نهێنی</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>ڕۆڵ</label>
                        <select name="role" required>
                            <option value="user">بەکارهێنەر</option>
                            <option value="admin">بەڕێوەبەر</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn">زیادکردن</button>
                        <a href="admin_dashboard.php" class="btn btn-secondary">گەڕانەوە</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
