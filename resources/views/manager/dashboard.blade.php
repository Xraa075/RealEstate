<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manager Dashboard') }}
            </h2>
            <form method="GET" class="flex items-center gap-2">
                <label for="year" class="text-sm font-medium text-gray-700">Year:</label>
                <select name="year" id="year" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Enhanced Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Total Assets</h3>
                        <p class="text-3xl">{{ number_format($summaryStats['total_assets']) }}</p>
                        <p class="text-sm text-gray-500">+{{ number_format($summaryStats['assets_this_month']) }} this month</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Pending Requests</h3>
                        <p class="text-3xl">{{ number_format($summaryStats['pending_requests']) }}</p>
                        <p class="text-sm text-gray-500">+{{ number_format($summaryStats['requests_this_month']) }} this month</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Approved Requests</h3>
                        <p class="text-3xl">{{ number_format($summaryStats['approved_requests']) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Asset Value</h3>
                        <p class="text-3xl">{{ number_format($summaryStats['total_asset_value'] / 1000000000, 1) }}B</p>
                        <p class="text-sm text-gray-500">Total IDR</p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Monthly Assets Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Monthly Assets Added ({{ $year }})</h3>
                        <div style="height: 250px;">
                            <canvas id="monthlyAssetsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Monthly Requests Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Monthly Asset Requests ({{ $year }})</h3>
                        <div style="height: 250px;">
                            <canvas id="monthlyRequestsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Asset Status Distribution -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Asset Status Distribution</h3>
                        <div style="height: 250px;">
                            <canvas id="assetStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Request Status Distribution -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Request Status Distribution</h3>
                        <div style="height: 250px;">
                            <canvas id="requestStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('manager.assets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Add New Asset') }}
                        </a>
                        <a href="{{ route('manager.asset-requests.index', ['status' => 'pending']) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Review Pending Requests') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Assets -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Recent Assets</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($recentAssets as $asset)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->asset_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $asset->status }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($asset->value, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('manager.assets.show', $asset) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                            <a href="{{ route('manager.assets.edit', $asset) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center">No assets found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manager.assets.index') }}" class="text-indigo-600 hover:text-indigo-900">View All Assets</a>
                    </div>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Recent Requests</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($recentRequests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($request->type === 'create')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Create
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    Update
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($request->type === 'create')
                                                <span class="text-gray-900 font-medium">{{ $request->proposed_data['name'] ?? 'Unnamed Asset' }}</span>
                                                <br><small class="text-gray-500">{{ $request->proposed_data['asset_code'] ?? 'No Code' }}</small>
                                            @else
                                                <span class="text-gray-900 font-medium">{{ $request->asset->name ?? 'Unknown Asset' }}</span>
                                                <br><small class="text-gray-500">{{ $request->asset->asset_code ?? 'No Code' }}</small>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->requester->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($request->status === 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @elseif ($request->status === 'approved')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Approved
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Rejected
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('manager.asset-requests.show', $request) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                            @if($request->status === 'pending')
                                                <form method="POST" action="{{ route('manager.asset-requests.approve', $request) }}" class="inline mr-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Approve this request?')">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('manager.asset-requests.reject', $request) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Reject this request?')">Reject</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center">No requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('manager.asset-requests.index') }}" class="text-indigo-600 hover:text-indigo-900">View All Requests</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Assets Chart
        const monthlyAssetsCtx = document.getElementById('monthlyAssetsChart').getContext('2d');
        new Chart(monthlyAssetsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($monthlyAssetStats, 'month')) !!},
                datasets: [{
                    label: 'Assets Added',
                    data: {!! json_encode(array_column($monthlyAssetStats, 'count')) !!},
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Monthly Requests Chart
        const monthlyRequestsCtx = document.getElementById('monthlyRequestsChart').getContext('2d');
        new Chart(monthlyRequestsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($monthlyRequestStats, 'month')) !!},
                datasets: [{
                    label: 'Requests Submitted',
                    data: {!! json_encode(array_column($monthlyRequestStats, 'count')) !!},
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Asset Status Distribution Chart
        const assetStatusCtx = document.getElementById('assetStatusChart').getContext('2d');
        new Chart(assetStatusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($assetStatusDistribution->pluck('status')) !!},
                datasets: [{
                    data: {!! json_encode($assetStatusDistribution->pluck('count')) !!},
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)'
                    ],
                    borderColor: [
                        'rgb(34, 197, 94)',
                        'rgb(251, 191, 36)',
                        'rgb(239, 68, 68)',
                        'rgb(168, 85, 247)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });

        // Request Status Distribution Chart
        const requestStatusCtx = document.getElementById('requestStatusChart').getContext('2d');
        new Chart(requestStatusCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($requestStatusDistribution->pluck('status')) !!},
                datasets: [{
                    data: {!! json_encode($requestStatusDistribution->pluck('count')) !!},
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</x-app-layout>
