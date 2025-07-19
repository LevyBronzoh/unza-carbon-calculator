{{--
@php
/**
 * @var string $type
 * @var array $stoveTypes
 * @var array $fuelTypes
 * @var \Illuminate\Support\MessageBag $errors
 */
@endphp
--}}

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-{{ $type === 'baseline' ? 'primary' : 'success' }} text-white">
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

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="stove_efficiency" class="form-label">
                                    Stove Efficiency (%)
                                    <i class="fas fa-info-circle" data-bs-toggle="tooltip"
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
                            <a href="{{ route('calculator.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-{{ $type === 'baseline' ? 'primary' : 'success' }}">
                                {{ $type === 'baseline' ? 'Save Baseline Data' : 'Save Project Data' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Set default date
    const startDateInput = document.getElementById('start_date');
    if (startDateInput) {
        startDateInput.valueAsDate = new Date();
    }
</script>
@endsection
