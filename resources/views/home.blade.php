<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ================================================
        META TAGS & SEO
    ================================================= -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Dynamic Page Metadata -->
    <title>UNZA Carbon Calculator</title>
    <meta name="description" content="Track your cooking emissions and earn carbon credits through clean cooking interventions">

    <!-- ================================================
        CSS & STYLING
    ================================================= -->
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Theme Styles -->
    <style>
        :root {
            /* Climate-friendly color palette */
            --primary: #22c55e;         /* Fresh green */
            --primary-dark: #16a34a;    /* Darker green */
            --secondary: #06b6d4;       /* Ocean blue */
            --accent: #f59e0b;          /* Warm amber */
            --success: #10b981;         /* Emerald */
            --warning: #f59e0b;         /* Amber */
            --danger: #ef4444;          /* Red coral */

            /* Neutral colors */
            --light: #f8fafc;           /* Cool white */
            --light-gray: #f1f5f9;      /* Light slate */
            --medium-gray: #64748b;     /* Slate */
            --dark: #0f172a;            /* Dark slate */
            --text-muted: #64748b;

            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            --gradient-secondary: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%);
            --gradient-hero: linear-gradient(135deg, #f0f9ff 0%, #ecfdf5 100%);

            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);

            /* Animations */
            --transition-fast: 0.15s ease-in-out;
            --transition-normal: 0.3s ease-in-out;
            --transition-slow: 0.5s ease-in-out;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: var(--gradient-hero);
            color: var(--dark);
            line-height: 1.6;
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        /* ================================================
            ANIMATIONS & KEYFRAMES
        ================================================= */
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        .animate-slide-in {
            animation: slideInLeft 0.6s ease-out;
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* ================================================
            NAVIGATION
        ================================================= */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(34, 197, 94, 0.1);
            transition: all var(--transition-normal);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary);
            transition: all var(--transition-fast);
            display: flex;
            align-items: center;
        }

        .navbar-brand:hover {
            color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .navbar-brand i {
            font-size: 1.5rem;
            margin-right: 0.5rem;
            animation: float 3s ease-in-out infinite;
        }

        .nav-link {
            color: var(--medium-gray);
            font-weight: 500;
            transition: all var(--transition-fast);
            position: relative;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .nav-link:hover {
            color: var(--primary);
            background: rgba(34, 197, 94, 0.1);
            transform: translateY(-1px);
        }

        .nav-link i {
            margin-right: 0.5rem;
        }

        /* ================================================
            BUTTONS
        ================================================= */
        .btn {
            border-radius: 12px;
            font-weight: 500;
            transition: all var(--transition-normal);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left var(--transition-slow);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .btn-primary:hover {
            background: var(--gradient-primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* ================================================
            CARDS
        ================================================= */
        .card {
            border-radius: 16px;
            border: none;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-normal);
            background: white;
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform var(--transition-normal);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ================================================
            ALERTS
        ================================================= */
        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: var(--shadow-sm);
            animation: slideInLeft 0.5s ease-out;
        }

        .alert-success {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }

        /* ================================================
            WELCOME SECTION
        ================================================= */
        .welcome-section {
            background: var(--gradient-hero);
            padding: 3rem 0;
            position: relative;
            overflow: hidden;
        }

        .welcome-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 2rem;
            box-shadow: var(--shadow-xl);
            animation: float 3s ease-in-out infinite;
        }

        .energy-tips-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            pointer-events: none;
            opacity: 0.4; /* Increased from 0.1 to 0.4 for better visibility */
        }

        .energy-tip {
            position: absolute;
            font-size: 1rem; /* Increased from 0.9rem */
            color: var(--primary);
            font-weight: 600; /* Increased from 500 to 600 for better visibility */
            animation: scrollTip 20s linear infinite;
            white-space: nowrap;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8); /* Added text shadow for better contrast */
            background: rgba(255, 255, 255, 0.1); /* Added subtle background */
            padding: 0.5rem 1rem; /* Added padding */
            border-radius: 20px; /* Added rounded corners */
            backdrop-filter: blur(2px); /* Added blur effect */
        }

        @keyframes scrollTip {
            0% {
                transform: translateX(100vw);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        .energy-tip:nth-child(1) {
            top: 20%;
            animation-delay: 0s;
        }

        .energy-tip:nth-child(2) {
            top: 40%;
            animation-delay: 5s;
        }

        .energy-tip:nth-child(3) {
            top: 60%;
            animation-delay: 10s;
        }

        .energy-tip:nth-child(4) {
            top: 80%;
            animation-delay: 15s;
        }

        /* ================================================
            FOOTER
        ================================================= */
        .footer {
            background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%);
            color: #e2e8f0;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--gradient-primary);
        }

        .footer h6 {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 0.8rem;
            font-size: 1rem;
        }

        .footer a {
            color: #cbd5e1;
            text-decoration: none;
            transition: all var(--transition-fast);
            font-size: 0.9rem;
        }

        .footer a:hover {
            color: var(--primary);
            transform: translateX(5px);
        }

        .footer .text-muted {
            color: #94a3b8 !important;
        }

        /* ================================================
            RESPONSIVE DESIGN
        ================================================= */
        @media (max-width: 768px) {
            .navbar-collapse {
                padding-top: 1rem;
                background: white;
                border-radius: 12px;
                margin-top: 1rem;
                box-shadow: var(--shadow-md);
            }

            .card {
                margin-bottom: 1rem;
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }

            /* Make energy tips more visible on mobile */
            .energy-tip {
                font-size: 0.9rem;
                padding: 0.3rem 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 1rem;
            }

            .card-body {
                padding: 1rem;
            }

            .footer {
                text-align: center;
            }

            .footer .col-md-4 {
                margin-bottom: 2rem;
            }
        }

        /* ================================================
            UTILITY CLASSES
        ================================================= */
        .text-gradient {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hover-lift {
            transition: transform var(--transition-fast);
        }

        .hover-lift:hover {
            transform: translateY(-3px);
        }

        /* ================================================
            LOADING STATES
        ================================================= */
        .loading {
            position: relative;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid var(--primary);
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* ================================================
            DROPDOWN ENHANCEMENTS
        ================================================= */
        .dropdown-menu {
            border-radius: 12px;
            border: none;
            box-shadow: var(--shadow-lg);
            background: white;
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.3s ease-out;
        }

        .dropdown-item {
            color: var(--medium-gray);
            transition: all var(--transition-fast);
            border-radius: 8px;
            margin: 0.25rem 0.5rem;
        }

        .dropdown-item:hover {
            background: rgba(34, 197, 94, 0.1);
            color: var(--primary);
            transform: translateX(5px);
        }

        /* ================================================
            RIPPLE EFFECT
        ================================================= */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 600ms linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- ================================================
        HEADER & NAVIGATION
    ================================================= -->
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg shadow-sm">
            <div class="container">
                <!-- Brand Logo -->
                <a class="navbar-brand animate-slide-in" href="#">
                    <i class="fas fa-leaf text-success"></i>
                    <span class="fw-bold">UNZA Carbon Calculator</span>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                                <!-- Navigation Links -->
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav me-auto">
                        <!-- No home or dashboard links here -->
                    </ul>

                    <!-- Auth Links -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link hover-lift" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i>Login
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-primary" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    </ul>
                </div>


                </div>
            </div>
        </nav>
    </header>

    <!-- ================================================
        MAIN CONTENT AREA
    ================================================= -->
    <main class="flex-grow-1 py-4">
        <div class="container">
            <!-- Welcome Section -->
            <div class="welcome-section position-relative mb-5">
                <div class="energy-tips-background">
                    <div class="energy-tip">💡 Use energy-efficient LED bulbs to reduce electricity consumption</div>
                    <div class="energy-tip">🔥 Cook with lids on pots to save up to 30% cooking energy</div>
                    <div class="energy-tip">🌱 Switch to renewable energy sources like solar panels</div>
                    <div class="energy-tip">⚡ Unplug electronics when not in use to prevent phantom loads</div>
                </div>

                <div class="text-center position-relative">
                    <div class="welcome-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h1 class="display-4 fw-bold text-gradient mb-3">Welcome to Climate Yanga! 🌍</h1>
                    <p class="lead text-muted mb-4">
                        Your smart companion for sustainable living and carbon footprint reduction
                    </p>
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card bg-glass border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-6 text-center text-md-start">
                                            <h5 class="text-primary mb-2">
                                                <i class="fas fa-lightbulb me-2"></i>Smart Energy Tips
                                            </h5>
                                            <p class="mb-0 text-muted">
                                                Get personalized recommendations to reduce your carbon footprint
                                            </p>
                                        </div>
                                        <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                                            <div class="d-flex justify-content-center justify-content-md-end">
                                                <div class="me-3">
                                                    <i class="fas fa-leaf text-success fs-2"></i>
                                                </div>
                                                <div>
                                                    <i class="fas fa-bolt text-warning fs-2"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sample Content -->
            <div class="animate-fade-in">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-calculator text-primary me-2"></i>
                                    Carbon Calculator
                                </h5>
                                <p class="card-text">Track your daily emissions and discover ways to reduce your carbon footprint.</p>
                                <a href="{{ route('register') }}" class="btn btn-primary">Register to Get Started</a>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-chart-line text-success me-2"></i>
                                    Progress Tracking
                                </h5>
                                <p class="card-text">Monitor your sustainability journey with detailed analytics and insights.</p>
                               <a href="{{ route('register') }}" class="btn btn-primary">Register to View Progress</a>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-award text-warning me-2"></i>
                                    Earn Credits
                                </h5>
                                <p class="card-text">Participate in clean cooking interventions and earn valuable carbon credits.</p>
                                <a href="{{ route('register') }}" class="btn btn-primary">Register to Get Learn More</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ================================================
        FOOTER
    ================================================= -->
    <footer class="footer py-3 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h6 class="d-flex align-items-center">
                        <i class="fas fa-leaf me-2 animate-float"></i>
                        UNZA Carbon Calculator
                    </h6>
                    <p class="text-muted mb-0 small">
                        Empowering sustainable cooking practices at UNZA.
                    </p>
                </div>

                                    <div class="col-md-4 mb-3 mb-md-0">
                        <h6>Our Social Media</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-1">
                                <a href="https://facebook.com/ClimateYanga" target="_blank" class="hover-lift d-inline-block small">
                                    <i class="fab fa-facebook me-2 text-primary"></i>Facebook
                                </a>
                            </li>
                            <li class="mb-1">
                                <a href="https://wa.me/260971234567" target="_blank" class="hover-lift d-inline-block small">
                                    <i class="fab fa-whatsapp me-2 text-success"></i>WhatsApp
                                </a>
                            </li>
                            <li class="mb-1">
                                <a href="https://twitter.com/ClimateYanga" target="_blank" class="hover-lift d-inline-block small">
                                    <i class="fab fa-x-twitter me-2 text-dark"></i>X (Twitter)
                                </a>
                            </li>
                            <li class="mb-1">
                                <a href="https://youtube.com/@ClimateYanga" target="_blank" class="hover-lift d-inline-block small">
                                    <i class="fab fa-youtube me-2 text-danger"></i>YouTube
                                </a>



                        </ul>
                    </div>


                <div class="col-md-4">
                    <h6>Contact</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-1 d-flex align-items-center small">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            <a href="mailto:info@climateyanga.com" class="hover-lift">info@climateyanga.com</a>
                        </li>
                        <li class="mb-1 d-flex align-items-center small">
                            <i class="fas fa-phone me-2 text-primary"></i>
                            <span>+260 XXX XXX XXX</span>
                        </li>
                        <li class="d-flex align-items-center small">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            <span>University of Zambia</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="my-3 border-light opacity-25">

            <div class="text-center">
                <p class="mb-0 text-muted small">
                    &copy; 2025 UNZA Carbon Calculator. Built with 🌱 for a sustainable future.
                </p>
            </div>
        </div>
    </footer>

    <!-- ================================================
        JAVASCRIPT
    ================================================= -->
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Global Scripts -->
    <script>
        // Initialize tooltips and popovers
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
            $('[data-bs-toggle="popover"]').popover();

            // Auto-dismiss alerts after 5 seconds
            setTimeout(() => {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Add loading state to buttons on click
            $('button[type="submit"], .btn-primary').click(function() {
                $(this).addClass('loading');
            });

            // Smooth scrolling for anchor links
            $('a[href^="#"]').click(function(e) {
                e.preventDefault();
                const target = $(this.getAttribute('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 500);
                }
            });

            // Intersection Observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                    }
                });
            }, observerOptions);

            // Observe all cards and main content
            $('.card, .main-content').each(function() {
                observer.observe(this);
            });
        });

        // Format emissions values
        function formatEmissions(value) {
            return parseFloat(value).toFixed(3) + ' tCO₂e';
        }

        // Add ripple effect to buttons
        function createRipple(event) {
            const button = event.currentTarget;
            const circle = document.createElement('span');
            const diameter = Math.max(button.clientWidth, button.clientHeight);
            const radius = diameter / 2;

            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${event.clientX - button.offsetLeft - radius}px`;
            circle.style.top = `${event.clientY - button.offsetTop - radius}px`;
            circle.classList.add('ripple');

            const ripple = button.getElementsByClassName('ripple')[0];
            if (ripple) {
                ripple.remove();
            }

            button.appendChild(circle);
        }

        // Add ripple effect to all buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', createRipple);
        });
    </script>
</body>
</html>
