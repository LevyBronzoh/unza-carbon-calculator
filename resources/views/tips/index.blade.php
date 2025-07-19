@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-green-50 to-cyan-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-green-800 mb-2">Clean Cooking Tips</h1>
            <p class="text-lg text-gray-600">Learn how to improve efficiency and reduce emissions</p>
        </div>

        <!-- Category Filter -->
        <div class="flex flex-wrap justify-center gap-2 mb-8">
            <a href="{{ route('tips.index') }}"
               class="px-4 py-2 rounded-full {{ !request()->has('category') ? 'bg-green-600 text-white' : 'bg-white text-green-600 border border-green-300' }}">
                All Tips
            </a>
            @foreach($categories as $category)
            <a href="{{ route('tips.index') }}?category={{ $category }}"
               class="px-4 py-2 rounded-full {{ request('category') == $category ? 'bg-green-600 text-white' : 'bg-white text-green-600 border border-green-300' }}">
                {{ ucwords(str_replace('-', ' ', $category)) }}
            </a>
            @endforeach
        </div>

        <!-- Tips Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($tips as $tip)
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-2 rounded-full bg-green-100 text-green-600 mr-4">
                            @if($tip['category'] == 'stove-efficiency')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
                            </svg>
                            @elseif($tip['category'] == 'fuel-saving')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                            </svg>
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                            </svg>
                            @endif
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-800">
                            {{ ucwords(str_replace('-', ' ', $tip['category'])) }}
                        </span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $tip['title'] }}</h3>
                    <p class="text-gray-600">{{ $tip['content'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
