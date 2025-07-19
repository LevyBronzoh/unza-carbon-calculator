<x-guest-layout>
    <!-- Custom Styles -->
    <style>
        :root {
            /* Climate-friendly color palette */
            --primary: #22c55e;
            --primary-dark: #16a34a;
            --secondary: #06b6d4;
            --accent: #f59e0b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --light: #f8fafc;
            --light-gray: #f1f5f9;
            --medium-gray: #64748b;
            --dark: #0f172a;
            --text-muted: #64748b;
            --gradient-primary: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            --gradient-hero: linear-gradient(135deg, #f0f9ff 0%, #ecfdf5 100%);
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --transition-fast: 0.15s ease-in-out;
            --transition-normal: 0.3s ease-in-out;
            --transition-slow: 0.5s ease-in-out;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background: var(--gradient-hero);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        /* Registration Container */
        .register-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            position: relative;
            animation: fadeInUp 0.8s ease-out;
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--gradient-primary);
        }

        /* Header Section */
        .register-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .register-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        .register-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        .register-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .register-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        /* Form Styles */
        .register-form {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all var(--transition-normal);
            background: white;
            color: var(--dark);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
            transform: translateY(-1px);
        }

        .form-input:hover {
            border-color: var(--primary);
        }

        /* Error Messages */
        .error-message {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            animation: slideInLeft 0.3s ease-out;
        }

        .error-message::before {
            content: '⚠️';
            margin-right: 0.5rem;
        }

        /* Success Messages */
        .success-message {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border: 1px solid var(--success);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            animation: slideInLeft 0.5s ease-out;
            display: flex;
            align-items: center;
        }

        .success-message::before {
            content: '✅';
            margin-right: 0.5rem;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
        }

        .register-button {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal);
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            width: 100%;
            text-align: center;
        }

        .register-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left var(--transition-slow);
        }

        .register-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .register-button:hover::before {
            left: 100%;
        }

        .register-button:active {
            transform: translateY(0);
        }

        /* Login Link */
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all var(--transition-fast);
        }

        .login-link a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Animations */
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
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
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

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .register-container {
                margin: 0;
                border-radius: 16px;
            }

            .register-header {
                padding: 1.5rem;
            }

            .register-form {
                padding: 1.5rem;
            }
        }
    </style>

    <div class="register-container">
        <!-- Header -->
        <div class="register-header">
            <div class="register-logo">
                <i class="fas fa-leaf"></i>
            </div>
            <h1 class="register-title">Create Your Account</h1>
            <p class="register-subtitle">Join UNZA Carbon Calculator today</p>
        </div>

        <!-- Form -->
        <div class="register-form">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label">{{ __('Full Name') }}</label>
                    <input id="name"
                           class="form-input"
                           type="text"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           autofocus
                           autocomplete="name"
                           placeholder="Enter your full name">
                    @if($errors->get('name'))
                        <div class="error-message">
                            {{ implode(' ', $errors->get('name')) }}
                        </div>
                    @endif
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email"
                           class="form-input"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autocomplete="email"
                           placeholder="Enter your email address">
                    @if($errors->get('email'))
                        <div class="error-message">
                            {{ implode(' ', $errors->get('email')) }}
                        </div>
                    @endif
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password"
                           class="form-input"
                           type="password"
                           name="password"
                           required
                           autocomplete="new-password"
                           placeholder="Create a password">
                    @if($errors->get('password'))
                        <div class="error-message">
                            {{ implode(' ', $errors->get('password')) }}
                        </div>
                    @endif
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation"
                           class="form-input"
                           type="password"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           placeholder="Confirm your password">
                    @if($errors->get('password_confirmation'))
                        <div class="error-message">
                            {{ implode(' ', $errors->get('password_confirmation')) }}
                        </div>
                    @endif
                </div>

                <!-- Register Button -->
                <div class="action-group">
                    <button type="submit" class="register-button">
                        <i class="fas fa-user-plus" style="margin-right: 0.5rem;"></i>
                        {{ __('Register') }}
                    </button>
                </div>
            </form>

            <!-- Login Link -->
            <div class="login-link">
                <span style="color: var(--medium-gray); font-size: 0.9rem;">Already have an account?</span>
                <a href="{{ route('login') }}">{{ __('Log in here') }}</a>
            </div>
        </div>
    </div>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</x-guest-layout>
