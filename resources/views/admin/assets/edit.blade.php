<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Land Asset') }} - {{ $asset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.assets.update', $asset) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Asset Code -->
                                <div>
                                    <x-input-label for="asset_code" :value="__('Asset Code')" />
                                    <x-text-input id="asset_code" class="block mt-1 w-full" type="text" name="asset_code" :value="old('asset_code', $asset->asset_code)" required autofocus />
                                    <x-input-error :messages="$errors->get('asset_code')" class="mt-2" />
                                </div>

                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $asset->name)" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Description -->
                                <div>
                                    <x-input-label for="description" :value="__('Description')" />
                                    <textarea id="description" name="description" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="3">{{ old('description', $asset->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <!-- Address -->
                                <div>
                                    <x-input-label for="address" :value="__('Address')" />
                                    <textarea id="address" name="address" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="2" required>{{ old('address', $asset->address) }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div>
                                    <x-input-label for="status" :value="__('Status')" />
                                    <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                        <option value="tersedia" {{ old('status', $asset->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="disewakan" {{ old('status', $asset->status) == 'disewakan' ? 'selected' : '' }}>Disewakan</option>
                                        <option value="terjual" {{ old('status', $asset->status) == 'terjual' ? 'selected' : '' }}>Terjual</option>
                                        <option value="dalam_sengketa" {{ old('status', $asset->status) == 'dalam_sengketa' ? 'selected' : '' }}>Dalam Sengketa</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                <!-- Owner Name -->
                                <div>
                                    <x-input-label for="owner_name" :value="__('Owner Name')" />
                                    <x-text-input id="owner_name" class="block mt-1 w-full" type="text" name="owner_name" :value="old('owner_name', $asset->owner_name)" required />
                                    <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
                                </div>

                                <!-- Owner Contact -->
                                <div>
                                    <x-input-label for="owner_contact" :value="__('Owner Contact')" />
                                    <x-text-input id="owner_contact" class="block mt-1 w-full" type="text" name="owner_contact" :value="old('owner_contact', $asset->owner_contact)" required />
                                    <x-input-error :messages="$errors->get('owner_contact')" class="mt-2" />
                                </div>

                                <!-- Value -->
                                <div>
                                    <x-input-label for="value" :value="__('Value (IDR)')" />
                                    <x-text-input id="value" class="block mt-1 w-full" type="number" name="value" :value="old('value', $asset->value)" required />
                                    <x-input-error :messages="$errors->get('value')" class="mt-2" />
                                </div>

                                <!-- Area -->
                                <div>
                                    <x-input-label for="area_sqm" :value="__('Area (m²)')" />
                                    <x-text-input id="area_sqm" class="block mt-1 w-full bg-gray-100" type="number" name="area_sqm" :value="old('area_sqm', $asset->area_sqm)" readonly />
                                    <x-input-error :messages="$errors->get('area_sqm')" class="mt-2" />
                                    <p class="text-sm text-gray-500 mt-1">This will be calculated automatically when you draw on the map. Leave unchanged to keep current geometry.</p>
                                </div>

                                <!-- Hidden Geometry Field -->
                                <input type="hidden" id="geometry" name="geometry" value="{{ old('geometry', json_encode($asset->geometry)) }}">
                                <input type="hidden" id="original_geometry" name="original_geometry" value="{{ json_encode($asset->geometry) }}">
                            </div>

                            <!-- Right Column (Map) -->
                            <div>
                                <x-input-label :value="__('Edit Asset Boundary on Map')" />
                                <div class="mt-1">
                                    <x-map :assets="[$asset]" height="600px" />
                                </div>
                                <p class="text-sm text-gray-500 mt-2">Use the drawing tools to modify the polygon. Leave unchanged to keep current boundary.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.assets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button>
                                {{ __('Update Asset') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // The map component will automatically load existing geometry in edit mode
        // No additional script needed here since it's handled by the map component
        console.log('Admin asset edit page loaded');
    </script>
    @endpush
</x-app-layout>
