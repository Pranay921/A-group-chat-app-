<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Chat</title>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
        <script src="script.js"></script>
        <script src="favorites.js"></script>

        <style>
            /* Add this new CSS for instructor cards */
            .instructor-cards {
                display: flex;
                justify-content: space-between;
                margin: 0 auto;
                max-width: 1200px;
                gap: 20px;
                padding: 0 15px;
                flex-wrap: nowrap; /* Prevent wrapping */
            }
            
            /* Animation keyframes for course cards */
            @keyframes slideInFromLeft {
                from {
                    opacity: 0;
                    transform: translateX(-50px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes slideInFromRight {
                from {
                    opacity: 0;
                    transform: translateX(50px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes slideInFromBottom {
                from {
                    opacity: 0;
                    transform: translateY(50px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            /* Apply animations to course cards with standard animation properties */
            .courses-container .course-card:nth-child(3n+1) {
                opacity: 0;
                animation: slideInFromLeft 0.8s ease-out forwards;
                animation-delay: 0.2s;
            }
            
            .courses-container .course-card:nth-child(3n+2) {
                opacity: 0;
                animation: slideInFromBottom 0.8s ease-out forwards;
                animation-delay: 0.4s;
            }
            
            .courses-container .course-card:nth-child(3n+3) {
                opacity: 0;
                animation: slideInFromRight 0.8s ease-out forwards;
                animation-delay: 0.6s;
            }
            
            /* Existing profile card styles */
            .profile-card {
                max-width: 350px;
                margin: 20px 0;
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                font-family: Arial, sans-serif;
                flex: 1;
                min-width: 0; /* Allow cards to shrink below min-width */
            }
            
            /* Apply animations to instructor cards with the same pattern as course cards */
            .instructor-cards .profile-card:nth-child(3n+1) {
                opacity: 0;
                animation: slideInFromLeft 0.8s ease-out forwards;
                animation-delay: 0.2s;
            }
            
            .instructor-cards .profile-card:nth-child(3n+2) {
                opacity: 0;
                animation: slideInFromBottom 0.8s ease-out forwards;
                animation-delay: 0.4s;
            }
            
            .instructor-cards .profile-card:nth-child(3n+3) {
                opacity: 0;
                animation: slideInFromRight 0.8s ease-out forwards;
                animation-delay: 0.6s;
            }
            
            /* Make cards responsive on smaller screens */
            @media (max-width: 992px) {
                .instructor-cards {
                    flex-direction: column;
                    align-items: center;
                }
                
                .profile-card {
                    width: 100%;
                    max-width: 400px;
                }
            }
            
            .profile-header {
                background: linear-gradient(135deg, #87CEEB, #1E90FF);
                padding: 30px 20px;
                text-align: center;
                color: #fff;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                margin: 0 auto 15px;
                overflow: hidden;
                border: 3px solid #fff;
            }
            
            .profile-avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            /* Rest of your existing profile card styles */
            .profile-name {
                margin: 0;
                font-size: 24px;
                font-weight: 600;
            }
            
            .profile-title {
                margin: 5px 0 0;
                font-size: 14px;
                opacity: 0.9;
            }
            
            .profile-about {
                padding: 20px;
                text-align: center;
            }
            
            .profile-about h3 {
                margin-top: 0;
                color: #555;
                font-size: 16px;
            }
            
            .profile-about p {
                color: #666;
                font-size: 14px;
                line-height: 1.5;
            }
            
            .profile-social {
                display: flex;
                justify-content: center;
                padding: 20px 20px 20px;
                margin-top: 15px;
            }
            
            .social-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background-color: #f5f5f5;
                color: #555;
                margin: 0 5px;
                text-decoration: none;
                transition: all 0.3s ease;
            }
            .navbar {
                /* background-color: rgba(255, 255, 255, 0.95); */
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                position: sticky;
                top: 0;
                z-index: 1000;
            }
            .social-icon:hover {
                background: linear-gradient(135deg, #87CEEB, #1E90FF);
                color: #fff;
            }
            
            .profile-tabs {
                display: flex;
                border-top: 1px solid #eee;
            }
            
            .tab {
                flex: 1;
                text-align: center;
                padding: 15px 0;
                font-size: 14px;
                font-weight: 600;
                color: #555;
                cursor: pointer;
                transition: all 0.3s ease;
                border-bottom: 3px solid transparent;
            }
            
            .tab.active {
                color: #1E90FF;
                border-bottom-color: #1E90FF;
            }
            
            .tab-content {
                padding: 20px;
                color: #666;
                font-size: 14px;
                line-height: 1.5;
            }
            
            .experience-item {
                margin-bottom: 15px;
            }
            
            .experience-item h4 {
                margin: 0 0 5px;
                color: #444;
            }
            
            .company {
                font-weight: 600;
                color: #555;
                margin: 0 0 3px;
            }
            
            .period {
                color: #888;
                font-size: 12px;
                margin: 0 0 5px;
            }
            
            .contact-item {
                display: flex;
                align-items: center;
                margin-bottom: 10px;
            }
            
            .contact-item i {
                width: 30px;
                color: #6baed6;
            }
            
            .contact-item p {
                margin: 0;
            }
            .hero-section {
                position: relative;
                height: 85vh;
                background: linear-gradient(135deg, #1e88e5 0%, #5e35b1 100%);
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-top: 0; /* Ensure no gap between navbar and hero */
            }
            .hero-content {
                position: relative;
                z-index: 10;
                text-align: center;
                max-width: 1200px;
                padding: 0 20px;
            }

            .hero-title {
                font-size: 4rem;
                font-weight: 800;
                color: white;
                margin-bottom: 1rem;
                opacity: 0;
                transform: translateY(30px);
                animation: fadeInUp 0.8s ease forwards 0.2s;
            }

            .hero-subtitle {
                font-size: 1.5rem;
                color: rgba(255, 255, 255, 0.9);
                margin-bottom: 2rem;
                opacity: 0;
                transform: translateY(30px);
                animation: fadeInUp 0.8s ease forwards 0.4s;
            }

            .hero-buttons {
                display: flex;
                gap: 20px;
                justify-content: center;
                opacity: 0;
                transform: translateY(30px);
                animation: fadeInUp 0.8s ease forwards 0.6s;
            }

            .hero-btn {
                padding: 12px 30px;
                border-radius: 30px;
                font-weight: 600;
                font-size: 1rem;
                transition: all 0.3s ease;
                text-decoration: none;
                cursor: pointer;
            }

            .hero-btn-primary {
                background: white;
                color: #1e88e5;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }

            .hero-btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            }

            .hero-btn-secondary {
                background: transparent;
                color: white;
                border: 2px solid white;
            }

            .hero-btn-secondary:hover {
                background: rgba(255, 255, 255, 0.1);
                transform: translateY(-3px);
            }

            /* Animated elements */
            .floating-element {
                position: absolute;
                opacity: 0.2;
                background: white;
                animation: float 15s infinite ease-in-out;
            }

            .element-1 {
                width: 150px;
                height: 150px;
                top: 10%;
                left: 10%;
                border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
                animation-delay: 0s;
            }

            .element-2 {
                width: 80px;
                height: 80px;
                top: 60%;
                left: 15%;
                border-radius: 50%;
                animation-delay: 2s;
            }

            .element-3 {
                width: 200px;
                height: 200px;
                top: 20%;
                right: 15%;
                border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
                animation-delay: 4s;
            }

            .element-4 {
                width: 100px;
                height: 100px;
                bottom: 20%;
                right: 10%;
                border-radius: 50%;
                animation-delay: 6s;
            }

            .element-5 {
                width: 120px;
                height: 120px;
                bottom: 30%;
                left: 30%;
                border-radius: 30% 70% 50% 50% / 50% 50% 70% 30%;
                animation-delay: 8s;
            }

            /* Floating animation */
            @keyframes float {
                0% {
                    transform: translate(0, 0) rotate(0deg);
                }
                25% {
                    transform: translate(20px, 35px) rotate(8deg);
                }
                50% {
                    transform: translate(10px, -20px) rotate(15deg);
                }
                75% {
                    transform: translate(-20px, 10px) rotate(8deg);
                }
                100% {
                    transform: translate(0, 0) rotate(0deg);
                }
            }

            /* Fade in up animation */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Dark mode adjustments */
            .dark-mode .hero-section {
                background: linear-gradient(135deg, #0d47a1 0%, #4527a0 100%);
            }

            .dark-mode .hero-btn-primary {
                background: #f5f5f5;
                color: #0d47a1;
            }

            /* Responsive adjustments */
            @media screen and (max-width: 768px) {
                .hero-title {
                    font-size: 2.5rem;
                }
                .hero-subtitle {
                    font-size: 1.2rem;
                }
                .hero-buttons {
                    flex-direction: column;
                    align-items: center;
                }
                .hero-btn {
                    width: 100%;
                    max-width: 250px;
                    text-align: center;
                }
            }

            /* Add this new CSS for instructor cards */
            .instructor-cards {
                display: flex;
                justify-content: space-between;
                margin: 0 auto;
                max-width: 1200px;
                gap: 20px;
                padding: 0 15px;
                flex-wrap: nowrap; /* Prevent wrapping */
            }
            
            /* Animation keyframes for course cards */
            @keyframes slideInFromLeft {
                from {
                    opacity: 0;
                    transform: translateX(-50px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes slideInFromRight {
                from {
                    opacity: 0;
                    transform: translateX(50px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }
            
            @keyframes slideInFromBottom {
                from {
                    opacity: 0;
                    transform: translateY(50px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    </head>
<body>
    <div id="top"></div>
    <div class="chat-toggle" id="chat-toggle">
        <i class="fas fa-comments"></i>
    </div>
    
    <div class="chat-wrapper" id="chat-wrapper">
        <div class="chat-container">
            <div class="chat-header">
                <h2>Student Chat</h2>
                <div class="header-info">
                    <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                    <button onclick="window.location.href='logout.php'" class="logout-btn">Logout</button>
                    <button class="close-chat" id="close-chat">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="chat-messages" id="chat-messages">
                <!-- Messages will be loaded here -->
            </div>
            
            <div class="chat-input">
                <form id="chat-form">
                    <input type="text" id="message" name="message" placeholder="Type your message...">
                    <input type="file" id="file-input" name="file" class="file-input">
                    <button type="button" id="attach-btn">📎</button>
                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Header section -->
    <header class="header">
        <div class="header-container">
            <div class="menu-btn">
                <span class="menu-btn__burger"></span>
            </div>
        </div>
    </header>

    <nav class="navbar">
        <a href="index.php" class="nav-logo">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS53HSdYUOOOkjwiRYWJ1U081ZsMZz_UZ9Brw&s" alt="Logo">
        </a>
        <div class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="nav-content">
            <div class="nav-links">
                <a href="#top" class="active">Home</a>
                <a href="#instructor-section" class="nav-link">About Us</a>
                <a href="#welcome-tag" class="nav-link">Contact Us</a>
                <a href="#blog-section" class="nav-link">Blog</a>
                <a href="favorites.php" class="nav-link">Favourites</a>
                <a href="feedback.php" class="nav-link"><i class="fas fa-comment-dots"></i> Feedback</a>
                
            </div>
            <div class="theme-toggle">
                <input type="checkbox" id="theme-switch" class="theme-switch">
                <label for="theme-switch" class="switch-label">
                    <i class="fas fa-sun"></i>
                    <i class="fas fa-moon"></i>
                    <span class="switch-handle"></span>
                </label>
            </div>
        </div>
    </nav>
    <div class="nav-slider-spacer"></div>
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Elevate Your Learning Journey</h1>
            <p class="hero-subtitle">Discover expert-led courses designed to transform your skills and advance your career</p>
            <div class="hero-buttons">
                <a href="#courses-section" class="hero-btn hero-btn-primary">Explore Courses</a>
                <a href="#instructor-section" class="hero-btn hero-btn-secondary">Meet Our Instructors</a>
            </div>
        </div>
        <!-- Animated background elements -->
        <div class="floating-element element-1"></div>
        <div class="floating-element element-2"></div>
        <div class="floating-element element-3"></div>
        <div class="floating-element element-4"></div>
        <div class="floating-element element-5"></div>
    </section>
    
    <div class="counter-section">
        <h1 class="counter-heading">Our <span>student community</span> is more than one million strong</h1>
        <p class="counter-subheading">(and this is just the beginning)</p>
        
        <div class="counter-container">
            <div class="counter-item">
                <h2><span class="counter" data-target="1">0</span>M+</h2>
                <p>community members</p>
            </div>
            <div class="counter-item">
                <h2><span class="counter" data-target="19">0</span>M+</h2>
                <p>study sessions</p>
            </div>
            <div class="counter-item">
                <h2><span class="counter" data-target="4">0</span>M+</h2>
                <p>study goals reached</p>
            </div>
            <div class="counter-item">
                <h2><span class="counter" data-target="215">0</span>+</h2>
                <p>countries</p>
            </div>
            <div class="counter-item">
                <h2><span class="counter" data-target="4.8">0</span>/5</h2>
                <p>positive reviews</p>
            </div>
        </div>
    </div>
    <section id="courses-section" class="courses-section">
        <h2>Our Online Courses</h2>
        <div class="courses-container">
            <div class="course-card">
                <div class="course-image">
                    <img src="https://elearn.websitelayout.net/img/content/courses-01.jpg" alt="Business Course">
                    <span class="course-tag business">BUSINESS</span>
                    <button class="favorite-btn"><i class="far fa-heart"></i></button>
                </div>
                <div class="course-content">
                    <div class="instructor">
                        <img src="https://elearn.websitelayout.net/img/avatar/avatar-01.jpg" alt="Elijah Lions" class="instructor-img">
                        <span class="instructor-name">Elijah Lions</span>
                    </div>
                    <h3 class="course-title">Figuring out how to compose as an expert creator</h3>
                    <div class="course-info">
                        <span><i class="fas fa-book-open"></i> 10 Lessons</span>
                        <span><i class="fas fa-users"></i> 23</span>
                        <span><i class="fas fa-star"></i> 5.00(1)</span>
                    </div>
                    <div class="course-footer">
                        <span class="level-tag">ALL LEVELS</span>
                        <span class="price">$55.00</span>
                    </div>
                </div>
            </div>

            <div class="course-card">
                <div class="course-image">
                    <img src="https://elearn.websitelayout.net/img/content/courses-02.jpg" alt="Design Course">
                    <span class="course-tag design">DESIGN</span>
                    <button class="favorite-btn"><i class="far fa-heart"></i></button>
                </div>
                <div class="course-content">
                    <div class="instructor">
                        <img src="https://elearn.websitelayout.net/img/avatar/avatar-02.jpg" alt="Georgia Train" class="instructor-img">
                        <span class="instructor-name">Georgia Train</span>
                    </div>
                    <h3 class="course-title">Configuration instruments for communication</h3>
                    <div class="course-info">
                        <span><i class="fas fa-book-open"></i> 09 Lessons</span>
                        <span><i class="fas fa-users"></i> 15</span>
                        <span><i class="fas fa-star"></i> 4.00(2)</span>
                    </div>
                    <div class="course-footer">
                        <span class="level-tag">BEGINNER</span>
                        <span class="price">$35.00</span>
                    </div>
                </div>
            </div>

            <div class="course-card">
                <div class="course-image">
                    <img src="https://elearn.websitelayout.net/img/content/courses-03.jpg" alt="Network Course">
                    <span class="course-tag network">NETWORK</span>
                    <button class="favorite-btn"><i class="far fa-heart"></i></button>
                </div>
                <div class="course-content">
                    <div class="instructor">
                        <img src="https://elearn.websitelayout.net/img/avatar/avatar-03.jpg" alt="Christian Hope" class="instructor-img">
                        <span class="instructor-name">Christian Hope</span>
                    </div>
                    <h3 class="course-title">Introduction to community training course</h3>
                    <div class="course-info">
                        <span><i class="fas fa-book-open"></i> 20 Lessons</span>
                        <span><i class="fas fa-users"></i> 20</span>
                        <span><i class="fas fa-star"></i> 5.00(3)</span>
                    </div>
                    <div class="course-footer">
                        <span class="level-tag">EXPERT</span>
                        <span class="price">$99.00</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="courses-container">
            <div class="course-card">
                <div class="course-image">
                    <img src="https://elearn.websitelayout.net/img/content/courses-04.jpg" alt="Business Course">
                    <span class="course-tag business">BUSINESS</span>
                    <button class="favorite-btn"><i class="far fa-heart"></i></button>
                </div>
                <div class="course-content">
                    <div class="instructor">
                        <img src="https://elearn.websitelayout.net/img/avatar/avatar-04.jpg" alt="Elijah Lions" class="instructor-img">
                        <span class="instructor-name">Elijah Lions</span>
                    </div>
                    <h3 class="course-title">Figuring out how to compose as an expert creator</h3>
                    <div class="course-info">
                        <span><i class="fas fa-book-open"></i> 10 Lessons</span>
                        <span><i class="fas fa-users"></i> 23</span>
                        <span><i class="fas fa-star"></i> 5.00(1)</span>
                    </div>
                    <div class="course-footer">
                        <span class="level-tag">ALL LEVELS</span>
                        <span class="price">$55.00</span>
                    </div>
                </div>
            </div>

            <div class="course-card">
                <div class="course-image">
                    <img src="https://elearn.websitelayout.net/img/content/courses-05.jpg" alt="Design Course">
                    <span class="course-tag design">DESIGN</span>
                    <button class="favorite-btn"><i class="far fa-heart"></i></button>
                </div>
                <div class="course-content">
                    <div class="instructor">
                        <img src="https://elearn.websitelayout.net/img/avatar/avatar-05.jpg" alt="Georgia Train" class="instructor-img">
                        <span class="instructor-name">Georgia Train</span>
                    </div>
                    <h3 class="course-title">Configuration instruments for communication</h3>
                    <div class="course-info">
                        <span><i class="fas fa-book-open"></i> 09 Lessons</span>
                        <span><i class="fas fa-users"></i> 15</span>
                        <span><i class="fas fa-star"></i> 4.00(2)</span>
                    </div>
                    <div class="course-footer">
                        <span class="level-tag">BEGINNER</span>
                        <span class="price">$35.00</span>
                    </div>
                </div>
            </div>

            <div class="course-card">
                <div class="course-image">
                    <img src="https://elearn.websitelayout.net/img/content/courses-06.jpg" alt="Network Course">
                    <span class="course-tag network">NETWORK</span>
                    <button class="favorite-btn"><i class="far fa-heart"></i></button>
                </div>
                <div class="course-content">
                    <div class="instructor">
                        <img src="https://elearn.websitelayout.net/img/avatar/avatar-06.jpg" alt="Christian Hope" class="instructor-img">
                        <span class="instructor-name">Christian Hope</span>
                    </div>
                    <h3 class="course-title">Introduction to community training course</h3>
                    <div class="course-info">
                        <span><i class="fas fa-book-open"></i> 20 Lessons</span>
                        <span><i class="fas fa-users"></i> 20</span>
                        <span><i class="fas fa-star"></i> 5.00(3)</span>
                    </div>
                    <div class="course-footer">
                        <span class="level-tag">EXPERT</span>
                        <span class="price">$99.00</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <div class="categories-section">
        <div class="instructors-tag">INSTRUCTORS</div>
        <h2 class="categories-title">Popular Categories</h2>
        
        <div class="categories-grid">

            <div class="category-card">
                <a href="chemistrycourse.html" style="text-decoration: none; color: inherit;">
                    <div class="category-content">
                        <i class="fa-solid fa-atom category-icon"></i>
                        <h3>Chemistry</h3>
                    </div>
                </a>
            </div>
            
            <div class="category-card">
                <a href="physicscourse.html" style="text-decoration: none; color: inherit;">
                    <div class="category-content">
                        <i class="fa-solid fa-magnet category-icon"></i>
                        <h3>Physics</h3>
                    </div>
                </a>
            </div>
            
            <div class="category-card">
                <a href="languagecourse.html" style="text-decoration: none; color: inherit;">
                    <div class="category-content">
                        <i class="fa-solid fa-language category-icon"></i>
                        <h3>Language</h3>
                    </div>
                </a>
            </div>
            
            <div class="category-card">
                <a href="businesscourse.html" style="text-decoration: none; color: inherit;">
                    <div class="category-content">
                        <i class="fa-solid fa-briefcase category-icon"></i>
                        <h3>Business</h3>
                    </div>
                </a>
            </div>
            
            <div class="category-card">
                <a href="photographycourse.html" style="text-decoration: none; color: inherit;">
                    <div class="category-content">
                        <i class="fa-solid fa-camera category-icon"></i>
                        <h3>Photography</h3>
                    </div>
                </a>
            </div>
            
            <div class="category-card">
                <a href="rocketsciencecourse.html" style="text-decoration: none; color: inherit;">
                    <div class="category-content">
                        <i class="fa-solid fa-rocket category-icon"></i>
                        <h3>Rocket Science</h3>
                    </div>
                </a>
            </div>
            
            <div class="category-card">
                <a href="mathcourse.html" style="text-decoration: none; color: inherit;">
                    <div class="category-content">
                        <i class="fa-solid fa-calculator category-icon"></i>
                        <h3>Math</h3>
                    </div>
                </a>
            </div>
            
            <div class="category-card">
                <a href="foodrecipecourse.html" style="text-decoration: none; color: inherit;">
                    <div class="category-content">
                        <i class="fa-solid fa-utensils category-icon"></i>
                        <h3>Food & recipe</h3>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <section id="instructor-section" class="instructor-section">
        <h2 class="section-title">Experience Instructor</h2>
        
        <div class="instructor-cards">
            <?php
            // Database connection
            $servername = "localhost";
            $db_username = "root";
            $db_password = "";
            $database = "demochatapp";
            
            // Create a connection
            $conn = mysqli_connect($servername, $db_username, $db_password, $database);
            
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            
            // Get teachers from database
            $query = "SELECT * FROM teachers ORDER BY id DESC LIMIT 3";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0) {
                $counter = 1;
                while ($teacher = mysqli_fetch_assoc($result)) {
                    // Default image if none is provided
                    $image = !empty($teacher['image']) && file_exists($teacher['image']) 
                        ? $teacher['image'] 
                        : "https://randomuser.me/api/portraits/men/45.jpg";
            ?>
            <!-- Instructor Card <?php echo $counter; ?> -->
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($teacher['name']); ?>">
                    </div>
                    <h2 class="profile-name"><?php echo htmlspecialchars($teacher['name']); ?></h2>
                    <p class="profile-title"><?php echo htmlspecialchars($teacher['expertise']); ?></p>
                </div>
                
                <div class="profile-social">
                    <?php if (!empty($teacher['social_facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($teacher['social_facebook']); ?>" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    
                    <?php if (!empty($teacher['social_twitter'])): ?>
                    <a href="<?php echo htmlspecialchars($teacher['social_twitter']); ?>" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    
                    <?php if (!empty($teacher['social_linkedin'])): ?>
                    <a href="<?php echo htmlspecialchars($teacher['social_linkedin']); ?>" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                </div>
                
                <div class="profile-tabs">
                    <div class="tab active" data-tab="about<?php echo $counter; ?>">ABOUT</div>
                    <div class="tab" data-tab="experience<?php echo $counter; ?>">EXPERIENCE</div>
                    <div class="tab" data-tab="contact<?php echo $counter; ?>">CONTACT</div>
                </div>
                
                <div class="tab-content" id="about<?php echo $counter; ?>-content">
                    <p><?php echo htmlspecialchars($teacher['bio']); ?></p>
                </div>
                
                <div class="tab-content" id="experience<?php echo $counter; ?>-content" style="display: none;">
                    <div class="experience-item">
                        <h4><?php echo htmlspecialchars($teacher['expertise']); ?></h4>
                        <p class="company">Experience: <?php echo htmlspecialchars($teacher['experience']); ?> years</p>
                        <p class="period">Qualification: <?php echo htmlspecialchars($teacher['qualification']); ?></p>
                    </div>
                </div>
                
                <div class="tab-content" id="contact<?php echo $counter; ?>-content" style="display: none;">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <p><?php echo htmlspecialchars($teacher['email']); ?></p>
                    </div>
                    <?php if (!empty($teacher['phone'])): ?>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <p><?php echo htmlspecialchars($teacher['phone']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
                    $counter++;
                }
            } else {
                // If no teachers in database, show placeholder
            ?>
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Profile Picture">
                    </div>
                    <h2 class="profile-name">No Teachers Yet</h2>
                    <p class="profile-title">ADD TEACHERS IN ADMIN PANEL</p>
                </div>
                
                <div class="profile-about">
                    <p>No teachers have been added yet. Please add teachers through the admin panel.</p>
                </div>
            </div>
            <?php
            }
            
            // Close the database connection
            mysqli_close($conn);
            ?>
        </div>
    </section>
    <div class="welcome-section" id="welcome-section">
    <div class="container">
        <div class="row">
            <div class="left-section" id="left-section">
                <div class="image-container">
                    <img src="https://img.freepik.com/premium-photo/distance-learning-online-education-caucasian-smile-kid-boy-studying-home-with-book-writing_339191-1547.jpg" alt="Student studying" class="main-image">
                    <div class="experience-badge">
                        <span class="number">9+</span>
                        <div class="text">
                            <p>YEARS EXPERIENCE</p>
                            <p>JUST ACHIEVED</p>
                        </div>
                    </div>
                    <img src="https://elearn.websitelayout.net/img/content/about-02.jpg" alt="Student giving thumbs up" class="overlay-image">
                </div>
            </div>
            <div class="right-section">
                <span class="welcome-tag" id="welcome-tag">WELCOME!</span>
                <h1>Learn whenever, anyplace, at your own speed.</h1>
                <div class="quote">
                    <p>A spot to furnish understudies with sufficient information and abilities in an unpredictable world.</p>
                </div>
                <p class="description">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable.</p>
                <div class="contact-info">
                    <div class="phone">
                        <i class="fa-solid fa-phone"></i>
                        <div>
                            <h3>Phone Number</h3>
                            <p>8603538900</p>
                        </div>
                    </div>
                    <div class="email">
                        <i class="fa-solid fa-envelope"></i>
                        <div>
                            <h3>Email Address</h3>
                            <p>pranaydeep921@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
   
    </section>
    <section class="blog-section" id="blog-section">
        <h2 class="section-title">Our Latest Blog</h2>
        
        <div class="blog-container">
            <!-- Blog Card 1 -->
            <div class="blog-card">
                <div class="blog-image">
                    <img src="https://elearn.websitelayout.net/img/blog/blog-01.jpg" alt="Student studying">
                </div>
                <div class="blog-content">
                    <span class="blog-tag">CREATIVE</span>
                    <h3 class="blog-title">Skills that you can learn from eLearn.</h3>
                    <p class="blog-excerpt">Duty obligations of business frequently occur pleasures enjoy...</p>
                    <div class="blog-footer">
                        <a href="#" class="read-more">READ MORE</a>
                        <span class="blog-date">6 Jul 2023</span>
                    </div>
                </div>
            </div>

            <!-- Blog Card 2 (Center Image) -->
            <div class="blog-card blog-card-center">
                <img src="https://images.pexels.com/photos/8283962/pexels-photo-8283962.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="Graduate student">
            </div>

            <!-- Blog Card 3 -->
            <div class="blog-card blog-card-right">
                <div class="blog-content">
                    <span class="blog-tag">LEARNING</span>
                    <h3 class="blog-title">Is eLearn any good? 7 ways you can be certain.</h3>
                    <div class="blog-footer">
                        <a href="#" class="read-more">READ MORE</a>
                        <span class="blog-date">4 Jul 2023</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    

</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.profile-card');
        
        cards.forEach((card, cardIndex) => {
            const tabs = card.querySelectorAll('.tab');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Remove active class from all tabs in this card
                    tabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Hide all tab contents in this card
                    const tabContents = card.querySelectorAll('.tab-content');
                    tabContents.forEach(content => {
                        content.style.display = 'none';
                    });
                    
                    // Show the selected tab content
                    document.getElementById(tabId + '-content').style.display = 'block';
                });
            });
        });
    });
</script>
<!-- Add this at the end of the body, before the closing body tag -->
        <footer class="site-footer">
            <div class="footer-container">
                <div class="footer-section">
                    <h3 class="footer-title">Student Chat</h3>
                    <p class="footer-description">
                        Subscribe to our newsletter to watch more updates on course development
                        and press the bell icon to get immediate notification of latest courses.
                    </p>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Office</h3>
                    <address class="footer-address">
                        ITPL Road<br>
                        Whitefield, Bangalore<br>
                        Karnataka, PIN 560066<br>
                        India
                    </address>
                    <a href="mailto:contact@studentchat.com" class="footer-email">pranaydeep921@gmail.com</a><br>
                    <span class="footer-phone">+91 - 8603538900</span>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-title">Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#courses-section">Courses</a></li>
                        <li><a href="favorites.php">My Favorites</a></li>
                        <li><a href="#about-section">About Us</a></li>
                        <li><a href="#features-section">Features</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                     <h1>Follow us on </h1>
                    <div class="footer-social">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-pinterest-p"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>Student Chat © 2023 - All Rights Reserved</p>
            </div>
        </footer>

        <style>
            /* Footer Styles */
            .site-footer {
                background-color: #0a1929;
                color: #fff;
                padding: 60px 0 20px;
                margin-top: 60px;
                font-family: Arial, sans-serif;
            }
            
            .footer-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }
            
            .footer-section {
                flex: 1;
                min-width: 250px;
                margin-bottom: 30px;
                padding: 0 15px;
            }
            
            .footer-title {
                font-size: 18px;
                margin-bottom: 20px;
                position: relative;
                padding-bottom: 10px;
                color: #fff;
            }
            
            .footer-title::after {
                content: '';
                position: absolute;
                left: 0;
                bottom: 0;
                width: 50px;
                height: 2px;
                background-color: #1E90FF;
            }
            
            .footer-description {
                line-height: 1.6;
                color: #b8b8b8;
                margin-bottom: 20px;
            }
            
            .footer-address {
                font-style: normal;
                line-height: 1.8;
                color: #b8b8b8;
                margin-bottom: 15px;
            }
            
            .footer-email, .footer-phone {
                display: block;
                color: #b8b8b8;
                margin-bottom: 10px;
                text-decoration: none;
            }
            
            .footer-email:hover {
                color: #1E90FF;
            }
            
            .footer-links {
                list-style: none;
                padding: 0;
            }
            
            .footer-links li {
                margin-bottom: 12px;
            }
            
            .footer-links a {
                color: #b8b8b8;
                text-decoration: none;
                transition: color 0.3s;
            }
            
            .footer-links a:hover {
                color: #1E90FF;
            }
            
            .footer-input-group {
                display: flex;
                margin-bottom: 20px;
            }
            
            .footer-input-group input {
                flex: 1;
                padding: 12px 15px;
                border: none;
                border-radius: 4px 0 0 4px;
                background-color: #1a2a3a;
                color: #fff;
            }
            
            .footer-input-group input::placeholder {
                color: #b8b8b8;
            }
            
            .footer-submit-btn {
                background-color: #1E90FF;
                color: white;
                border: none;
                padding: 0 15px;
                border-radius: 0 4px 4px 0;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            
            .footer-submit-btn:hover {
                background-color: #1976d2;
            }
            
            .footer-social {
                display: flex;
                gap: 15px;
            }
            
            .social-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background-color: #1a2a3a;
                color: #fff;
                text-decoration: none;
                transition: all 0.3s;
            }
            
            .social-icon:hover {
                background-color: #1E90FF;
                transform: translateY(-3px);
            }
            
            .footer-bottom {
                text-align: center;
                padding-top: 30px;
                margin-top: 30px;
                border-top: 1px solid #1a2a3a;
                color: #b8b8b8;
                font-size: 14px;
            }
            
            /* Dark mode styles for footer */
            body.dark-mode .site-footer {
                background-color: #0a1929;
                color: #fff;
            }
            
            body.dark-mode .footer-title {
                color: #e0e0e0;
            }
            
            body.dark-mode .footer-description,
            body.dark-mode .footer-address,
            body.dark-mode .footer-email,
            body.dark-mode .footer-phone,
            body.dark-mode .footer-links a,
            body.dark-mode .footer-bottom {
                color: #b8b8b8;
            }
            
            body.dark-mode .footer-input-group input {
                background-color: #1a2a3a;
                color: #e0e0e0;
            }
            
            body.dark-mode .social-icon {
                background-color: #1a2a3a;
            }
            
            /* Responsive adjustments */
            @media (max-width: 768px) {
                .footer-container {
                    flex-direction: column;
                }
                
                .footer-section {
                    width: 100%;
                    margin-bottom: 40px;
                }
            }
        </style>
    </body>
</html>