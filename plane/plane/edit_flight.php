<?php
session_start();
require_once 'config/Database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$database = new Database();
$conn = $database->connect();

if (isset($_GET['id'])) {
    $flight_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
    $stmt->execute([$flight_id]);
    $flight = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination = $_POST['destination'];
    $departure_date = $_POST['departure_date'];
    $departure_time = $_POST['departure_time'];
    $price = $_POST['price'];
    $available_seats = $_POST['available_seats'];
    
    $update_stmt = $conn->prepare("
        UPDATE flights 
        SET destination = ?, departure_date = ?, departure_time = ?, 
            price = ?, available_seats = ? 
        WHERE id = ?
    ");
    
    if ($update_stmt->execute([$destination, $departure_date, $departure_time, $price, $available_seats, $flight_id])) {
        header("Location: admin_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دەستکاری گەشت | گەشتی ئاسمانی</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --gradient-primary: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            --gradient-dark: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            --shadow-primary: 0 10px 30px rgba(30, 60, 114, 0.3);
        }

        * {
            font-family: '20_Sarchia_Banoka_1';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg,rgb(16, 115, 7) 0%,rgb(69, 32, 26) 100%);
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .edit-container {
            width: 100%;
            max-width: 600px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }

        .edit-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .edit-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        .edit-form {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #1e3c72;
            font-weight: bold;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid rgba(30, 60, 114, 0.1);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
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
            background: var(--gradient-primary);
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            animation: fadeIn 0.5s ease-out 0.6s forwards;
            opacity: 0;
        }
        .close-btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            background: darkred;
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            animation: fadeIn 0.5s ease-out 0.6s forwards;
            opacity: 0;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-primary);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes slideIn {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .price-input {
            position: relative;
        }

        .price-input::before {
            content: '$';
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #1e3c72;
        }

        .price-input input {
            padding-left: 2.5rem;
        }

        .seats-input {
            position: relative;
        }

        .seats-input::before {
            content: '👥';
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .seats-input input {
            padding-left: 2.5rem;
        }
    </style>
</head>
<body>
    <div class="edit-container">
       
        <div class="edit-header">
            <i class="fas fa-plane"></i>
            <h1>دەستکاری گەشت</h1>
        </div>

        <form method="POST" class="edit-form">
            <div class="form-group">
                <label>شوێن</label>
                <input type="text" name="destination" class="form-control" 
                       value="<?php echo htmlspecialchars($flight['destination']); ?>" required>
            </div>

            <div class="form-group">
                <label>بەروار</label>
                <input type="date" name="departure_date" class="form-control" 
                       value="<?php echo htmlspecialchars($flight['departure_date']); ?>" required>
            </div>

            <div class="form-group">
                <label>کات</label>
                <input type="time" name="departure_time" class="form-control" 
                       value="<?php echo htmlspecialchars($flight['departure_time']); ?>" required>
            </div>

            <div class="form-group">
                <label>نرخ</label>
                <div class="price-input">
                    <input type="number" name="price" class="form-control" 
                           value="<?php echo htmlspecialchars($flight['price']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>کورسی بەردەست</label>
                <div class="seats-input">
                    <input type="number" name="available_seats" class="form-control" 
                           value="<?php echo htmlspecialchars($flight['available_seats']); ?>" required>
                </div>
            </div>
           
            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i>
                نوێکردنەوە
            </button> <br>
            <button  class="close-btn"  onclick="window.location.href='admin_dashboard.php'">داخستن</button>
        </form>
    </div>
</body>
</html>
