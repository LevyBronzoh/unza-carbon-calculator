@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #22c55e;
        --primary-dark: #16a34a;
        --secondary: #06b6d4;
        --accent: #f59e0b;
        --light: #f8fafc;
        --light-gray: #f1f5f9;
        --medium-gray: #64748b;
        --dark: #0f172a;
        --gradient-primary: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        --gradient-secondary: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%);
        --gradient-hero: linear-gradient(135deg, #f0f9ff 0%, #ecfdf5 100%);
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .calculator-container {
        background: var(--gradient-hero);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow-md);
    }

    .card {
        border-radius: 16px;
        border: none;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 1.5rem;
        position: relative;
        background: white;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
        z-index: 1;
    }

    .card-header {
        background: var(--gradient-primary);
        color: white;
        font-weight: 600;
        border-bottom: none;
        padding: 1.25rem 1.5rem;
        position: relative;
    }

    .btn-primary {
        background: var(--gradient-primary);
        border: none;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.75rem 1.5rem;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        filter: brightness(1.1);
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        filter: brightness(1.1);
    }

    .form-control, .form-select {
        border-radius: 12px;
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.15);
        outline: none;
    }

    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc2626;
        box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.15);
    }

    .form-label {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .tooltip-icon {
        color: var(--medium-gray);
        cursor: help;
        margin-left: 0.5rem;
        transition: color 0.3s ease;
    }

    .tooltip-icon:hover {
        color: var(--primary);
    }

    .text-danger {
        color: #dc2626;
        font-size: 0.85rem;
        margin-top: 0.25rem;
        display: block;
    }

    .animate-fade-in {
        animation: fadeInUp 0.8s ease-out;
    }

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

    /* Responsive improvements */
    @media (max-width: 768px) {
        .calculator-container {
            padding: 1rem;
        }
    }

    /* Add this to your existing CSS */
.btn {
    position: relative;
    overflow: hidden;
}

.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    pointer-events: none;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>

<div class="container py-4 calculator-container animate-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-{{ $type === 'baseline' ? 'primary' : 'success' }} text-white">
                    <i class="fas {{ $type === 'baseline' ? 'fa-fire' : 'fa-leaf' }} me-2"></i>
                    {{ $type === 'baseline' ? 'Baseline Cooking Data' : 'Clean Cooking Project Data' }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('calculator.store') }}">
                        @csrf
                        <input type="hidden" name="calculation_type" value="{{ $type }}">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="stove_type" class="form-label">Stove Type</label>
                                <select class="form-select" id="stove_type" name="stove_type" required>
                                    <option value="">Select stove type</option>
                                    @foreach($stoveTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('stove_type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('stove_type'))
                                    <span class="text-danger">{{ $errors->first('stove_type') }}</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="fuel_type" class="form-label">Fuel Type</label>
                                <select class="form-select" id="fuel_type" name="fuel_type" required>
                                    <option value="">Select fuel type</option>
                                    @foreach($fuelTypes as $key => $label)
                                        <option value="{{ $key }}" {{ old('fuel_type') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('fuel_type'))
                                    <span class="text-danger">{{ $errors->first('fuel_type') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="daily_fuel_use" class="form-label">Daily Fuel Use (kg/liters)</label>
                                <input type="number" step="0.01" class="form-control" id="daily_fuel_use"
                                       name="daily_fuel_use" value="{{ old('daily_fuel_use') }}" required>
                                @if($errors->has('daily_fuel_use'))
                                    <span class="text-danger">{{ $errors->first('daily_fuel_use') }}</span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="daily_cooking_hours" class="form-label">Daily Cooking Hours</label>
                                <input type="number" step="0.1" class="form-control" id="daily_cooking_hours"
                                       name="daily_cooking_hours" value="{{ old('daily_cooking_hours') }}" required>
                                @if($errors->has('daily_cooking_hours'))
                                    <span class="text-danger">{{ $errors->first('daily_cooking_hours') }}</span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="household_size" class="form-label">Household Size</label>
                                <input type="number" class="form-control" id="household_size"
                                       name="household_size" value="{{ old('household_size', 1) }}" required>
                                @if($errors->has('household_size'))
                                    <span class="text-danger">{{ $errors->first('household_size') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="stove_efficiency" class="form-label">
                                    Stove Efficiency (%)
                                    <i class="fas fa-info-circle tooltip-icon" data-bs-toggle="tooltip"
                                       title="Leave blank to use default for selected stove type"></i>
                                </label>
                                <input type="number" step="0.1" class="form-control" id="stove_efficiency"
                                       name="stove_efficiency" value="{{ old('stove_efficiency') }}">
                                @if($errors->has('stove_efficiency'))
                                    <span class="text-danger">{{ $errors->first('stove_efficiency') }}</span>
                                @endif
                            </div>
                            @if($type === 'project')
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date of Clean Cooking</label>
                                <input type="date" class="form-control" id="start_date"
                                       name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                @if($errors->has('start_date'))
                                    <span class="text-danger">{{ $errors->first('start_date') }}</span>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('calculator.index') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-{{ $type === 'baseline' ? 'primary' : 'success' }}">
                                <i class="fas fa-save me-2"></i>
                                {{ $type === 'baseline' ? 'Save Baseline Data' : 'Save Project Data' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Add ripple effect to buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');

                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Form submission loading state
        const form = document.querySelector('form');
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
            }
        });
    });
</script>
@endsection

@endsection
