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
    $site_name = $_POST['site_name'];
    $contact_email = $_POST['contact_email'];
    $support_phone = $_POST['support_phone'];
    $booking_fee = $_POST['booking_fee'];
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE settings SET value = ? WHERE setting_key = ?");
    
    $settings = [
        'site_name' => $site_name,
        'contact_email' => $contact_email,
        'support_phone' => $support_phone,
        'booking_fee' => $booking_fee,
        'maintenance_mode' => $maintenance_mode
    ];

    foreach ($settings as $key => $value) {
        $stmt->execute([$value, $key]);
    }

    header("Location: settings.php?success=1");
    exit();
}

// Get current settings
$settings_query = $conn->query("SELECT * FROM settings");
$settings = [];
while ($row = $settings_query->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['value'];
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <title>ڕێکخستنەکان | گەشتی ئاسمانی</title>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --primary-color: #1a237e;
            --primary-color-dark: #0d47a1;
            --text-dark: #0a192f;
            --text-light: #64748b;
            --extra-light: #f8fafc;
            --white: #ffffff;
            --sidebar-width: 250px;
            --gradient-start: #0a192f;
            --gradient-end: #1e3a8a;
            --success-color: #22c55e;
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

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(to bottom, var(--gradient-start), var(--gradient-end));
            padding: 2rem 1.5rem;
            border-left: 1px solid rgba(0,0,0,0.05);
            position: fixed;
            height: 100vh;
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
            margin-right: var(--sidebar-width);
            padding: 2rem;
            width: calc(100% - var(--sidebar-width));
        }

        .settings-form {
            background: var(--white);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            max-width: 800px;
            margin: 0 auto;
        }

        .sidebar h2 {
            color: var(--white);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: var(--white);
        }

        .stat-card h3 {
            color: var(--extra-light);
        }

        .stat-card p {
            color: var(--white);
        }

        .btn {
            background: linear-gradient(to right, var(--gradient-start), var(--gradient-end));
        }

        .btn:hover {
            background: linear-gradient(to right, var(--gradient-end), var(--gradient-start));
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

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .form-group.checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group.checkbox input {
            width: auto;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background-color: var(--primary-color-dark);
        }

        .success-message {
            background-color: var(--success-color);
            color: var(--white);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="sidebar">
            <h2>بەڕێوەبەر</h2>
            <ul class="nav-links">
                <li><a href="admin_dashboard.php"><i class="ri-dashboard-line"></i> داشبۆرد</a></li>
                <li><a href="manage_users.php"><i class="ri-user-line"></i> بەکارهێنەران</a></li>
                <li><a href="manage_flights.php"><i class="ri-flight-takeoff-line"></i> گەشتەکان</a></li>
                <li><a href="settings.php"><i class="ri-settings-line"></i> ڕێکخستنەکان</a></li>
                <li><a href="logout.php"><i class="ri-logout-box-line"></i> چوونەدەرەوە</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="settings-form">
                <h2>ڕێکخستنەکانی سیستەم</h2>
                <?php if (isset($_GET['success'])): ?>
                    <div class="success-message">
                        ڕێکخستنەکان بە سەرکەوتوویی پاشەکەوت کران
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>ناوی سایت</label>
                        <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>ئیمەیڵی پەیوەندی</label>
                        <input type="email" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>ژمارەی پشتگیری</label>
                        <input type="text" name="support_phone" value="<?php echo htmlspecialchars($settings['support_phone'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>کرێی داواکاری</label>
                        <input type="number" name="booking_fee" step="0.01" value="<?php echo htmlspecialchars($settings['booking_fee'] ?? '0'); ?>" required>
                    </div>
                    <div class="form-group checkbox">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" <?php echo ($settings['maintenance_mode'] ?? 0) ? 'checked' : ''; ?>>
                        <label for="maintenance_mode">دۆخی چاککردنەوە</label>
                    </div>
                    <button type="submit" class="btn">پاشەکەوتکردن</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
