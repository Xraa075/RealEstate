<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Asset Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('surveyor.requests.store') }}" class="space-y-6">
                        @csrf

                        <!-- Request Type Selection -->
                        <div class="mb-6">
                            <x-input-label for="type" :value="__('Request Type')" />
                            <div class="mt-2 flex space-x-4">
                                <div class="flex items-center">
                                    <input type="radio" id="type_create" name="type" value="create" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ (old('type', request('type')) == 'create' || !request('type')) ? 'checked' : '' }} onchange="toggleAssetSelection(this.value)">
                                    <label for="type_create" class="ml-2 block text-sm text-gray-900">Create New Asset</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="type_update" name="type" value="update" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ old('type', request('type')) == 'update' ? 'checked' : '' }} onchange="toggleAssetSelection(this.value)">
                                    <label for="type_update" class="ml-2 block text-sm text-gray-900">Update Existing Asset</label>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Asset Selection (for update requests) -->
                        <div id="asset_selection" class="{{ old('type', request('type')) == 'update' ? '' : 'hidden' }} mb-6">
                            <x-input-label for="asset_id" :value="__('Select Asset to Update')" />
                            <select id="asset_id" name="asset_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">-- Select an asset --</option>
                                @foreach (App\Models\LandAsset::orderBy('name')->get() as $asset)
                                    <option value="{{ $asset->id }}" {{ old('asset_id', request('asset_id')) == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->name }} ({{ $asset->asset_code }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('asset_id')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Asset Code -->
                                <div id="asset_code_field" class="{{ old('type', request('type')) == 'update' ? 'hidden' : '' }}">
                                    <x-input-label for="asset_code" :value="__('Asset Code')" />
                                    <x-text-input id="asset_code" class="block mt-1 w-full" type="text" name="asset_code" :value="old('asset_code')" />
                                    <x-input-error :messages="$errors->get('asset_code')" class="mt-2" />
                                </div>

                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Description -->
                                <div>
                                    <x-input-label for="description" :value="__('Description')" />
                                    <textarea id="description" name="description" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <!-- Address -->
                                <div>
                                    <x-input-label for="address" :value="__('Address')" />
                                    <textarea id="address" name="address" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="2" required>{{ old('address') }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="disewakan" {{ old('status') == 'disewakan' ? 'selected' : '' }}>Disewakan</option>
                                        <option value="terjual" {{ old('status') == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                        <option value="dalam_sengketa" {{ old('status') == 'dalam_sengketa' ? 'selected' : '' }}>Dalam Sengketa</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                <!-- Owner Name -->
                                <div>
                                    <x-input-label for="owner_name" :value="__('Owner Name')" />
                                    <x-text-input id="owner_name" class="block mt-1 w-full" type="text" name="owner_name" :value="old('owner_name')" required />
                                    <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
                                </div>

                                <!-- Owner Contact -->
                                <div>
                                    <x-input-label for="owner_contact" :value="__('Owner Contact')" />
                                    <x-text-input id="owner_contact" class="block mt-1 w-full" type="text" name="owner_contact" :value="old('owner_contact')" required />
                                    <x-input-error :messages="$errors->get('owner_contact')" class="mt-2" />
                                </div>

                                <!-- Value -->
                                <div>
                                    <x-input-label for="value" :value="__('Value (IDR)')" />
                                    <x-text-input id="value" class="block mt-1 w-full" type="number" name="value" :value="old('value')" required />
                                    <x-input-error :messages="$errors->get('value')" class="mt-2" />
                                </div>

                                <!-- Area -->
                                <div>
                                    <x-input-label for="area_sqm" :value="__('Area (m²)')" />
                                    <x-text-input id="area_sqm" class="block mt-1 w-full bg-gray-100" type="number" name="area_sqm" :value="old('area_sqm')" readonly />
                                    <x-input-error :messages="$errors->get('area_sqm')" class="mt-2" />
                                    <p class="text-sm text-gray-500 mt-1">This will be calculated automatically when you draw on the map.</p>
                                </div>

                                <!-- Hidden Geometry Field -->
                                <input type="hidden" id="geometry" name="geometry" value="{{ old('geometry') }}">
                            </div>

                            <!-- Right Column (Map) -->
                            <div>
                                <x-input-label :value="__('Draw Asset Boundary on Map')" />
                                <div class="mt-1">
                                    @php
                                        // Buat objek sementara dengan properti geometry untuk dikirim ke komponen map
                                        $mapAssets = [];
                                        if (old('geometry')) {
                                            $tempAsset = new stdClass();
                                            $tempAsset->geometry = old('geometry');
                                            $mapAssets = [$tempAsset];
                                        }
                                    @endphp
                                    <x-map :assets="$mapAssets" height="600px" />
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Use the drawing tools to create a polygon representing the asset boundary. <span class="text-red-600 font-semibold">Required: Please draw the asset boundary before submitting.</span></p>
                                <x-input-error :messages="$errors->get('geometry')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('surveyor.requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('Submit Request') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleAssetSelection(type) {
            const assetSelectionDiv = document.getElementById('asset_selection');
            const assetCodeField = document.getElementById('asset_code_field');

            if (type === 'update') {
                assetSelectionDiv.classList.remove('hidden');
                assetCodeField.classList.add('hidden');
            } else {
                assetSelectionDiv.classList.add('hidden');
                assetCodeField.classList.remove('hidden');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const selectedType = document.querySelector('input[name="type"]:checked').value;
            toggleAssetSelection(selectedType);

            // Fetch asset data if asset_id is selected
            const assetIdSelect = document.getElementById('asset_id');
            assetIdSelect.addEventListener('change', function() {
                if (this.value) {
                    // Fetch asset data via AJAX and populate form fields
                    fetch(`/api/assets/${this.value}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                // Populate form fields with existing asset data
                                document.getElementById('name').value = data.name || '';
                                document.getElementById('description').value = data.description || '';
                                document.getElementById('area_sqm').value = data.area_sqm || '';
                                document.getElementById('address').value = data.address || '';
                                document.getElementById('status').value = data.status || '';
                                document.getElementById('value').value = data.value || '';
                                document.getElementById('owner_name').value = data.owner_name || '';
                                document.getElementById('owner_contact').value = data.owner_contact || '';

                                // Handle geometry
                                if (data.geometry) {
                                    document.getElementById('geometry').value = data.geometry;

                                    // Load geometry on map if map instance is available
                                    if (window.mapInstance && typeof window.mapInstance.loadExistingGeometry === 'function') {
                                        try {
                                            const geometry = JSON.parse(data.geometry);
                                            setTimeout(() => {
                                                window.mapInstance.loadExistingGeometry(geometry);
                                            }, 500);
                                        } catch (e) {
                                            console.error('Error loading geometry:', e);
                                        }
                                    }
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching asset data:', error);
                        });
                } else {
                    // Clear form when no asset selected
                    const fields = ['name', 'description', 'area_sqm', 'address', 'status', 'value', 'owner_name', 'owner_contact', 'geometry'];
                    fields.forEach(field => {
                        const element = document.getElementById(field);
                        if (element) element.value = '';
                    });
                }
            });

            // Validate geometry before form submission
            const form = document.querySelector('form');
            const geometryInput = document.getElementById('geometry');
            const typeRadios = document.querySelectorAll('input[name="type"]');

            form.addEventListener('submit', function(e) {
                const selectedType = document.querySelector('input[name="type"]:checked').value;

                // Only validate geometry for new asset creation
                if (selectedType === 'create') {
                    const geometryValue = geometryInput.value;

                    if (!geometryValue || geometryValue.trim() === '') {
                        e.preventDefault();
                        alert('Please draw the asset boundary on the map before submitting the form.');
                        return false;
                    }

                    try {
                        const geometry = JSON.parse(geometryValue);
                        if (!geometry || !geometry.coordinates || geometry.coordinates.length === 0) {
                            e.preventDefault();
                            alert('Please draw a valid asset boundary on the map before submitting the form.');
                            return false;
                        }
                    } catch (error) {
                        e.preventDefault();
                        alert('Invalid geometry data. Please redraw the asset boundary on the map.');
                        return false;
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
