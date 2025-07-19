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

        /* Navigation Cards */
        .nav-card {
            height: 100%;
            border-radius: 16px;
            border: none;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-normal);
            background: white;
            overflow: hidden;
            position: relative;
        }

        .nav-card::before {
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

        .nav-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .nav-card:hover::before {
            transform: scaleX(1);
        }

        .nav-card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .nav-card-body {
            padding: 1.5rem;
        }

        .nav-card-title {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .nav-card-link {
            display: block;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 8px;
            color: var(--medium-gray);
            text-decoration: none;
            transition: all var(--transition-fast);
        }

        .nav-card-link:hover {
            background: rgba(34, 197, 94, 0.1);
            color: var(--primary);
            transform: translateX(5px);
        }

        .nav-card-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        /* Social Media Cards */
        .social-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-normal);
        }

        .social-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
            font-size: 1.25rem;
            transition: all var(--transition-fast);
        }

        .social-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--dark);
            margin-bottom: 1rem;
            transition: all var(--transition-fast);
        }

        .social-link:hover {
            color: var(--primary);
            transform: translateX(5px);
        }

        .social-link:hover .social-icon {
            transform: scale(1.1);
        }

        .facebook {
            background: linear-gradient(135deg, #3b5998 0%, #2d4373 100%);
        }

        .whatsapp {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        }

        .twitter {
            background: linear-gradient(135deg, #1DA1F2 0%, #0d8ecf 100%);
        }

        .youtube {
            background: linear-gradient(135deg, #FF0000 0%, #cc0000 100%);
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
                        <!-- Navigation items can be added here if needed -->
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- ================================================
        MAIN CONTENT AREA
    ================================================= -->
    <main class="flex-grow-1 py-4">
        <div class="container">
            <!-- Navigation Cards Section -->
            <div class="row mb-5">
                <div class="col-md-6 mb-4">
                    <div class="nav-card">
                        <div class="nav-card-header">
                            <h5 class="nav-card-title">
                                <i class="fas fa-compass me-2 text-primary"></i>
                                Navigation
                            </h5>
                        </div>
                        <div class="nav-card-body">
                            <a href="{{ route('home') }}" class="nav-card-link">
                                <i class="fas fa-home"></i> Home
                            </a>
                            <a href="{{ route('calculator.index') }}" class="nav-card-link">
                                <i class="fas fa-calculator"></i> Calculator
                            </a>
                            <a href="{{ route('analytics.index') }}" class="nav-card-link">
                                <i class="fas fa-chart-bar"></i> Analytics
                            </a>
                            <a href="{{ route('profile.edit') }}" class="nav-card-link">
                                <i class="fas fa-user"></i> Profile
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="nav-card">
                        <div class="nav-card-header">
                            <h5 class="nav-card-title">
                                <i class="fas fa-book me-2 text-primary"></i>
                                Resources
                            </h5>
                        </div>
                        <div class="nav-card-body">
                            <a href="{{ route('tips.index') }}" class="nav-card-link">
                                <i class="fas fa-lightbulb"></i> Energy Tips
                            </a>
                            <a href="#" class="nav-card-link">
                                <i class="fas fa-book"></i> Documentation
                            </a>
                            <a href="#" class="nav-card-link">
                                <i class="fas fa-question-circle"></i> FAQs
                            </a>
                            <a href="#" class="nav-card-link">
                                <i class="fas fa-envelope"></i> Contact
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="row animate-fade-in">
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>
                                Recent Activity
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($recentActivities->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-borderless table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Activity</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentActivities as $activity)
                                            <tr>
                                                <td class="text-nowrap">{{ $activity->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    @switch($activity->type)
                                                        @case('baseline')
                                                            <span class="badge bg-primary bg-opacity-10 text-primary">Baseline</span>
                                                            @break
                                                        @case('project')
                                                            <span class="badge bg-success bg-opacity-10 text-success">Project</span>
                                                            @break
                                                        @case('calculation')
                                                            <span class="badge bg-info bg-opacity-10 text-info">Calculation</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>{{ $activity->description }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 text-muted">No recent activities found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('baseline.create') }}" class="btn btn-outline-primary hover-lift text-start">
                                    <i class="fas fa-fire me-2"></i> Add Baseline Data
                                </a>
                                @if($baselineData)
                                    <a href="{{ route('project.create') }}" class="btn btn-outline-success hover-lift text-start">
                                        <i class="fas fa-leaf me-2"></i> Add Clean Cooking Data
                                    </a>
                                @else
                                    <button class="btn btn-outline-success text-start" disabled>
                                        <i class="fas fa-leaf me-2"></i> Add Clean Cooking Data
                                    </button>
                                @endif
                                <a href="{{ route('calculator.index') }}" class="btn btn-outline-info hover-lift text-start">
                                    <i class="fas fa-calculator me-2"></i> Use Carbon Calculator
                                </a>
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary hover-lift text-start">
                                    <i class="fas fa-user-cog me-2"></i> Update Profile
                                </a>
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
    <footer class="footer py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="d-flex align-items-center mb-3">
                        <div class="logo me-2">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">UNZA Carbon Calculator</h5>
                    </div>
                    <p class="small text-muted">
                        Empowering sustainable cooking practices at the University of Zambia.
                    </p>
                </div>

                <!-- Social Media Section -->

                <div class="col-md-4 mb-4 mb-md-0">
                    <h6 class="fw-bold mb-3">Connect With Us</h6>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fab fa-facebook-f me-2 text-primary"></i>
                            <a href="https://facebook.com/ClimateYanga" target="_blank" class="text-muted hover-lift">Facebook</a>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fab fa-whatsapp me-2 text-primary"></i>
                            <a href="https://wa.me/260971234567" target="_blank" class="text-muted hover-lift">WhatsApp</a>
                        </li>
                        <li class="mb-2 d-flex align-items-center">
                            <i class="fab fa-twitter me-2 text-primary"></i>
                            <a href="https://twitter.com/ClimateYanga" target="_blank" class="text-muted hover-lift">Twitter</a>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="fab fa-youtube me-2 text-primary"></i>
                            <a href="https://youtube.com/@ClimateYanga" target="_blank" class="text-muted hover-lift">YouTube</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h6 class="fw-bold mb-3">Contact</h6>

                        <ul class="list-unstyled text-muted small">
                            <li class="mb-2">
                                <a href="mailto:info@climateyanga.com" class="d-flex align-items-center text-muted hover-lift text-decoration-none">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    info@climateyanga.com
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="tel:+260XXXXXXXXX" class="d-flex align-items-center text-muted hover-lift text-decoration-none">
                                    <i class="fas fa-phone me-2 text-primary"></i>
                                    +260 XXX XXX XXX
                                </a>
                            </li>
                            <li>
                                <span class="d-flex align-items-center text-muted hover-lift">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    University of Zambia
                                </span>
                            </li>
                        </ul>
                </div>
            </div>
            <hr class="my-4 border-light">
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="small text-muted mb-0">
                        &copy; {{ date('Y') }} UNZA Carbon Calculator. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="small text-muted mb-0">
                        Built with <i class="fas fa-heart text-danger"></i> for a sustainable future
                    </p>
                </div>
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        // Initialize charts if needed
        if ($('#emissionsChart').length) {
            const ctx = document.getElementById('emissionsChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Baseline Emissions',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.3,
                        fill: true
                    }, {
                        label: 'Project Emissions',
                        data: [8, 15, 2, 4, 1, 2],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw.toFixed(3) + ' tCO₂e';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Emissions (tCO₂e)'
                            }
                        }
                    }
                }
            });
        }

        // Format numbers with commas
        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
        }
    </script>
</body>
</html>
