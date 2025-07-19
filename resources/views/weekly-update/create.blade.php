@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-green-50 to-cyan-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-6">
            <h2 class="text-2xl font-bold text-green-800 mb-4">Weekly Cooking Update</h2>
            <p class="text-gray-600 mb-6">Please update your cooking fuel usage for this week to accurately track your emissions.</p>

            <form method="POST" action="{{ route('weekly.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fuel Consumption -->
                    <div>
                        <label for="fuel_consumption" class="block text-sm font-medium text-gray-700 mb-1">
                            Fuel Used This Week ({{ $project->new_fuel_type == 'electricity' ? 'kWh' : ($project->new_fuel_type == 'lpg' ? 'liters' : 'kg') }})
                        </label>
                        <input type="number" step="0.01" name="fuel_consumption" id="fuel_consumption"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               required>
                        <p class="mt-1 text-sm text-gray-500">Total {{ $project->new_fuel_type }} used for cooking</p>
                    </div>

                    <!-- Cooking Hours -->
                    <div>
                        <label for="cooking_hours" class="block text-sm font-medium text-gray-700 mb-1">
                            Cooking Hours This Week
                        </label>
                        <input type="number" step="0.5" name="cooking_hours" id="cooking_hours"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               required>
                    </div>

                    <!-- Stove Usage Percentage -->
                    <div>
                        <label for="stove_usage_percentage" class="block text-sm font-medium text-gray-700 mb-1">
                            % of Cooking Done with {{ ucfirst($project->new_stove_type) }}
                        </label>
                        <input type="number" min="0" max="100" name="stove_usage_percentage" id="stove_usage_percentage"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               required>
                        <p class="mt-1 text-sm text-gray-500">Estimate what percentage of meals used the new stove</p>
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                            Additional Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                        <p class="mt-1 text-sm text-gray-500">Any special circumstances this week?</p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit"
                            class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        Submit Weekly Update
                    </button>
                </div>
            </form>
        </div>

        @if($lastUpdate)
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Last Week's Update</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Fuel Used</p>
                    <p class="font-medium">{{ $lastUpdate->fuel_consumption }} {{ $project->new_fuel_type == 'electricity' ? 'kWh' : ($project->new_fuel_type == 'lpg' ? 'L' : 'kg') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Cooking Hours</p>
                    <p class="font-medium">{{ $lastUpdate->cooking_hours }} hrs</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Stove Usage</p>
                    <p class="font-medium">{{ $lastUpdate->stove_usage_percentage }}%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Date</p>
                    <p class="font-medium">{{ $lastUpdate->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
