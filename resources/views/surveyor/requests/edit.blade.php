<x-app-layout>
    @php
        // Pastikan proposed_data selalu berupa array
        if (!is_array($assetRequest->proposed_data)) {
            $assetRequest->proposed_data = json_decode($assetRequest->proposed_data, true) ?? [];
        }
    @endphp
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Asset Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('surveyor.requests.update', $assetRequest) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-700 font-medium">Request Type:</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $assetRequest->type === 'create' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($assetRequest->type) }}
                                </span>
                            </div>

                            @if ($assetRequest->type === 'update' && $assetRequest->asset)
                                <div class="mt-2 flex items-center space-x-2">
                                    <span class="text-gray-700 font-medium">Asset:</span>
                                    <span>{{ $assetRequest->asset->name }} ({{ $assetRequest->asset->asset_code }})</span>
                                </div>
                            @endif

                            <input type="hidden" name="type" value="{{ $assetRequest->type }}">
                            @if ($assetRequest->asset_id)
                                <input type="hidden" name="asset_id" value="{{ $assetRequest->asset_id }}">
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Asset Code (only for create requests) -->
                                @if ($assetRequest->type === 'create')
                                    <div>
                                        <x-input-label for="asset_code" :value="__('Asset Code')" />
                                        <x-text-input id="asset_code" class="block mt-1 w-full" type="text" name="asset_code" :value="old('asset_code', $assetRequest->proposed_data['asset_code'] ?? '')" required />
                                        <x-input-error :messages="$errors->get('asset_code')" class="mt-2" />
                                    </div>
                                @endif

                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $assetRequest->proposed_data['name'] ?? '')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Description -->
                                <div>
                                    <x-input-label for="description" :value="__('Description')" />
                                    <textarea id="description" name="description" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('description', $assetRequest->proposed_data['description'] ?? '') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <!-- Address -->
                                <div>
                                    <x-input-label for="address" :value="__('Address')" />
                                    <textarea id="address" name="address" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="2" required>{{ old('address', $assetRequest->proposed_data['address'] ?? '') }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        <option value="tersedia" {{ old('status', $assetRequest->proposed_data['status'] ?? '') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="disewakan" {{ old('status', $assetRequest->proposed_data['status'] ?? '') == 'disewakan' ? 'selected' : '' }}>Disewakan</option>
                                        <option value="terjual" {{ old('status', $assetRequest->proposed_data['status'] ?? '') == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                        <option value="dalam_sengketa" {{ old('status', $assetRequest->proposed_data['status'] ?? '') == 'dalam_sengketa' ? 'selected' : '' }}>Dalam Sengketa</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                <!-- Owner Name -->
                                <div>
                                    <x-input-label for="owner_name" :value="__('Owner Name')" />
                                    <x-text-input id="owner_name" class="block mt-1 w-full" type="text" name="owner_name" :value="old('owner_name', $assetRequest->proposed_data['owner_name'] ?? '')" required />
                                    <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
                                </div>

                                <!-- Owner Contact -->
                                <div>
                                    <x-input-label for="owner_contact" :value="__('Owner Contact')" />
                                    <x-text-input id="owner_contact" class="block mt-1 w-full" type="text" name="owner_contact" :value="old('owner_contact', $assetRequest->proposed_data['owner_contact'] ?? '')" required />
                                    <x-input-error :messages="$errors->get('owner_contact')" class="mt-2" />
                                </div>

                                <!-- Value -->
                                <div>
                                    <x-input-label for="value" :value="__('Value (IDR)')" />
                                    <x-text-input id="value" class="block mt-1 w-full" type="number" name="value" :value="old('value', $assetRequest->proposed_data['value'] ?? '')" required />
                                    <x-input-error :messages="$errors->get('value')" class="mt-2" />
                                </div>

                                <!-- Area -->
                                <div>
                                    <x-input-label for="area_sqm" :value="__('Area (m²)')" />
                                    <x-text-input id="area_sqm" class="block mt-1 w-full bg-gray-100" type="number" name="area_sqm" :value="old('area_sqm', $assetRequest->proposed_data['area_sqm'] ?? '')" readonly />
                                    <x-input-error :messages="$errors->get('area_sqm')" class="mt-2" />
                                    <p class="text-sm text-gray-500 mt-1">This will be calculated automatically when you draw on the map.</p>
                                </div>

                                <!-- Hidden Geometry Fields -->
                                <input type="hidden" id="geometry" name="geometry" value="{{ old('geometry', $assetRequest->proposed_data['geometry'] ?? '') }}">
                                <input type="hidden" id="original_geometry" value="{{ $assetRequest->proposed_data['geometry'] ?? '' }}">
                            </div>

                            <!-- Right Column (Map) -->
                            <div>
                                <x-input-label :value="__('Draw Asset Boundary on Map')" />
                                <div class="mt-1">
                                    @php
                                        // Buat objek sementara dengan properti geometry untuk dikirim ke komponen map
                                        $mapAssets = [];
                                        if (!empty($assetRequest->proposed_data['geometry'])) {
                                            $tempAsset = new stdClass();
                                            $tempAsset->geometry = $assetRequest->proposed_data['geometry'];
                                            $mapAssets = [$tempAsset];
                                        }
                                    @endphp
                                    <x-map :assets="$mapAssets" height="600px" />
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Use the drawing tools to create a polygon representing the asset boundary.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('surveyor.requests.show', $assetRequest) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('Update Request') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load existing geometry data if available
            const originalGeometryInput = document.getElementById('original_geometry');
            const geometryInput = document.getElementById('geometry');

            function loadExistingGeometry() {
                if (originalGeometryInput && originalGeometryInput.value && window.mapInstance) {
                    try {
                        const geometry = JSON.parse(originalGeometryInput.value);
                        // Load geometry on map
                        window.mapInstance.loadExistingGeometry(geometry);
                        console.log('Loaded existing geometry for editing');
                    } catch (e) {
                        console.error('Error parsing existing geometry data:', e);
                    }
                }
            }

            // Try to load geometry multiple times with increasing delays
            setTimeout(loadExistingGeometry, 500);
            setTimeout(loadExistingGeometry, 1000);
            setTimeout(loadExistingGeometry, 2000);

            // Ensure geometry field is populated when form is submitted
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!geometryInput.value && originalGeometryInput.value) {
                        // If no new geometry drawn, use the original geometry
                        geometryInput.value = originalGeometryInput.value;
                        console.log('Using original geometry for submission');
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
