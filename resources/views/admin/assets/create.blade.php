<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Land Asset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.assets.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Asset Code -->
                                <div>
                                    <x-input-label for="asset_code" :value="__('Asset Code')" />
                                    <x-text-input id="asset_code" class="block mt-1 w-full" type="text" name="asset_code" :value="old('asset_code')" required autofocus />
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
                                    <x-map height="600px" />
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Use the drawing tools to create a polygon representing the asset boundary. <span class="text-red-600 font-semibold">Required: Please draw the asset boundary before submitting.</span></p>
                                <x-input-error :messages="$errors->get('geometry')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.assets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('Create Asset') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.querySelector('form');
                            const geometryInput = document.getElementById('geometry');

                            console.log('Admin form validation script loaded');
                            console.log('Geometry input found:', geometryInput);

                            form.addEventListener('submit', function(e) {
                                const geometryValue = geometryInput.value;
                                console.log('Form submitted, geometry value:', geometryValue);

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
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
