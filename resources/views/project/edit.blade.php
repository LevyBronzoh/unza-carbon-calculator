@extends('layouts.app')

@section('title', 'Edit Project Data')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Your Clean Cooking Project
                    </h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('project.update', $project->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- New Stove Type -->
                        <div class="mb-3">
                            <label for="new_stove_type" class="form-label">New Stove Type</label>
                            <select class="form-select @error('new_stove_type') is-invalid @enderror"
                                    id="new_stove_type"
                                    name="new_stove_type"
                                    required>
                                <option value="">Select Stove Type</option>
                                @foreach($stoveTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ old('new_stove_type', $project->new_stove_type) == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('new_stove_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Fuel Type -->
                        <div class="mb-3">
                            <label for="new_fuel_type" class="form-label">Fuel Type</label>
                            <select class="form-select @error('new_fuel_type') is-invalid @enderror"
                                    id="new_fuel_type"
                                    name="new_fuel_type"
                                    required>
                                <option value="">Select Fuel Type</option>
                                @foreach($fuelTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ old('new_fuel_type', $project->new_fuel_type) == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('new_fuel_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Daily Fuel Use -->
                        <div class="mb-3">
                            <label for="fuel_use_project" class="form-label">Estimated Daily Fuel Use (kg or liters)</label>
                            <input type="number"
                                   step="0.01"
                                   class="form-control @error('fuel_use_project') is-invalid @enderror"
                                   id="fuel_use_project"
                                   name="fuel_use_project"
                                   value="{{ old('fuel_use_project', $project->fuel_use_project) }}"
                                   required>
                            @error('fuel_use_project')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stove Efficiency -->
                        <div class="mb-3">
                            <label for="new_efficiency" class="form-label">
                                Stove Efficiency (decimal, e.g., 0.25 for 25%)
                                <small class="text-muted">Leave blank to use default</small>
                            </label>
                            <input type="number"
                                   step="0.01"
                                   min="0.01"
                                   max="1"
                                   class="form-control @error('new_efficiency') is-invalid @enderror"
                                   id="new_efficiency"
                                   name="new_efficiency"
                                   value="{{ old('new_efficiency', $project->new_efficiency) }}">
                            @error('new_efficiency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Default efficiency for selected stove:
                                <span id="default-efficiency-display">
                                    {{ number_format($this->getDefaultEfficiency($project->new_stove_type) * 100, 1) }}%
                                </span>
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date of Cleaner Cooking</label>
                            <input type="date"
                                   class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date"
                                   name="start_date"
                                   value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}"
                                   required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Observed Reduction -->
                        <div class="mb-3">
                            <label for="observed_reduction" class="form-label">Observed % Reduction (if known)</label>
                            <input type="number"
                                   step="0.1"
                                   class="form-control @error('observed_reduction') is-invalid @enderror"
                                   id="observed_reduction"
                                   name="observed_reduction"
                                   value="{{ old('observed_reduction', $project->observed_reduction) }}">
                            @error('observed_reduction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Emissions Display -->
                        <div class="alert alert-info mb-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Current Project Emissions:</strong>
                                </div>
                                <div>
                                    <span class="badge bg-success">
                                        {{ number_format($project->emissions_after, 3) }} tCO₂e/month
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Update Project Data
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
    document.getElementById('new_stove_type').addEventListener('change', function() {
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
