@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-green-50 to-cyan-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-green-800 mb-2">Your Cooking Emissions Results</h1>
            <p class="text-lg text-gray-600">Track your progress toward cleaner cooking</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Baseline Emissions -->
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-blue-500">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Baseline Emissions</h3>
                </div>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($results['baseline_monthly'], 2) }} tCO₂e/month</p>
                <p class="text-sm text-gray-500 mt-2">Before switching to cleaner cooking</p>
            </div>

            <!-- Current Emissions -->
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-green-500">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Current Emissions</h3>
                </div>
                <p class="text-2xl font-bold text-green-600">{{ number_format($results['project_monthly'], 2) }} tCO₂e/month</p>
                <p class="text-sm text-gray-500 mt-2">With your new cooking setup</p>
            </div>

            <!-- Emissions Reduced -->
            <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-purple-500">
                <div class="flex items-center mb-4">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Emissions Reduced</h3>
                </div>
                <p class="text-2xl font-bold text-purple-600">{{ number_format($results['monthly_reduction'], 2) }} tCO₂e/month</p>
                <p class="text-sm text-gray-500 mt-2">That's {{ number_format($results['monthly_reduction'] * 1000, 0) }} kg CO₂e!</p>
            </div>
        </div>

        <!-- Detailed Results -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Carbon Credits -->
            <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-1">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Your Carbon Credits</h3>
                <div class="flex items-center justify-center mb-4">
                    <div class="relative w-40 h-40">
                        <svg class="w-full h-full" viewBox="0 0 36 36">
                            <path d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#e6e6e6"
                                stroke-width="3"
                                stroke-dasharray="100, 100" />
                            <path d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#10b981"
                                stroke-width="3"
                                stroke-dasharray="{{ min($results['total_credits'] * 10, 100) }}, 100" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-3xl font-bold text-green-600">{{ number_format($results['total_credits'], 2) }}</span>
                        </div>
                    </div>
                </div>
                <p class="text-center text-gray-600">tonnes CO₂e saved</p>
                <p class="text-center text-sm text-gray-500 mt-2">Equivalent to {{ number_format($results['total_credits'] * 1000, 0) }} kg</p>
            </div>

            <!-- Recent Updates -->
            <div class="bg-white rounded-xl shadow-md p-6 lg:col-span-2">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Weekly Updates</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuel Used</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emissions</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% Reduction</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($updates as $update)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $update->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $update->fuel_consumption }} {{ $project->new_fuel_type == 'electricity' ? 'kWh' : ($project->new_fuel_type == 'lpg' ? 'L' : 'kg') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($update->estimated_emissions, 3) }} tCO₂e</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @php
                                        $reduction = $results['baseline_monthly'] > 0 ? (1 - ($update->estimated_emissions * 4 / $results['baseline_monthly'])) * 100 : 0;
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ number_format($reduction, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="bg-white rounded-xl shadow-md p-6 text-center">
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Keep up the good work!</h3>
            <p class="text-gray-600 mb-4">Your switch to cleaner cooking is making a real difference.</p>
            <a href="{{ route('weekly.update') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Submit This Week's Update
            </a>
        </div>
    </div>
</div>
@endsection
