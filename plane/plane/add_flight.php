<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination = $_POST['destination'];
    $departure_date = $_POST['departure_date'];
    $departure_time = $_POST['departure_time'];
    $price = $_POST['price'];
    $available_seats = $_POST['available_seats'];

    $stmt = $conn->prepare("INSERT INTO flights (destination, departure_date, departure_time, price, available_seats) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$destination, $departure_date, $departure_time, $price, $available_seats]);

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>زیادکردنی گەشت | گەشتی ئاسمانی</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --gradient-primary: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            --shadow-primary: 0 10px 30px rgba(30, 60, 114, 0.3);
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f6f8ff 0%, #ffffff 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #1e3c72;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
        }

        button {
            width: 100%;
            padding: 1rem;
            background: var(--gradient-primary);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-primary);
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 1rem;
            color: #1e3c72;
            text-decoration: none;
        }

        .back-btn:hover {
            color: #2a5298;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-btn">
            <i class="fas fa-arrow-right"></i> گەڕانەوە
        </a>
        <h1>زیادکردنی گەشتی نوێ</h1>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>شوێن</label>
                <input type="text" name="destination" required>
            </div>
            
            <div class="form-group">
                <label>بەرواری گەشت</label>
                <input type="date" name="departure_date" required>
            </div>
            
            <div class="form-group">
                <label>کاتی گەشت</label>
                <input type="time" name="departure_time" required>
            </div>
            
            <div class="form-group">
                <label>نرخ</label>
                <input type="number" name="price" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label>ژمارەی کورسی بەردەست</label>
                <input type="number" name="available_seats" required>
            </div>
            
            <button type="submit">زیادکردنی گەشت</button>
        </form>
    </div>
</body>
</html>
