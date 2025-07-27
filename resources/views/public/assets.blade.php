<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Public Assets</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Navigation -->
            <nav class="bg-white border-b border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800">
                                Real Estate Assets
                            </a>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ url('/') }}" class="text-gray-600 hover:text-gray-900">Home</a>
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900">Register</a>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Header -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-3xl font-bold text-gray-900">Available Assets</h1>
                    <p class="mt-2 text-gray-600">Browse our real estate portfolio</p>
                </div>
            </header>

            <!-- Content -->
            <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($assets as $asset)
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $asset->name }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">Code: {{ $asset->asset_code }}</p>
                                    <p class="text-sm text-gray-600 mb-2">Area: {{ number_format($asset->area_sqm, 2) }} m²</p>
                                    <p class="text-sm text-gray-600 mb-2">Status:
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($asset->status === 'tersedia') bg-green-100 text-green-800
                                            @elseif($asset->status === 'disewakan') bg-yellow-100 text-yellow-800
                                            @elseif($asset->status === 'terjual') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </p>
                                    <p class="text-sm text-gray-600 mb-4">Address: {{ Str::limit($asset->address, 50) }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-900">
                                            Rp {{ number_format($asset->value, 0, ',', '.') }}
                                        </span>
                                        <a href="{{ route('public.asset.show', $asset) }}"
                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full">
                                <div class="text-center py-12">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No assets available</h3>
                                    <p class="text-gray-600">Check back later for new listings.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
