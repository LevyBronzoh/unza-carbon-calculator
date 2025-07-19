{{--
    Baseline Data Entry View - Cooking Setup Data Collection Form

    This Blade template captures user's current cooking setup data as specified
    in the technical requirements: Stove Type, Fuel Type, Daily Cooking Time,
    Daily Fuel Use, Stove Efficiency, and Household Size.

    Polymorphism: This template extends the master layout (@extends)
    - Inherits all layout structure, navigation, and styling from master layout
    - Overrides specific sections (title, content, styles) while maintaining base structure
    - Uses Laravel's Blade template engine polymorphic features

    Developed by Levy Bronzoh, Climate Yanga
    UNZA Carbon Calculator - Baseline Data Collection System
--}}

@extends('layouts.master')

{{-- Set page title - appears in browser tab and page header --}}
@section('title', 'Baseline Data Entry - UNZA Carbon Calculator')

{{-- Additional CSS for baseline data entry page styling --}}
@section('styles')
<style>
    /* Baseline form container styling */
    .baseline-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Climate Yanga branding colors */
    .btn-primary {
        background-color: #2d5a27;
        border-color: #2d5a27;
    }

    .btn-primary:hover {
        background-color: #1e3d1b;
        border-color: #1e3d1b;
    }

    /* Form input styling with focus effects */
    .form-control:focus, .form-select:focus {
        border-color: #2d5a27;
        box-shadow: 0 0 0 0.2rem rgba(45, 90, 39, 0.25);
    }

    /* Section dividers */
    .form-section {
        border-left: 4px solid #2d5a27;
        padding-left: 1.5rem;
        margin-bottom: 2rem;
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
    }

    /* Help text styling */
    .help-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    /* Progress indicator */
    .progress-bar {
        background-color: #2d5a27;
    }

    /* Icon styling */
    .section-icon {
        color: #2d5a27;
        font-size: 1.5rem;
        margin-right: 0.5rem;
    }
</style>
@endsection

{{-- Main content section --}}
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="baseline-container">
                {{-- Page header with progress indicator --}}
                <div class="text-center mb-4">
                    <h2 class="text-primary">
                        <i class="fas fa-fire section-icon"></i>
                        Baseline Cooking Data Entry
                    </h2>
                    <p class="text-muted">Tell us about your current cooking setup to calculate baseline emissions</p>

                    {{-- Progress bar to show form completion --}}
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: 33%"></div>
                    </div>
                    <small class="text-muted">Step 1 of 3: Baseline Data Collection</small>
                </div>

                {{-- Display validation errors if any exist --}}
                {{-- Laravel's $errors variable contains validation error messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> Please correct the following errors:</h6>
                        <ul class="mb-0">
                            {{-- Loop through each error and display it --}}
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Display success messages from session --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                {{-- Baseline data entry form - submits to BaselineDataController@store --}}
                <form method="POST" action="{{ route('baseline-data.store') }}" id="baselineForm">
                    {{-- CSRF token for security - prevents cross-site request forgery --}}
                    @csrf

                    {{-- Stove and Fuel Information Section --}}
                    <div class="form-section">
                        <h4 class="text-primary mb-3">
                            <i class="fas fa-utensils section-icon"></i>
                            Stove and Fuel Information
                        </h4>

                        <div class="row">
                            {{-- Stove Type selection dropdown --}}
                            <div class="col-md-6 mb-3">
                                <label for="stove_type" class="form-label">Current Stove Type <span class="text-danger">*</span></label>
                                {{--
                                    Select dropdown with stove types as specified in technical requirements
                                    - Options: 3-Stone Fire, Charcoal Brazier, Kerosene Stove, LPG Stove, Electric Stove, Improved Biomass Stove
                                    - old('stove_type') maintains selection on validation error
                                --}}
                                <select class="form-select @error('stove_type') is-invalid @enderror"
                                        id="stove_type"
                                        name="stove_type"
                                        required>
                                    <option value="">Select your stove type</option>
                                    <option value="3-Stone Fire" {{ old('stove_type') == '3-Stone Fire' ? 'selected' : '' }}>3-Stone Fire</option>
                                    <option value="Charcoal Brazier" {{ old('stove_type') == 'Charcoal Brazier' ? 'selected' : '' }}>Charcoal Brazier</option>
                                    <option value="Kerosene Stove" {{ old('stove_type') == 'Kerosene Stove' ? 'selected' : '' }}>Kerosene Stove</option>
                                    <option value="LPG Stove" {{ old('stove_type') == 'LPG Stove' ? 'selected' : '' }}>LPG Stove</option>
                                    <option value="Electric Stove" {{ old('stove_type') == 'Electric Stove' ? 'selected' : '' }}>Electric Stove</option>
                                    <option value="Improved Biomass Stove" {{ old('stove_type') == 'Improved Biomass Stove' ? 'selected' : '' }}>Improved Biomass Stove</option>
                                </select>

                                @error('stove_type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Fuel Type selection dropdown --}}
                            <div class="col-md-6 mb-3">
                                <label for="fuel_type" class="form-label">Fuel Type <span class="text-danger">*</span></label>
                                {{--
                                    Select dropdown with fuel types as specified in technical requirements
                                    - Options: Wood, Charcoal, LPG, Electricity, Ethanol, Other
                                --}}
                                <select class="form-select @error('fuel_type') is-invalid @enderror"
                                        id="fuel_type"
                                        name="fuel_type"
                                        required>
                                    <option value="">Select fuel type</option>
                                    <option value="Wood" {{ old('fuel_type') == 'Wood' ? 'selected' : '' }}>Wood</option>
                                    <option value="Charcoal" {{ old('fuel_type') == 'Charcoal' ? 'selected' : '' }}>Charcoal</option>
                                    <option value="LPG" {{ old('fuel_type') == 'LPG' ? 'selected' : '' }}>LPG</option>
                                    <option value="Electricity" {{ old('fuel_type') == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                                    <option value="Ethanol" {{ old('fuel_type') == 'Ethanol' ? 'selected' : '' }}>Ethanol</option>
                                    <option value="Other" {{ old('fuel_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>

                                @error('fuel_type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
<select name="stove_type" class="form-control" required>
    <option value="">Select Stove Type</option>
    @foreach($stoveTypes as $type)
        <option value="{{ $type }}" {{ old('stove_type', $data->stove_type ?? '') == $type ? 'selected' : '' }}>
            {{ ucwords(str_replace('-', ' ', $type)) }}
        </option>
    @endforeach
</select>

<form method="POST" action="{{ route('baseline-data.store') }}" class="space-y-4">
    @csrf

    <!-- Stove Type Dropdown -->
    <div class="form-group">
        <label for="stove_type" class="block font-medium text-gray-700">Stove Type</label>
        <select name="stove_type" id="stove_type" class="form-select mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="">Select Stove Type</option>
            <option value="3-stone-fire" {{ old('stove_type') == '3-stone-fire' ? 'selected' : '' }}>3-Stone Fire</option>
            <option value="charcoal-brazier" {{ old('stove_type') == 'charcoal-brazier' ? 'selected' : '' }}>Charcoal Brazier</option>
            <option value="kerosene-stove" {{ old('stove_type') == 'kerosene-stove' ? 'selected' : '' }}>Kerosene Stove</option>
            <option value="lpg-stove" {{ old('stove_type') == 'lpg-stove' ? 'selected' : '' }}>LPG Stove</option>
            <option value="electric-stove" {{ old('stove_type') == 'electric-stove' ? 'selected' : '' }}>Electric Stove</option>
            <option value="improved-biomass-stove" {{ old('stove_type') == 'improved-biomass-stove' ? 'selected' : '' }}>Improved Biomass Stove</option>
        </select>
        @error('stove_type')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Fuel Type Dropdown -->
    <div class="form-group">
        <label for="fuel_type" class="block font-medium text-gray-700">Fuel Type</label>
        <select name="fuel_type" id="fuel_type" class="form-select mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="">Select Fuel Type</option>
            <option value="wood" {{ old('fuel_type') == 'wood' ? 'selected' : '' }}>Wood</option>
            <option value="charcoal" {{ old('fuel_type') == 'charcoal' ? 'selected' : '' }}>Charcoal</option>
            <option value="lpg" {{ old('fuel_type') == 'lpg' ? 'selected' : '' }}>LPG</option>
            <option value="electricity" {{ old('fuel_type') == 'electricity' ? 'selected' : '' }}>Electricity</option>
            <option value="kerosene" {{ old('fuel_type') == 'kerosene' ? 'selected' : '' }}>Kerosene</option>
            <option value="ethanol" {{ old('fuel_type') == 'ethanol' ? 'selected' : '' }}>Ethanol</option>
        </select>
        @error('fuel_type')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Daily Fuel Use -->
    <div class="form-group">
        <label for="daily_fuel_use" class="block font-medium text-gray-700">Daily Fuel Use (kg/liters)</label>
        <input type="number" step="0.01" min="0.1" name="daily_fuel_use" id="daily_fuel_use"
               class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm"
               value="{{ old('daily_fuel_use') }}" required>
        @error('daily_fuel_use')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Daily Cooking Hours -->
    <div class="form-group">
        <label for="daily_hours" class="block font-medium text-gray-700">Daily Cooking Hours</label>
        <input type="number" step="0.1" min="0.1" max="24" name="daily_hours" id="daily_hours"
               class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm"
               value="{{ old('daily_hours') }}" required>
        @error('daily_hours')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Household Size -->
    <div class="form-group">
        <label for="household_size" class="block font-medium text-gray-700">Household Size</label>
        <input type="number" min="1" name="household_size" id="household_size"
               class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm"
               value="{{ old('household_size') }}" required>
        @error('household_size')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Stove Efficiency (Optional) -->
    <div class="form-group">
        <label for="efficiency" class="block font-medium text-gray-700">Stove Efficiency (optional)</label>
        <small class="text-gray-500">Enter as decimal (e.g., 0.25 for 25%)</small>
        <input type="number" step="0.01" min="0.01" max="1" name="efficiency" id="efficiency"
               class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm"
               value="{{ old('efficiency') }}">
        @error('efficiency')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <!-- Submit Button -->
    <div class="form-group">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Calculate Emissions
        </button>
    </div>
</form>
                    {{-- Usage Patterns Section --}}
                    <div class="form-section">
                        <h4 class="text-primary mb-3">
                            <i class="fas fa-clock section-icon"></i>
                            Daily Usage Patterns
                        </h4>

                        <div class="row">
                            {{-- Daily Cooking Time input --}}
                            <div class="col-md-4 mb-3">
                                <label for="daily_hours" class="form-label">Daily Cooking Time (hours) <span class="text-danger">*</span></label>
                                {{--
                                    Number input with validation constraints
                                    - step="0.1" allows decimal values (e.g., 2.5 hours)
                                    - min="0.1" ensures positive values
                                    - max="24" prevents unrealistic values
                                --}}
                                <input type="number"
                                       class="form-control @error('daily_hours') is-invalid @enderror"
                                       id="daily_hours"
                                       name="daily_hours"
                                       value="{{ old('daily_hours') }}"
                                       step="0.1"
                                       min="0.1"
                                       max="24"
                                       required
                                       placeholder="e.g., 2.5">
                                <div class="help-text">Average hours spent cooking per day</div>

                                @error('daily_hours')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Daily Fuel Use input --}}
                            <div class="col-md-4 mb-3">
                                <label for="daily_fuel_use" class="form-label">Daily Fuel Use (kg or liters) <span class="text-danger">*</span></label>
                                {{--
                                    Number input for fuel consumption
                                    - step="0.1" allows decimal values
                                    - min="0.1" ensures positive values
                                    - Placeholder changes based on fuel type selection
                                --}}
                                <input type="number"
                                       class="form-control @error('daily_fuel_use') is-invalid @enderror"
                                       id="daily_fuel_use"
                                       name="daily_fuel_use"
                                       value="{{ old('daily_fuel_use') }}"
                                       step="0.1"
                                       min="0.1"
                                       required
                                       placeholder="e.g., 3.0">
                                <div class="help-text">Amount of fuel used per day</div>

                                @error('daily_fuel_use')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Household Size input --}}
                            <div class="col-md-4 mb-3">
                                <label for="household_size" class="form-label">Household Size <span class="text-danger">*</span></label>
                                {{--
                                    Number input for household size
                                    - min="1" ensures at least 1 person
                                    - max="20" prevents unrealistic values
                                --}}
                                <input type="number"
                                       class="form-control @error('household_size') is-invalid @enderror"
                                       id="household_size"
                                       name="household_size"
                                       value="{{ old('household_size') }}"
                                       min="1"
                                       max="20"
                                       required
                                       placeholder="e.g., 4">
                                <div class="help-text">Number of people in household</div>

                                @error('household_size')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Stove Efficiency Section (Optional) --}}
                    <div class="form-section">
                        <h4 class="text-primary mb-3">
                            <i class="fas fa-chart-line section-icon"></i>
                            Stove Efficiency (Optional)
                        </h4>

                        <div class="row">
                            {{-- Stove Efficiency input --}}
                            <div class="col-md-6 mb-3">
                                <label for="efficiency" class="form-label">Stove Efficiency (%)</label>
                                {{--
                                    Number input for stove efficiency
                                    - Optional field as per technical requirements
                                    - min="1" max="100" for percentage values
                                    - Default values will be used if not provided
                                --}}
                                <input type="number"
                                       class="form-control @error('efficiency') is-invalid @enderror"
                                       id="efficiency"
                                       name="efficiency"
                                       value="{{ old('efficiency') }}"
                                       min="1"
                                       max="100"
                                       placeholder="e.g., 10">
                                <div class="help-text">Leave blank if unknown - we'll use default values</div>

                                @error('efficiency')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Efficiency help information --}}
                            <div class="col-md-6 mb-3">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Efficiency Guidelines:</h6>
                                    <ul class="mb-0 small">
                                        <li>3-Stone Fire: ~10%</li>
                                        <li>Charcoal Brazier: ~15%</li>
                                        <li>Improved Biomass: ~25%</li>
                                        <li>LPG Stove: ~55%</li>
                                        <li>Electric Stove: ~75%</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="d-flex justify-content-between">
                        {{-- Back button --}}
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>

                        {{-- Submit button --}}
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-calculator"></i> Calculate Baseline Emissions
                        </button>
                    </div>
                </form>

                {{-- Information footer --}}
                <div class="text-center mt-4 pt-3 border-top">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt"></i> Your data is secure and used only for carbon credit calculations<br>
                        <strong>Next:</strong> After submitting, you'll see your baseline emissions and can add project interventions
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Additional JavaScript for baseline data entry --}}
@section('scripts')
<script>
    // Dynamic form enhancement and validation
    document.addEventListener('DOMContentLoaded', function() {
        // Get form elements
        const fuelTypeSelect = document.getElementById('fuel_type');
        const dailyFuelUseInput = document.getElementById('daily_fuel_use');
        const stoveTypeSelect = document.getElementById('stove_type');
        const efficiencyInput = document.getElementById('efficiency');
        const form = document.getElementById('baselineForm');

        // Update fuel use placeholder based on fuel type selection
        fuelTypeSelect.addEventListener('change', function() {
            const fuelType = this.value;
            switch(fuelType) {
                case 'Wood':
                case 'Charcoal':
                    dailyFuelUseInput.placeholder = 'e.g., 3.0 (kg)';
                    break;
                case 'LPG':
                case 'Kerosene':
                    dailyFuelUseInput.placeholder = 'e.g., 0.5 (liters)';
                    break;
                case 'Electricity':
                    dailyFuelUseInput.placeholder = 'e.g., 2.0 (kWh)';
                    break;
                default:
                    dailyFuelUseInput.placeholder = 'Amount per day';
            }
        });

        // Auto-suggest efficiency based on stove type
        stoveTypeSelect.addEventListener('change', function() {
            const stoveType = this.value;
            let suggestedEfficiency = '';

            switch(stoveType) {
                case '3-Stone Fire':
                    suggestedEfficiency = '10';
                    break;
                case 'Charcoal Brazier':
                    suggestedEfficiency = '15';
                    break;
                case 'Improved Biomass Stove':
                    suggestedEfficiency = '25';
                    break;
                case 'LPG Stove':
                    suggestedEfficiency = '55';
                    break;
                case 'Electric Stove':
                    suggestedEfficiency = '75';
                    break;
                case 'Kerosene Stove':
                    suggestedEfficiency = '45';
                    break;
            }

            // Only set if efficiency field is empty
            if (!efficiencyInput.value && suggestedEfficiency) {
                efficiencyInput.placeholder = `Suggested: ${suggestedEfficiency}%`;
            }
        });

        // Form submission handler
        form.addEventListener('submit', function(e) {
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Calculating...';
            submitBtn.disabled = true;
        });
    });


</script>
@endsection
