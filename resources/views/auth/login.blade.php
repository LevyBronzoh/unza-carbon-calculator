<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

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

        /* Login Container */
        .login-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            position: relative;
            animation: fadeInUp 0.8s ease-out;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--gradient-primary);
        }

        /* Header Section */
        .login-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        .login-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: float 3s ease-in-out infinite;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        /* Form Styles */
        .login-form {
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

        /* Remember Me Checkbox */
        .remember-group {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .remember-checkbox {
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid var(--primary);
            border-radius: 4px;
            margin-right: 0.75rem;
            cursor: pointer;
            position: relative;
            transition: all var(--transition-fast);
        }

        .remember-checkbox:checked {
            background: var(--primary);
            border-color: var(--primary);
        }

        .remember-checkbox:checked::before {
            content: '✓';
            color: white;
            font-weight: bold;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 0.9rem;
        }

        .remember-label {
            font-size: 0.9rem;
            color: var(--medium-gray);
            cursor: pointer;
            user-select: none;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
        }

        .forgot-password {
            color: var(--medium-gray);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all var(--transition-fast);
        }

        .forgot-password:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .login-button {
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
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left var(--transition-slow);
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:active {
            transform: translateY(0);
        }

        /* Register Link */
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .register-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all var(--transition-fast);
        }

        .register-link a:hover {
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

            .login-container {
                margin: 0;
                border-radius: 16px;
            }

            .login-header {
                padding: 1.5rem;
            }

            .login-form {
                padding: 1.5rem;
            }

            .action-group {
                flex-direction: column;
                gap: 1rem;
            }

            .forgot-password {
                order: 2;
            }

            .login-button {
                order: 1;
                width: 100%;
            }
        }
    </style>

    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-leaf"></i>
            </div>
            <h1 class="login-title">Welcome Back!</h1>
            <p class="login-subtitle">Sign in to your UNZA Carbon Calculator account</p>
        </div>

        <!-- Form -->
        <div class="login-form">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email"
                           class="form-input"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="Enter your email address" />
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
                           autocomplete="current-password"
                           placeholder="Enter your password" />
                    @if($errors->get('password'))
                        <div class="error-message">
                            {{ implode(' ', $errors->get('password')) }}
                        </div>
                    @endif
                </div>

                <!-- Remember Me -->
                <div class="remember-group">
                    <input id="remember_me"
                           type="checkbox"
                           class="remember-checkbox"
                           name="remember">
                    <label for="remember_me" class="remember-label">{{ __('Remember me') }}</label>
                </div>

                <!-- Action Buttons -->
                <div class="action-group">
                    @if (Route::has('password.request'))
                        <a class="forgot-password" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <button type="submit" class="login-button">
                        <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i>
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>

            <!-- Register Link -->
            @if (Route::has('register'))
                <div class="register-link">
                    <span style="color: var(--medium-gray); font-size: 0.9rem;">Don't have an account?</span>
                    <a href="{{ route('register') }}">{{ __('Create one here') }}</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</x-guest-layout>
