<?php
require_once 'config/Database.php';

// Database connection and query
$database = new Database();

$conn = $database->connect();
$sql = "SELECT * FROM flights ORDER BY flight_date DESC";
$result = $conn->query($sql);

$row = $result->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
    <title>سلێمانی | گەشتی ئاسمانی</title>
    <style>
        @font-face {
            font-family: '20_Sarchia_Banoka_1';
            src: url('fonts/20_Sarchia_Banoka_1.ttf') format('truetype');
        }

        :root {
            --primary-color: #3d5cb8;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
            --white: #ffffff;
            --background-light: #f8f9fa;
            --shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: '20_Sarchia_Banoka_1';
        }

        body {
          background: linear-gradient(25deg,rgb(36, 197, 255),rgb(0, 0, 0));
        }

        .nav {
            background: var(--white);
            padding: 1rem 5%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .nav__container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .nav__logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .nav__links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav__link {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav__link:hover {
            color: var(--primary-color);
        }

        .header__container {
            margin-top: 80px;
            position: relative;
            overflow: hidden;
        }

        .header__content {
            text-align: center;
            padding: 2rem 1rem;
        }

        .section__header {
    font-size: 2.5rem;
    color: var(--text-dark);
    margin-bottom: 2rem;
    animation: floatText 3s ease-in-out infinite;
}

@keyframes floatText {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
    100% {
        transform: translateY(0px);
    }
}



        .header__image {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            
            max-width: 1200px;
            margin: 0 auto;
        }

        .main-image {
            width: 100%;
            height: auto;
            transition: transform 0.5s ease;
        }

        .services-section {
            padding: 4rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        .service-card {
            text-align: center;
            padding: 2rem;
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-card i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .service-card h3 {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .service-card p {
            color: var(--text-light);
        }

        .flights-section {
            padding: 4rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .flights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        .flight-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .flight-card:hover {
            transform: translateY(-5px);
        }

        .flight-header {
            background: var(--primary-color);
            color: var(--white);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .flight-info {
            padding: 1.5rem;
        }

        .route {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .route h3 {
            color: var(--text-dark);
            font-size: 1.2rem;
        }

        .details {
            margin-bottom: 1rem;
        }

        .details p {
            color: var(--text-light);
            margin: 0.5rem 0;
        }

        .book-btn {
            width: 100%;
            background: linear-gradient(135deg,rgb(133, 0, 0) 0%,rgb(69, 32, 26) 100%);
            color: var(--white);
            border: none;
            padding: 0.8rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .book-btn:hover {
            background: var(--secondary-color);
        }

        .contact-section {
            padding: 4rem 1rem;
            background: var(--white);
        }

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 3rem;
            margin-top: 2rem;
        }

        .contact-info {
            display: grid;
            gap: 2rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background: var(--background-light);
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .contact-item:hover {
            transform: translateY(-5px);
        }

        .contact-item i {
            font-size: 2rem;
            color: var(--primary-color);
        }

        .contact-item h3 {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .contact-item p {
            color: var(--text-light);
        }

        .contact-map {
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1001;
            animation: fadeIn 0.3s;
        }

        .modal-content {
            background: var(--white);
            width: 90%;
            max-width: 400px;
            margin: 15% auto;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.2);
            text-align: center;
            animation: slideIn 0.4s;
        }

        .modal-title {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .modal-btn {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .modal-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
       

.register-btn {
  background: linear-gradient(135deg,rgb(2, 95, 67) 0%,rgb(69, 32, 26) 100%);
    color: var(--white);
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    
}

.register-btn:hover {
    background: var(--secondary-color);
    
    box-shadow: var(--shadow);
}

.register-btn i {
  
}

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-100px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 768px) {
            .nav__links {
                display: none;
            }

            .section__header {
                font-size: 2rem;
            }

            .flights-grid {
                grid-template-columns: 1fr;
            }

            .contact-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav style="border-radius:5px" class="nav">
        <div class="nav__container">
        <div class="header__buttons">
    <button class="register-btn">
        <i class="ri-user-add-line"></i>
        خۆت تۆمار بکە
    </button>
</div>
<ul s class="nav__links">
                <li><a href="#" class="nav__link">سەرەکی</a></li>
                <li><a href="#flights" class="nav__link">گەشتەکان</a></li>
                <li><a href="#services" class="nav__link">خزمەتگوزارییەکان</a></li>
                <li><a href="#contact" class="nav__link">پەیوەندی</a></li>
            </ul>
            <div style="color: darkgreen; text-align: center;  font-size: 1.5rem;
            font-weight: 700;
            " >سلێمانی</div>
           
        </div>
    </nav>

    <header  class="header__container">

        <div style="background-color:white; border-radius:5px;"  class="header__content">
            <h1 class="section__header" style="color:darkgreen">گەشتێکی خۆش تۆمار بکە</h1>
            <div  class="header__image">
                <img src="assets/header.jpg" alt="Airplane view" class="main-image" />
            </div>
        </div>
    </header><br>

    <section class="services-section" id="services">
        <h2 class="section__header">خزمەتگوزارییەکان</h2>
        <div class="services-grid">
            <div class="service-card">
                <i class="ri-suitcase-line"></i>
                <h3>بارهەڵگرتن</h3>
                <p>تا ٢٣ کیلۆ بار بەخۆڕایی</p>
            </div>
            <div class="service-card">
                <i class="ri-restaurant-line"></i>
                <h3>خواردن</h3>
                <p>خواردنی گەرم لەسەر فڕۆکە</p>
            </div>
            <div class="service-card">
                <i class="ri-wifi-line"></i>
                <h3>وایفای</h3>
                <p>ئینتەرنێتی خێرا لەسەر فڕۆکە</p>
            </div>
            <div class="service-card">
                <i class="ri-customer-service-2-line"></i>
                <h3>خزمەتگوزاری ٢٤/٧</h3>
                <p>پشتگیری بەردەوام</p>
            </div>
        </div>
    </section>

    <section class="flights-section" id="flights">
        <h2 style="color: white;" class="section__header">گەشتە بەردەستەکان</h2>
        <div class="flights-grid">
        <?php 
foreach($row as $flight) { ?>
    <div class="flight-card">
        <div class="flight-header">
            <i class="ri-flight-takeoff-line"></i>
            <span class="flight-date"><?php echo $flight['flight_date']; ?></span>
        </div>
        <div class="flight-info">
            <div class="route">
                <h3><?php echo $flight['destination']; ?></h3>
            </div>
            <div class="details">
                <p>کات: <?php echo $flight['departure_time']; ?></p>
                <p>نرخ: $<?php echo $flight['price']; ?></p>
                <p>کورسی بەردەست: <?php echo $flight['available_seats']; ?></p>
            </div>
            <button class="book-btn">
                گەشت تۆمار بکە
            </button>
        </div>
    </div>
<?php } ?>

        
        </div>
    </section>

    <section style="border-radius:20px" class="contact-section" id="contact">
        <div class="contact-container">
            <h2 class="section__header">پەیوەندیمان پێوە بکە</h2>
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="ri-map-pin-line"></i>
                        <div>
                            <h3>ناونیشان</h3>
                            <p>فڕۆکەخانەی نێودەوڵەتی سلێمانی</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="ri-phone-line"></i>
                        <div>
                            <h3>تەلەفۆن</h3>
                            <p>٠٧٧٠ ١٢٣ ٤٥٦٧</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="ri-mail-line"></i>
                        <div>
                        <h3>ئیمەیڵ</h3>
                            <p>info@sulairport.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="ri-time-line"></i>
                        <div>
                            <h3>کاتەکانی کار</h3>
                            <p>هەموو ڕۆژێک ٢٤ کاتژمێر</p>
                        </div>
                    </div>
                </div>
                <div class="contact-map">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3234.8235977772426!2d45.51473!3d35.561099!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzXCsDMzJzQwLjAiTiA0NcKwMzAnNTMuMCJF!5e0!3m2!1sen!2siq!4v1234567890!5m2!1sen!2siq" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <div class="modal" id="bookingModal">
        <div class="modal-content">
            <h2 class="modal-title">تۆمارکردن</h2>
            <p>تکایە خۆت تۆمار بکە</p>
            <button class="modal-btn" onclick="closeModal()">باشە</button>
        </div>
    </div>

    <script>
    document.querySelector('.register-btn').addEventListener('click', () => {
    window.location.href = 'register.php';
});

        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.nav');
            if (window.scrollY > 50) {
                nav.style.background = 'rgba(255,255,255,0.95)';
            } else {
                nav.style.background = 'var(--white)';
            }
        });

        const bookButtons = document.querySelectorAll('.book-btn');
        const modal = document.getElementById('bookingModal');

        bookButtons.forEach(button => {
            button.addEventListener('click', () => {
                modal.style.display = 'block';
            });
        });

        function closeModal() {
            modal.style.display = 'none';
            window.location.href = 'register.php';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>

