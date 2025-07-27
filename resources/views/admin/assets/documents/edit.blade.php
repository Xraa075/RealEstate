<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Document - {{ $asset->name }}
            </h2>
            <a href="{{ route('admin.assets.documents.index', $asset) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Documents
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Asset Information</h3>
                        <p class="text-sm text-gray-600">{{ $asset->asset_code }} - {{ $asset->address }}</p>
                    </div>

                    <form action="{{ route('admin.assets.documents.update', [$asset, $document]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="document_name" class="block text-sm font-medium text-gray-700">Document Name</label>
                            <input type="text" name="document_name" id="document_name" value="{{ old('document_name', $document->document_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            @error('document_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type</label>
                            <select name="document_type" id="document_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="">Select Document Type</option>
                                <option value="Sertifikat Tanah" {{ old('document_type', $document->document_type) === 'Sertifikat Tanah' ? 'selected' : '' }}>Sertifikat Tanah</option>
                                <option value="IMB" {{ old('document_type', $document->document_type) === 'IMB' ? 'selected' : '' }}>IMB (Izin Mendirikan Bangunan)</option>
                                <option value="PBB" {{ old('document_type', $document->document_type) === 'PBB' ? 'selected' : '' }}>PBB (Pajak Bumi dan Bangunan)</option>
                                <option value="AMDAL" {{ old('document_type', $document->document_type) === 'AMDAL' ? 'selected' : '' }}>AMDAL</option>
                                <option value="Perjanjian Jual Beli" {{ old('document_type', $document->document_type) === 'Perjanjian Jual Beli' ? 'selected' : '' }}>Perjanjian Jual Beli</option>
                                <option value="Surat Kuasa" {{ old('document_type', $document->document_type) === 'Surat Kuasa' ? 'selected' : '' }}>Surat Kuasa</option>
                                <option value="Survey Report" {{ old('document_type', $document->document_type) === 'Survey Report' ? 'selected' : '' }}>Survey Report</option>
                                <option value="Lainnya" {{ old('document_type', $document->document_type) === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('document_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="issue_date" class="block text-sm font-medium text-gray-700">Issue Date</label>
                                <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', $document->issue_date->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                @error('issue_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date (Optional)</label>
                                <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $document->expiry_date ? $document->expiry_date->format('Y-m-d') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('expiry_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Leave empty if the document doesn't expire</p>
                            </div>
                        </div>

                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700">Document File</label>
                            <input type="file" name="file" id="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Leave empty to keep current file. Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG. Maximum size: 10MB</p>

                            @if($document->file_path)
                                <div class="mt-2 p-3 bg-gray-50 rounded-md">
                                    <p class="text-sm text-gray-600">Current file:
                                        <a href="{{ route('admin.assets.documents.download', [$asset, $document]) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            {{ $document->document_name }}.{{ pathinfo($document->file_path, PATHINFO_EXTENSION) }}
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.assets.documents.index', $asset) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Update Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
