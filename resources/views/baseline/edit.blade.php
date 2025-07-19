@extends('layouts.app')

@section('title', 'Edit Baseline Cooking Data')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Your Baseline Cooking Data
                    </h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('baseline.update', $baseline->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Stove Type -->
                        <div class="mb-3">
                            <label for="stove_type" class="form-label">Current Stove Type</label>
                            <select class="form-select @error('stove_type') is-invalid @enderror"
                                    id="stove_type"
                                    name="stove_type"
                                    required>
                                <option value="">Select Stove Type</option>
                                @foreach($stoveTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ old('stove_type', $baseline->stove_type) == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('stove_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fuel Type -->
                        <div class="mb-3">
                            <label for="fuel_type" class="form-label">Fuel Type</label>
                            <select class="form-select @error('fuel_type') is-invalid @enderror"
                                    id="fuel_type"
                                    name="fuel_type"
                                    required>
                                <option value="">Select Fuel Type</option>
                                @foreach($fuelTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ old('fuel_type', $baseline->fuel_type) == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fuel_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Daily Cooking Time -->
                        <div class="mb-3">
                            <label for="daily_hours" class="form-label">Daily Cooking Time (hours)</label>
                            <input type="number"
                                   step="0.1"
                                   class="form-control @error('daily_hours') is-invalid @enderror"
                                   id="daily_hours"
                                   name="daily_hours"
                                   value="{{ old('daily_hours', $baseline->daily_hours) }}"
                                   required>
                            @error('daily_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Daily Fuel Use -->
                        <div class="mb-3">
                            <label for="daily_fuel_use" class="form-label">Daily Fuel Use (kg or liters)</label>
                            <input type="number"
                                   step="0.01"
                                   class="form-control @error('daily_fuel_use') is-invalid @enderror"
                                   id="daily_fuel_use"
                                   name="daily_fuel_use"
                                   value="{{ old('daily_fuel_use', $baseline->daily_fuel_use) }}"
                                   required>
                            @error('daily_fuel_use')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stove Efficiency -->
                        <div class="mb-3">
                            <label for="efficiency" class="form-label">
                                Stove Efficiency (decimal, e.g., 0.25 for 25%)
                                <small class="text-muted">Leave blank to use default</small>
                            </label>
                            <input type="number"
                                   step="0.01"
                                   min="0.01"
                                   max="1"
                                   class="form-control @error('efficiency') is-invalid @enderror"
                                   id="efficiency"
                                   name="efficiency"
                                   value="{{ old('efficiency', $baseline->efficiency) }}">
                            @error('efficiency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Default efficiency for selected stove:
                                <span id="default-efficiency-display">
                                    {{ number_format($baseline->efficiency * 100, 1) }}%
                                </span>
                            </div>
                        </div>

                        <!-- Household Size -->
                        <div class="mb-3">
                            <label for="household_size" class="form-label">Household Size</label>
                            <input type="number"
                                   class="form-control @error('household_size') is-invalid @enderror"
                                   id="household_size"
                                   name="household_size"
                                   value="{{ old('household_size', $baseline->household_size) }}"
                                   required>
                            @error('household_size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Emissions Display -->
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Current Emissions:</strong>
                                </div>
                                <div>
                                    <span class="badge bg-primary">
                                        {{ number_format($baseline->emission_total, 3) }} tCO₂e/month
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Baseline Data
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
    // Update default efficiency display when stove type changes
    document.getElementById('stove_type').addEventListener('change', function() {
        const efficiencies = {
            '3-Stone Fire': '10.0%',
            'Charcoal Brazier': '10.0%',
            'Kerosene Stove': '35.0%',
            'LPG Stove': '55.0%',
            'Electric Stove': '75.0%',
            'Improved Biomass Stove': '25.0%'
        };

        const selectedStove = this.value;
        const displayElement = document.getElementById('default-efficiency-display');

        if (selectedStove in efficiencies) {
            displayElement.textContent = efficiencies[selectedStove];
        } else {
            displayElement.textContent = '10.0%';
        }
    });
</script>
@endsection
@endsection
