<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Asset Request Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('manager.asset-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Request Information</h3>
                        <div>
                            @if ($assetRequest->status === 'pending')
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @elseif ($assetRequest->status === 'approved')
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Approved by {{ $assetRequest->approver->name ?? 'Unknown' }}
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Rejected by {{ $assetRequest->approver->name ?? 'Unknown' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Request ID</p>
                                    <p>{{ $assetRequest->id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Request Type</p>
                                    <p>
                                        @if ($assetRequest->type === 'create')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Create New Asset
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Update Existing Asset
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Requested By</p>
                                    <p>{{ $assetRequest->requester->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Requested At</p>
                                    <p>{{ $assetRequest->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                @if ($assetRequest->reviewed_at)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Reviewed At</p>
                                        <p>{{ $assetRequest->reviewed_at->format('d M Y, H:i') }}</p>
                                    </div>
                                @endif
                                @if ($assetRequest->notes)
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Notes</p>
                                        <p>{{ $assetRequest->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($assetRequest->type === 'update' && $assetRequest->asset)
                            <div>
                                <p class="text-sm font-medium text-gray-500 mb-2">Related Asset</p>
                                <div class="border rounded-md p-4">
                                    <p class="font-semibold">{{ $assetRequest->asset->name }}</p>
                                    <p class="text-sm text-gray-600">Code: {{ $assetRequest->asset->asset_code }}</p>
                                    <p class="text-sm text-gray-600">Status: {{ $assetRequest->asset->status }}</p>
                                    <a href="{{ route('manager.assets.show', $assetRequest->asset) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View Asset Details</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Proposed Data</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($assetRequest->proposed_data as $field => $value)
                                    @if ($field !== 'geometry')
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                            <td class="px-6 py-4">
                                                @if (is_array($value))
                                                    <pre class="text-sm">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if (isset($assetRequest->proposed_data['geometry']))
                        <div class="mt-6">
                            <h4 class="font-medium mb-2">Asset Location</h4>
                            <div style="height: 400px;">
                                @php
                                    // Buat objek sementara dengan properti geometry untuk dikirim ke komponen map
                                    $tempAsset = new stdClass();
                                    $tempAsset->geometry = $assetRequest->proposed_data['geometry'];
                                @endphp
                                <x-map :assets="[$tempAsset]" height="400px" />
                            </div>
                        </div>
                    @endif

                    @if ($assetRequest->status === 'pending')
                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-semibold mb-4">Review Request</h3>
                            <div class="flex space-x-4">
                                <form action="{{ route('manager.asset-requests.approve', $assetRequest) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-4">
                                        <x-input-label for="approve_notes" :value="__('Notes (Optional)')" />
                                        <textarea id="approve_notes" name="notes" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="2"></textarea>
                                    </div>
                                    <x-primary-button class="bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900">
                                        {{ __('Approve Request') }}
                                    </x-primary-button>
                                </form>

                                <form action="{{ route('manager.asset-requests.reject', $assetRequest) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <div class="mb-4">
                                        <x-input-label for="reject_notes" :value="__('Reason for Rejection (Optional)')" />
                                        <textarea id="reject_notes" name="notes" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="2" placeholder="Enter reason for rejection (optional)"></textarea>
                                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                    </div>
                                    <x-danger-button>
                                        {{ __('Reject Request') }}
                                    </x-danger-button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
