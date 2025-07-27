<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $asset->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.assets.edit', $asset) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit') }}
                </a>
                <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to delete this asset?')">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Map View -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Asset Location</h3>
                    <x-map :assets="[$asset]" height="500px" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Asset Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg col-span-2">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Asset Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Asset Code</p>
                                <p>{{ $asset->asset_code }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <p>
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
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Area</p>
                                <p>{{ number_format($asset->area_sqm) }} m²</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Value</p>
                                <p>IDR {{ number_format($asset->value) }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-500">Address</p>
                                <p>{{ $asset->address }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Owner Name</p>
                                <p>{{ $asset->owner_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Owner Contact</p>
                                <p>{{ $asset->owner_contact }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm font-medium text-gray-500">Description</p>
                                <p>{{ $asset->description ?? 'No description provided.' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Created By</p>
                                <p>{{ $asset->creator->name ?? 'Unknown' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Created At</p>
                                <p>{{ $asset->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Documents</h3>
                            <a href="{{ route('admin.assets.documents.create', $asset) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Add Document
                            </a>
                        </div>

                        @if($asset->documents->count() > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($asset->documents as $document)
                                    <li class="py-3">
                                        <div class="flex justify-between">
                                            <div>
                                                <p class="font-medium">{{ $document->document_name }}</p>
                                                <p class="text-sm text-gray-500">{{ $document->document_type }}</p>
                                                <p class="text-xs text-gray-500">
                                                    Issued: {{ $document->issue_date->format('d M Y') }}
                                                    @if($document->expiry_date)
                                                        | Expires: {{ $document->expiry_date->format('d M Y') }}

                                                        @if($document->isExpired())
                                                            <span class="text-red-600 font-medium">(Expired)</span>
                                                        @endif
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.assets.documents.download', [$asset, $document]) }}" class="text-indigo-600 hover:text-indigo-900" title="Download">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mt-4">
                                <a href="{{ route('admin.assets.documents.index', $asset) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    View All Documents ({{ $asset->documents->count() }})
                                </a>
                            </div>
                        @else
                            <p class="text-gray-500">No documents attached to this asset.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
