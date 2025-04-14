<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Showcase Slideshow</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E88E5;
            --secondary: #5e35b1;
            --accent: #4CAF50;
            --light: #F8F9FA;
            --dark: #212529;
            --gradient: linear-gradient(135deg, #1E88E5, #5e35b1);
            --text-color: #212529;
            --bg-color: #E8F1F8;
            --slide-bg: white;
            --slide-text: #666;
        }
        
        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            /* :root {
                --bg-color: #121212;
                --text-color: #F8F9FA;
                --slide-bg: #1E1E1E;
                --slide-text: #CCCCCC;
            } */
            
            .slide-badge {
                background: rgba(30, 136, 229, 0.2);
            }
            
            .nav-btn {
                background: #333;
                color: #F8F9FA;
            }
            
            .stat-label {
                color: #CCCCCC;
            }
            
            .indicator {
                background: rgba(255, 255, 255, 0.3);
            }
        }
        
        /* For sites with a class-based dark mode toggle */
        .dark-mode {
            --bg-color: #121212;
            --text-color: #F8F9FA;
            --slide-bg: #1E1E1E;
            --slide-text: #CCCCCC;
        }
        
        .dark-mode .slide-badge {
            background: rgba(30, 136, 229, 0.2);
        }
        
        .dark-mode .nav-btn {
            background: #333;
            color: #F8F9FA;
        }
        
        .dark-mode .stat-label {
            color: #CCCCCC;
        }
        
        .dark-mode .indicator {
            background: rgba(255, 255, 255, 0.3);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            overflow-x: hidden;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }
        
        .section-title p {
            color: var(--text-color);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Slideshow Container */
        .student-slideshow {
            position: relative;
            height: 500px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
        }
        
        /* Slides */
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transform: scale(1.1);
            transition: opacity 0.8s ease, transform 0.8s ease;
            display: flex;
            background: var(--slide-bg);
        }
        
        .slide.active {
            opacity: 1;
            transform: scale(1);
            z-index: 1;
        }
        
        /* Slide Content */
        .slide-image {
            flex: 1;
            overflow: hidden;
            position: relative;
        }
        
        .slide-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease;
        }
        
        .slide.active .slide-image img {
            animation: zoomInOut 10s ease infinite alternate;
        }
        
        .slide-content {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .slide-badge {
            display: inline-block;
            background: #E3F2FD;
            color: var(--primary);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 20px;
            transform: translateY(50px);
            opacity: 0;
            transition: all 0.5s ease 0.2s;
        }
        
        .slide.active .slide-badge {
            transform: translateY(0);
            opacity: 1;
        }
        
        .slide-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--text-color);
            transform: translateY(50px);
            opacity: 0;
            transition: all 0.5s ease 0.4s;
            line-height: 1.3;
        }
        
        .slide.active .slide-title {
            transform: translateY(0);
            opacity: 1;
        }
        
        .slide-description {
            font-size: 1rem;
            line-height: 1.7;
            color: var(--slide-text);
            margin-bottom: 30px;
            transform: translateY(50px);
            opacity: 0;
            transition: all 0.5s ease 0.6s;
        }
        
        .slide.active .slide-description {
            transform: translateY(0);
            opacity: 1;
        }
        
        .slide-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            transform: translateY(50px);
            opacity: 0;
            transition: all 0.5s ease 0.8s;
        }
        
        .slide.active .slide-stats {
            transform: translateY(0);
            opacity: 1;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
            display: block;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: #666;
        }
        
        .slide-button {
            display: inline-block;
            background: var(--gradient);
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(30, 136, 229, 0.3);
            transition: all 0.3s ease;
            transform: translateY(50px);
            opacity: 0;
            transition-property: transform, opacity, box-shadow, transform;
            transition-duration: 0.5s, 0.5s, 0.3s, 0.3s;
            transition-delay: 1s, 1s, 0s, 0s;
        }
        
        .slide.active .slide-button {
            transform: translateY(0);
            opacity: 1;
        }
        
        .slide-button:hover {
            box-shadow: 0 8px 25px rgba(30, 136, 229, 0.5);
            transform: translateY(-3px);
        }
        
        /* Decorative Elements */
        .slide-content::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(30, 136, 229, 0.05);
            z-index: -1;
        }
        
        .slide-content::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(94, 53, 177, 0.05);
            z-index: -1;
        }
        
        /* Slide Indicators */
        .slide-indicators {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }
        
        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }
        
        .indicator.active {
            background: var(--primary);
            transform: scale(1.2);
            border-color: white;
        }
        
        .indicator::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--primary);
            transform: translateX(-100%);
            transition: transform 5s linear;
        }
        
        .indicator.active::after {
            transform: translateX(0);
        }
        
        /* Navigation Arrows */
        .slide-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
            transform: translateY(-50%);
            z-index: 10;
        }
        
        .nav-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            opacity: 0.7;
        }
        
        .nav-btn:hover {
            background: var(--primary);
            color: white;
            opacity: 1;
            transform: scale(1.1);
        }
        
        /* Animations */
        @keyframes zoomInOut {
            0% {
                transform: scale(1);
            }
            100% {
                transform: scale(1.1);
            }
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .slide {
                flex-direction: column;
            }
            
            .slide-image, .slide-content {
                flex: none;
            }
            
            .slide-image {
                height: 250px;
            }
            
            .slide-content {
                padding: 30px;
            }
            
            .slide-title {
                font-size: 1.8rem;
            }
            
            .student-slideshow {
                height: auto;
                aspect-ratio: 1 / 1.2;
            }
        }
        
        @media (max-width: 768px) {
            .section-title h2 {
                font-size: 2rem;
            }
            
            .slide-stats {
                flex-wrap: wrap;
            }
            
            .stat-item {
                flex: 1 0 40%;
            }
            
            .nav-btn {
                width: 40px;
                height: 40px;
            }
        }
        
        @media (max-width: 576px) {
            .section-title h2 {
                font-size: 1.8rem;
            }
            
            .slide-badge {
                font-size: 0.8rem;
            }
            
            .slide-title {
                font-size: 1.5rem;
            }
            
            .slide-description {
                font-size: 0.9rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .student-slideshow {
                aspect-ratio: 1 / 1.5;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="section-title">
            <h2>Student Achievements</h2>
            <!-- <p>Celebrating the outstanding accomplishments of our talented students</p> -->
        </div>
        
        <div class="student-slideshow">
            <!-- Slide 1 -->
            <div class="slide active">
                <div class="slide-image">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80" alt="Student Achievement">
                </div>
                <div class="slide-content">
                    <span class="slide-badge">ACADEMIC EXCELLENCE</span>
                    <h2 class="slide-title">National Science Competition Winners</h2>
                    <p class="slide-description">Our students took first place in the National Science Competition with their innovative project on renewable energy solutions for urban environments.</p>
                    <div class="slide-stats">
                        <div class="stat-item">
                            <span class="stat-number">15</span>
                            <span class="stat-label">Students</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">6</span>
                            <span class="stat-label">Months</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">1st</span>
                            <span class="stat-label">Place</span>
                        </div>
                    </div>
                    <a href="#" class="slide-button">Learn More</a>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="slide">
                <div class="slide-image">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80" alt="Student Achievement">
                </div>
                <div class="slide-content">
                    <span class="slide-badge">LEADERSHIP</span>
                    <h2 class="slide-title">Student Council Initiatives</h2>
                    <p class="slide-description">Our student council has successfully implemented several campus-wide initiatives focused on sustainability, mental health awareness, and community outreach.</p>
                    <div class="slide-stats">
                        <div class="stat-item">
                            <span class="stat-number">12</span>
                            <span class="stat-label">Projects</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">500+</span>
                            <span class="stat-label">Participants</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">3</span>
                            <span class="stat-label">Awards</span>
                        </div>
                    </div>
                    <a href="#" class="slide-button">Learn More</a>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="slide">
                <div class="slide-image">
                    <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Student Achievement">
                </div>
                <div class="slide-content">
                    <span class="slide-badge">ARTS & CULTURE</span>
                    <h2 class="slide-title">International Arts Festival Recognition</h2>
                    <p class="slide-description">Our performing arts students received standing ovations and special recognition at the International Youth Arts Festival for their original production.</p>
                    <div class="slide-stats">
                        <div class="stat-item">
                            <span class="stat-number">24</span>
                            <span class="stat-label">Performers</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">8</span>
                            <span class="stat-label">Countries</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">2</span>
                            <span class="stat-label">Awards</span>
                        </div>
                    </div>
                    <a href="#" class="slide-button">Learn More</a>
                </div>
            </div>
            
            <!-- Slide 4 -->
            <div class="slide">
                <div class="slide-image">
                    <img src="https://images.unsplash.com/photo-1543269865-cbf427effbad?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Student Achievement">
                </div>
                <div class="slide-content">
                    <span class="slide-badge">INNOVATION</span>
                    <h2 class="slide-title">Student Startup Success</h2>
                    <p class="slide-description">A team of our entrepreneurial students has secured seed funding for their educational technology startup that aims to make learning more accessible.</p>
                    <div class="slide-stats">
                        <div class="stat-item">
                            <span class="stat-number">5</span>
                            <span class="stat-label">Founders</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">$50K</span>
                            <span class="stat-label">Funding</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">2K+</span>
                            <span class="stat-label">Users</span>
                        </div>
                    </div>
                    <a href="#" class="slide-button">Learn More</a>
                </div>
            </div>
            
            <!-- Navigation -->
            <div class="slide-nav">
                <div class="nav-btn prev-btn">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="nav-btn next-btn">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            
            <!-- Indicators -->
            <div class="slide-indicators">
                <div class="indicator active" data-index="0"></div>
                <div class="indicator" data-index="1"></div>
                <div class="indicator" data-index="2"></div>
                <div class="indicator" data-index="3"></div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.slide');
            const indicators = document.querySelectorAll('.indicator');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            let currentSlide = 0;
            let slideInterval;
            
            // Initialize slideshow
            function initSlideshow() {
                showSlide(currentSlide);
                startSlideshow();
                
                // Add event listeners
                prevBtn.addEventListener('click', prevSlide);
                nextBtn.addEventListener('click', nextSlide);
                
                indicators.forEach(indicator => {
                    indicator.addEventListener('click', function() {
                        const slideIndex = parseInt(this.getAttribute('data-index'));
                        showSlide(slideIndex);
                        resetInterval();
                    });
                });
                
                // Pause slideshow on hover
                document.querySelector('.student-slideshow').addEventListener('mouseenter', pauseSlideshow);
                document.querySelector('.student-slideshow').addEventListener('mouseleave', startSlideshow);
            }
            
            // Show specific slide
            function showSlide(index) {
                // Hide all slides
                slides.forEach(slide => {
                    slide.classList.remove('active');
                });
                
                // Remove active class from all indicators
                indicators.forEach(indicator => {
                    indicator.classList.remove('active');
                });
                
                // Show current slide and indicator
                currentSlide = index;
                slides[currentSlide].classList.add('active');
                indicators[currentSlide].classList.add('active');
                
                // Reset the animation for the active indicator
                indicators[currentSlide].style.animation = 'none';
                setTimeout(() => {
                    indicators[currentSlide].style.animation = '';
                }, 10);
            }
            
            // Next slide
            function nextSlide() {
                let next = currentSlide + 1;
                if (next >= slides.length) next = 0;
                showSlide(next);
                resetInterval();
            }
            
            // Previous slide
            function prevSlide() {
                let prev = currentSlide - 1;
                if (prev < 0) prev = slides.length - 1;
                showSlide(prev);
                resetInterval();
            }
            
            // Start automatic slideshow
            function startSlideshow() {
                slideInterval = setInterval(nextSlide, 5000);
            }
            
            // Pause slideshow
            function pauseSlideshow() {
                clearInterval(slideInterval);
            }
            
            // Reset interval
            function resetInterval() {
                clearInterval(slideInterval);
                startSlideshow();
            }
            
            // Initialize
            initSlideshow();
        });
    </script>
</body>
</html>