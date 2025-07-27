<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LandAsset;
use App\Models\AssetDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetDocumentController extends Controller
{
    /**
     * Display a listing of the asset documents.
     */
    public function index(LandAsset $asset)
    {
        $documents = $asset->documents()->latest()->get();
        return view('manager.assets.documents.index', compact('asset', 'documents'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create(LandAsset $asset)
    {
        return view('manager.assets.documents.create', compact('asset'));
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request, LandAsset $asset)
    {
        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|string|max:100',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Store the file
        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('asset-documents', $filename, 'private');

        // Create document record
        $asset->documents()->create([
            'document_name' => $validated['document_name'],
            'document_type' => $validated['document_type'],
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'file_path' => $filePath,
        ]);

        return redirect()->route('manager.assets.documents.index', $asset)
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Display the specified document.
     */
    public function show(LandAsset $asset, AssetDocument $document)
    {
        // Check if document belongs to asset
        if ($document->asset_id !== $asset->id) {
            abort(404);
        }

        return view('manager.assets.documents.show', compact('asset', 'document'));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(LandAsset $asset, AssetDocument $document)
    {
        // Check if document belongs to asset
        if ($document->asset_id !== $asset->id) {
            abort(404);
        }

        return view('manager.assets.documents.edit', compact('asset', 'document'));
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, LandAsset $asset, AssetDocument $document)
    {
        // Check if document belongs to asset
        if ($document->asset_id !== $asset->id) {
            abort(404);
        }

        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|string|max:100',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Handle file replacement if new file is uploaded
        if ($request->hasFile('file')) {
            // Delete old file
            if ($document->file_path && Storage::disk('private')->exists($document->file_path)) {
                Storage::disk('private')->delete($document->file_path);
            }

            // Store new file
            $file = $request->file('file');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('asset-documents', $filename, 'private');
            $validated['file_path'] = $filePath;
        }

        $document->update($validated);

        return redirect()->route('manager.assets.documents.index', $asset)
            ->with('success', 'Document updated successfully.');
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(LandAsset $asset, AssetDocument $document)
    {
        // Check if document belongs to asset
        if ($document->asset_id !== $asset->id) {
            abort(404);
        }

        // Delete file from storage
        if ($document->file_path && Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('manager.assets.documents.index', $asset)
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Download the document file.
     */
    public function download(LandAsset $asset, AssetDocument $document)
    {
        // Check if document belongs to asset
        if ($document->asset_id !== $asset->id) {
            abort(404);
        }

        if (!$document->file_path || !Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        $originalName = $document->document_name . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION);
        $fileContent = Storage::disk('private')->get($document->file_path);

        // Determine mime type based on file extension
        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        return response($fileContent)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $originalName . '"');
    }
}
