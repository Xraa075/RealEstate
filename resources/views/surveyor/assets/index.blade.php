<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Land Assets') }}
            </h2>
            <a href="{{ route('surveyor.requests.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Request New Asset') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Map View -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Asset Map</h3>
                    <x-map :assets="$allAssets" height="500px" />
                </div>
            </div>

            <!-- Table View -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Asset List</h3>

                    <!-- Enhanced Search and Filter -->
                    <div class="mb-6">
                        <form action="{{ route('surveyor.assets.index') }}" method="GET" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Search Input -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                           placeholder="Search by name, code, address, or owner..."
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>

                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">All Statuses</option>
                                        <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="disewakan" {{ request('status') == 'disewakan' ? 'selected' : '' }}>Disewakan</option>
                                        <option value="terjual" {{ request('status') == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                        <option value="dalam_sengketa" {{ request('status') == 'dalam_sengketa' ? 'selected' : '' }}>Dalam Sengketa</option>
                                    </select>
                                </div>

                                <!-- Value Range Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Value Range</label>
                                    <select name="value_range" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">All Values</option>
                                        <option value="0-1000000000" {{ request('value_range') == '0-1000000000' ? 'selected' : '' }}>< 1 Billion</option>
                                        <option value="1000000000-5000000000" {{ request('value_range') == '1000000000-5000000000' ? 'selected' : '' }}>1-5 Billion</option>
                                        <option value="5000000000-10000000000" {{ request('value_range') == '5000000000-10000000000' ? 'selected' : '' }}>5-10 Billion</option>
                                        <option value="10000000000-999999999999" {{ request('value_range') == '10000000000-999999999999' ? 'selected' : '' }}>> 10 Billion</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex justify-between items-center">
                                <div class="flex gap-2">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Search
                                    </button>
                                    <a href="{{ route('surveyor.assets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Clear
                                    </a>
                                </div>

                                <div class="text-sm text-gray-500">
                                    Showing {{ $assets->count() }} of {{ $assets->total() }} assets
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area (m²)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($assets as $asset)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->asset_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($asset->area_sqm) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($asset->status === 'tersedia')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Tersedia
                                                </span>
                                            @elseif ($asset->status === 'disewakan')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Disewakan
                                                </span>
                                            @elseif ($asset->status === 'terjual')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Terjual
                                                </span>
                                            @elseif ($asset->status === 'dalam_sengketa')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Dalam Sengketa
                                                </span>
                                            @else
                                                {{ $asset->status }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($asset->value) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->owner_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                            <a href="{{ route('surveyor.assets.show', $asset) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            <a href="{{ route('surveyor.requests.create', ['type' => 'update', 'asset_id' => $asset->id]) }}" class="text-green-600 hover:text-green-900">Request Update</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center">No assets found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $assets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
