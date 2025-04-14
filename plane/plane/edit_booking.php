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
    $booking_id = $_GET['id'];
    $stmt = $conn->prepare("
        SELECT b.*, f.destination, u.username 
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $passengers = $_POST['passengers'];
    
    $update_stmt = $conn->prepare("UPDATE bookings SET status = ?, passengers = ? WHERE id = ?");
    
    if ($update_stmt->execute([$status, $passengers, $booking_id])) {
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
    <title>دەستکاری داواکاری | گەشتی ئاسمانی</title>
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
            background: linear-gradient(135deg, #f6f8ff 0%, #ffffff 100%);
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

        .booking-info {
            padding: 2rem;
            background: rgba(30, 60, 114, 0.05);
            border-bottom: 1px solid rgba(30, 60, 114, 0.1);
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            color: #1e3c72;
        }

        .info-item i {
            width: 24px;
            text-align: center;
        }

        .edit-form {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

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
        }

        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.1);
            outline: none;
        }

        .status-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%231e3c72' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 1rem center;
            background-size: 1em;
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
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-primary);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            background: rgba(30, 60, 114, 0.1);
            color: #1e3c72;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <div class="edit-header">
            <i class="fas fa-ticket-alt"></i>
            <h1>دەستکاری داواکاری</h1>
        </div>

        <div class="booking-info">
            <div class="info-item">
                <i class="fas fa-user"></i>
                <span>بەکارهێنەر: <?php echo htmlspecialchars($booking['username']); ?></span>
            </div>
            <div class="info-item">
                <i class="fas fa-plane-departure"></i>
                <span>شوێن: <?php echo htmlspecialchars($booking['destination']); ?></span>
            </div>
            <div class="info-item">
                <i class="fas fa-calendar-alt"></i>
                <span>بەروار: <?php echo htmlspecialchars($booking['booking_date']); ?></span>
            </div>
        </div>

        <form method="POST" class="edit-form">
            <div class="form-group">
                <label>دۆخی داواکاری</label>
                <select name="status" class="form-control status-select">
                    <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>چاوەڕوان</option>
                    <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>پەسەندکراو</option>
                    <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>ڕەتکراوەتەوە</option>
                </select>
            </div>

            <div class="form-group">
                <label>ژمارەی گەشتیار</label>
                <input type="number" name="passengers" class="form-control" value="<?php echo $booking['passengers']; ?>" required>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i>
                نوێکردنەوە
            </button>
            <br>
            <button  class="close-btn"  onclick="window.location.href='admin_dashboard.php'">داخستن</button>
        </form>
    </div>
</body>
</html>
