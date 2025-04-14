<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

$user_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    
    $role = $_POST['role'];
    
    $stmt = $conn->prepare("UPDATE users SET username = ?,  role = ? WHERE id = ?");
    $stmt->execute([$username,  $role, $user_id]);
    
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دەستکاریکردنی بەکارهێنەر | گەشتی ئاسمانی</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --gradient-primary: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            --gradient-dark: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg,rgb(255, 174, 0) 0%,rgb(69, 32, 26) 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .edit-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }

        .edit-header {
            text-align: center;
            margin-bottom: 2rem;
            color: #1e3c72;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #1e3c72;
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg,rgb(255, 174, 0) 0%,rgb(69, 32, 26) 100%);
            color: white;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .close-btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            background: darkred;
            color: white;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(30, 60, 114, 0.3);
        }

        .back-btn {
            display: inline-block;
            margin-top: 1rem;
            color: #1e3c72;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="edit-header">
            <h1>دەستکاریکردنی بەکارهێنەر</h1>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>ناو</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            

            <div class="form-group">
                <label>ڕۆڵ</label>
                <select name="role" required>
                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>بەکارهێنەر</option>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>بەڕێوەبەر</option>
                </select>
            </div>

            <button type="submit" class="submit-btn">نوێکردنەوە</button>
            <br><br>
            <button  class="close-btn"  onclick="window.location.href='admin_dashboard.php'">داخستن</button>
        </form>

       
    </div>
</body>
</html>
