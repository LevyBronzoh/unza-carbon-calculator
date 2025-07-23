{{--
    Baseline Data Entry View - Cooking Setup Data Collection Form

    This Blade template captures user's current cooking setup data for carbon credit calculations.
    Collects: Stove Type, Fuel Type, Daily Cooking Time, Daily Fuel Use, Stove Efficiency, and Household Size.

    Developed by Levy Bronzoh, Climate Yanga
    UNZA Carbon Calculator - Baseline Data Collection System
--}}

@extends('layouts.master')

@section('title', 'Baseline Data Entry - UNZA Carbon Calculator')

@section('styles')
<style>
    :root {
        --climate-primary: #2d5a27;
        --climate-primary-dark: #1e3d1b;
        --climate-secondary: #f8f9fa;
        --climate-accent: rgba(45, 90, 39, 0.25);
    }

    .baseline-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 2.5rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background-color: var(--climate-primary);
        border-color: var(--climate-primary);
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: var(--climate-primary-dark);
        border-color: var(--climate-primary-dark);
        transform: translateY(-1px);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--climate-primary);
        box-shadow: 0 0 0 0.2rem var(--climate-accent);
    }

    .form-section {
        background: linear-gradient(135deg, var(--climate-secondary) 0%, #ffffff 100%);
        border: 1px solid #e9ecef;
        border-left: 5px solid var(--climate-primary);
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 50px;
        height: 50px;
        background: var(--climate-primary);
        opacity: 0.1;
        border-radius: 50%;
        transform: translate(25px, -25px);
    }

    .section-icon {
        color: var(--climate-primary);
        font-size: 1.8rem;
        margin-right: 0.75rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .help-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
        font-style: italic;
    }

    .progress-bar {
        background: linear-gradient(90deg, var(--climate-primary) 0%, var(--climate-primary-dark) 100%);
        border-radius: 4px;
    }

    .efficiency-guide {
        background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%);
        border: 1px solid #81c784;
        border-radius: 8px;
        padding: 1.5rem;
    }

    .efficiency-guide ul {
        columns: 2;
        gap: 1rem;
        margin-bottom: 0;
    }

    .form-floating-group {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .floating-label {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        transition: all 0.3s ease;
        pointer-events: none;
        color: #6c757d;
        background: white;
        padding: 0 5px;
    }

    .form-control:focus + .floating-label,
    .form-control:valid + .floating-label,
    .form-select:focus + .floating-label,
    .form-select:valid + .floating-label {
        top: 0;
        font-size: 0.75rem;
        color: var(--climate-primary);
    }

    .page-header {
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
    }

    .page-header::after {
        content: '';
        display: block;
        width: 60px;
        height: 3px;
        background: var(--climate-primary);
        margin: 1rem auto;
        border-radius: 2px;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 2px solid #e9ecef;
    }

    .info-footer {
        text-align: center;
        margin-top: 2rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid var(--climate-primary);
    }

    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
            gap: 1rem;
        }

        .efficiency-guide ul {
            columns: 1;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="baseline-container">
                {{-- Page Header --}}
                <div class="page-header">
                    <h1 class="text-primary mb-3">
                        <i class="fas fa-fire section-icon"></i>
                        Baseline Cooking Data Entry
                    </h1>
                    <p class="lead text-muted mb-4">
                        Tell us about your current cooking setup to calculate baseline emissions
                    </p>

                    {{-- Progress Indicator --}}
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: 33%"
                             aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted fw-bold">Step 1 of 3: Baseline Data Collection</small>
                </div>

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Please correct the following errors:
                        </h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Success Messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Main Form --}}
                <form method="POST" action="{{ route('baseline-data.store') }}" id="baselineForm" novalidate>
                    @csrf

                    {{-- Stove and Fuel Information --}}
                    <div class="form-section">
                        <h3 class="text-primary mb-4">
                            <i class="fas fa-utensils section-icon"></i>
                            Stove and Fuel Information
                        </h3>

                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <label for="stove_type" class="form-label fw-semibold">
                                    Current Stove Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('stove_type') is-invalid @enderror"
                                        id="stove_type"
                                        name="stove_type"
                                        required>
                                    <option value="" disabled selected>Choose your stove type</option>
                                    <option value="3-Stone Fire" {{ old('stove_type') == '3-Stone Fire' ? 'selected' : '' }}>
                                        3-Stone Fire
                                    </option>
                                    <option value="Charcoal Brazier" {{ old('stove_type') == 'Charcoal Brazier' ? 'selected' : '' }}>
                                        Charcoal Brazier
                                    </option>
                                    <option value="Kerosene Stove" {{ old('stove_type') == 'Kerosene Stove' ? 'selected' : '' }}>
                                        Kerosene Stove
                                    </option>
                                    <option value="LPG Stove" {{ old('stove_type') == 'LPG Stove' ? 'selected' : '' }}>
                                        LPG Stove
                                    </option>
                                    <option value="Electric Stove" {{ old('stove_type') == 'Electric Stove' ? 'selected' : '' }}>
                                        Electric Stove
                                    </option>
                                    <option value="Improved Biomass Stove" {{ old('stove_type') == 'Improved Biomass Stove' ? 'selected' : '' }}>
                                        Improved Biomass Stove
                                    </option>
                                </select>
                                @error('stove_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6 mb-4">
                                <label for="fuel_type" class="form-label fw-semibold">
                                    Fuel Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('fuel_type') is-invalid @enderror"
                                        id="fuel_type"
                                        name="fuel_type"
                                        required>
                                    <option value="" disabled selected>Select fuel type</option>
                                    <option value="Wood" {{ old('fuel_type') == 'Wood' ? 'selected' : '' }}>Wood</option>
                                    <option value="Charcoal" {{ old('fuel_type') == 'Charcoal' ? 'selected' : '' }}>Charcoal</option>
                                    <option value="LPG" {{ old('fuel_type') == 'LPG' ? 'selected' : '' }}>LPG</option>
                                    <option value="Electricity" {{ old('fuel_type') == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                                    <option value="Ethanol" {{ old('fuel_type') == 'Ethanol' ? 'selected' : '' }}>Ethanol</option>
                                    <option value="Kerosene" {{ old('fuel_type') == 'Kerosene' ? 'selected' : '' }}>Kerosene</option>
                                    <option value="Other" {{ old('fuel_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('fuel_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Usage Patterns --}}
                    <div class="form-section">
                        <h3 class="text-primary mb-4">
                            <i class="fas fa-clock section-icon"></i>
                            Daily Usage Patterns
                        </h3>

                        <div class="row">
                            <div class="col-lg-4 mb-4">
                                <label for="daily_hours" class="form-label fw-semibold">
                                    Daily Cooking Time (hours) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('daily_hours') is-invalid @enderror"
                                       id="daily_hours"
                                       name="daily_hours"
                                       value="{{ old('daily_hours') }}"
                                       step="0.1"
                                       min="0.1"
                                       max="24"
                                       placeholder="e.g., 2.5"
                                       required>
                                <div class="help-text">
                                    <i class="fas fa-info-circle"></i> Average hours spent cooking daily
                                </div>
                                @error('daily_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-4 mb-4">
                                <label for="daily_fuel_use" class="form-label fw-semibold">
                                    Daily Fuel Use <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('daily_fuel_use') is-invalid @enderror"
                                       id="daily_fuel_use"
                                       name="daily_fuel_use"
                                       value="{{ old('daily_fuel_use') }}"
                                       step="0.01"
                                       min="0.01"
                                       placeholder="Amount per day"
                                       required>
                                <div class="help-text" id="fuel-unit-text">
                                    <i class="fas fa-info-circle"></i> <span id="unit-display">Amount of fuel used daily</span>
                                </div>
                                @error('daily_fuel_use')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-4 mb-4">
                                <label for="household_size" class="form-label fw-semibold">
                                    Household Size <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('household_size') is-invalid @enderror"
                                       id="household_size"
                                       name="household_size"
                                       value="{{ old('household_size') }}"
                                       min="1"
                                       max="20"
                                       placeholder="e.g., 4"
                                       required>
                                <div class="help-text">
                                    <i class="fas fa-info-circle"></i> Number of people in household
                                </div>
                                @error('household_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Stove Efficiency --}}
                    <div class="form-section">
                        <h3 class="text-primary mb-4">
                            <i class="fas fa-chart-line section-icon"></i>
                            Stove Efficiency (Optional)
                        </h3>

                        <div class="row align-items-center">
                            <div class="col-lg-6 mb-4">
                                <label for="efficiency" class="form-label fw-semibold">
                                    Stove Efficiency (%)
                                </label>
                                <input type="number"
                                       class="form-control @error('efficiency') is-invalid @enderror"
                                       id="efficiency"
                                       name="efficiency"
                                       value="{{ old('efficiency') }}"
                                       min="1"
                                       max="100"
                                       placeholder="e.g., 25">
                                <div class="help-text">
                                    <i class="fas fa-lightbulb"></i> Leave blank if unknown - we'll use default values based on stove type
                                </div>
                                @error('efficiency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-lg-6">
                                <div class="efficiency-guide">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-info-circle"></i> Typical Efficiency Values:
                                    </h6>
                                    <ul class="list-unstyled small">
                                        <li><i class="fas fa-fire text-warning"></i> 3-Stone Fire: ~10%</li>
                                        <li><i class="fas fa-burn text-dark"></i> Charcoal Brazier: ~15%</li>
                                        <li><i class="fas fa-seedling text-success"></i> Improved Biomass: ~25%</li>
                                        <li><i class="fas fa-gas-pump text-info"></i> LPG Stove: ~55%</li>
                                        <li><i class="fas fa-bolt text-primary"></i> Electric Stove: ~75%</li>
                                        <li><i class="fas fa-oil-can text-secondary"></i> Kerosene Stove: ~45%</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="form-actions">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                        </a>

                        <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                            <i class="fas fa-calculator me-2"></i> Calculate Baseline Emissions
                        </button>
                    </div>
                </form>

                {{-- Information Footer --}}
                <div class="info-footer">
                    <p class="mb-2">
                        <i class="fas fa-shield-alt text-success me-2"></i>
                        <strong>Your data is secure</strong> and used only for carbon credit calculations
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-arrow-right text-primary me-2"></i>
                        <strong>Next Step:</strong> After submitting, you'll see your baseline emissions and can add project interventions
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const form = document.getElementById('baselineForm');
    const fuelTypeSelect = document.getElementById('fuel_type');
    const dailyFuelUseInput = document.getElementById('daily_fuel_use');
    const stoveTypeSelect = document.getElementById('stove_type');
    const efficiencyInput = document.getElementById('efficiency');
    const submitBtn = document.getElementById('submitBtn');
    const unitDisplay = document.getElementById('unit-display');

    // Fuel type change handler
    fuelTypeSelect.addEventListener('change', function() {
        const fuelType = this.value;
        let placeholder, unit;

        switch(fuelType) {
            case 'Wood':
            case 'Charcoal':
                placeholder = 'e.g., 3.0';
                unit = 'Amount in kilograms (kg)';
                break;
            case 'LPG':
            case 'Kerosene':
                placeholder = 'e.g., 0.5';
                unit = 'Amount in liters (L)';
                break;
            case 'Electricity':
                placeholder = 'e.g., 2.0';
                unit = 'Amount in kilowatt-hours (kWh)';
                break;
            case 'Ethanol':
                placeholder = 'e.g., 1.0';
                unit = 'Amount in liters (L)';
                break;
            default:
                placeholder = 'Amount per day';
                unit = 'Amount of fuel used daily';
        }

        dailyFuelUseInput.placeholder = placeholder;
        unitDisplay.textContent = unit;

        // Add visual feedback
        dailyFuelUseInput.style.transition = 'all 0.3s ease';
        dailyFuelUseInput.style.borderColor = '#2d5a27';
        setTimeout(() => {
            dailyFuelUseInput.style.borderColor = '';
        }, 1000);
    });

    // Stove type change handler - auto-suggest efficiency
    stoveTypeSelect.addEventListener('change', function() {
        const stoveType = this.value;
        const efficiencyMap = {
            '3-Stone Fire': 10,
            'Charcoal Brazier': 15,
            'Improved Biomass Stove': 25,
            'LPG Stove': 55,
            'Electric Stove': 75,
            'Kerosene Stove': 45
        };

        if (efficiencyMap[stoveType] && !efficiencyInput.value) {
            efficiencyInput.placeholder = `Suggested: ${efficiencyMap[stoveType]}%`;

            // Optional: Auto-fill suggested value
            // efficiencyInput.value = efficiencyMap[stoveType];
        }
    });

    // Form validation and submission
    form.addEventListener('submit', function(e) {
        // Update submit button
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Calculating...';
        submitBtn.disabled = true;

        // Basic client-side validation
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');

                // Remove invalid class after user interaction
                field.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                }, { once: true });
            }
        });

        if (!isValid) {
            e.preventDefault();
            submitBtn.innerHTML = '<i class="fas fa-calculator me-2"></i> Calculate Baseline Emissions';
            submitBtn.disabled = false;

            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });

    // Input enhancement - add focus effects
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
            this.parentElement.style.transition = 'transform 0.3s ease';
        });

        input.addEventListener('blur', function() {
            this.parentElement.style.transform = '';
        });
    });

    // Auto-save functionality (optional)
    const autoSaveFields = ['stove_type', 'fuel_type', 'daily_hours', 'daily_fuel_use', 'household_size'];
    autoSaveFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function() {
                // Save to sessionStorage for form persistence
                sessionStorage.setItem(`baseline_${fieldId}`, this.value);
            });

            // Restore from sessionStorage
            const savedValue = sessionStorage.getItem(`baseline_${fieldId}`);
            if (savedValue && !field.value) {
                field.value = savedValue;
            }
        }
    });

    // Clear auto-saved data on successful submission
    form.addEventListener('submit', function() {
        if (this.checkValidity()) {
            autoSaveFields.forEach(fieldId => {
                sessionStorage.removeItem(`baseline_${fieldId}`);
            });
        }
    });
});
</script>
@endsection
