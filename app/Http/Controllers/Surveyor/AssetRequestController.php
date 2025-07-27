<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\AssetRequest;
use App\Models\LandAsset;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class AssetRequestController extends Controller
{
    /**
     * Display a listing of the surveyor's asset requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = AssetRequest::with(['asset'])
            ->where('requested_by', auth()->id());

        // Apply status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Apply type filter
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        // Get asset requests with pagination
        $assetRequests = $query->latest()->paginate(10);

        return view('surveyor.requests.index', compact('assetRequests'));
    }

    /**
     * Show the form for creating a new asset request.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('surveyor.requests.create');
    }

    /**
     * Store a newly created asset request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi dasar untuk semua request
        $baseValidation = [
            'type' => 'required|string|in:create,update',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'area_sqm' => 'nullable|numeric|min:0',
            'address' => 'required|string',
            'status' => 'required|string|in:tersedia,disewakan,terjual,dalam_sengketa',
            'value' => 'required|numeric|min:0',
            'geometry' => 'nullable|string',
            'owner_name' => 'required|string|max:255',
            'owner_contact' => 'required|string|max:255',
        ];

        // Tambahkan validasi khusus berdasarkan tipe request
        if ($request->type === 'create') {
            $baseValidation['asset_code'] = 'required|string|unique:land_assets,asset_code';
        } else { // update
            $baseValidation['asset_id'] = 'required|uuid|exists:land_assets,id';
        }

        $validated = $request->validate($baseValidation);

        // For create requests, validate that geometry is required and valid
        if ($validated['type'] === 'create') {
            if (empty($validated['geometry'])) {
                return back()->withErrors(['geometry' => 'Please draw the asset boundary on the map.'])->withInput();
            }

            try {
                $geometry = json_decode($validated['geometry'], true);
                if (!$geometry || !isset($geometry['coordinates']) || empty($geometry['coordinates'])) {
                    return back()->withErrors(['geometry' => 'Please draw a valid asset boundary on the map.'])->withInput();
                }
            } catch (Exception $e) {
                return back()->withErrors(['geometry' => 'Invalid geometry data. Please redraw the asset boundary.'])->withInput();
            }
        }

        // Create proposed data array
        $proposedData = [
            'name' => $validated['name'],
            'description' => $validated['description'],
            'area_sqm' => $validated['area_sqm'],
            'address' => $validated['address'],
            'status' => $validated['status'],
            'value' => $validated['value'],
            'owner_name' => $validated['owner_name'],
            'owner_contact' => $validated['owner_contact'],
        ];

        // Handle geometry based on request type
        if ($validated['type'] === 'create') {
            $proposedData['geometry'] = $validated['geometry'];
            $proposedData['asset_code'] = $validated['asset_code'];
        } else { // update
            // For update requests, use new geometry if provided, otherwise keep existing
            if (!empty($validated['geometry'])) {
                $proposedData['geometry'] = $validated['geometry'];
            } else {
                // Get existing geometry from the asset being updated
                $existingAsset = \App\Models\LandAsset::find($validated['asset_id']);
                if ($existingAsset && $existingAsset->geometry) {
                    $proposedData['geometry'] = $existingAsset->geometry;
                }
            }
        }

        // Create asset request
        $assetRequest = AssetRequest::create([
            'type' => $validated['type'],
            'asset_id' => $validated['type'] === 'update' ? $validated['asset_id'] : null,
            'proposed_data' => $proposedData,
            'requested_by' => auth()->id(),
            'status' => 'pending',
        ]);

        // Log the activity
        ActivityLog::log(
            'request_create',
            'Created new ' . $validated['type'] . ' asset request #' . $assetRequest->id,
            $request
        );

        return redirect()->route('surveyor.requests.show', $assetRequest)
            ->with('success', 'Asset request created successfully and is pending approval.');
    }

    /**
     * Display the specified asset request.
     *
     * @param  \App\Models\AssetRequest  $assetRequest
     * @return \Illuminate\View\View
     */
    public function show(AssetRequest $assetRequest)
    {
        // Ensure surveyor can only view their own requests
        if ($assetRequest->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $assetRequest->load(['asset', 'approver']);

        // Pastikan proposed_data selalu berupa array
        if (!is_array($assetRequest->proposed_data)) {
            $assetRequest->proposed_data = json_decode($assetRequest->proposed_data, true) ?? [];
        }

        return view('surveyor.requests.show', compact('assetRequest'));
    }

    /**
     * Show the form for editing the specified asset request.
     *
     * @param  \App\Models\AssetRequest  $assetRequest
     * @return \Illuminate\View\View
     */
    public function edit(AssetRequest $assetRequest)
    {
        // Ensure surveyor can only edit their own pending requests
        if ($assetRequest->requested_by !== auth()->id() || $assetRequest->status !== 'pending') {
            abort(403, 'Unauthorized action.');
        }

        return view('surveyor.requests.edit', compact('assetRequest'));
    }

    /**
     * Update the specified asset request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AssetRequest  $assetRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, AssetRequest $assetRequest)
    {
        // Ensure surveyor can only update their own pending requests
        if ($assetRequest->requested_by !== auth()->id() || $assetRequest->status !== 'pending') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'area_sqm' => 'nullable|numeric|min:0',
            'address' => 'required|string',
            'status' => 'required|string|in:tersedia,disewakan,terjual,dalam_sengketa',
            'value' => 'required|numeric|min:0',
            'geometry' => 'nullable|string',
            'owner_name' => 'required|string|max:255',
            'owner_contact' => 'required|string|max:255',
        ]);

        // If this is a create request, validate asset_code
        if ($assetRequest->type === 'create') {
            $validated = array_merge($validated, $request->validate([
                'asset_code' => 'required|string|unique:land_assets,asset_code',
            ]));
        }

        // Pastikan proposed_data adalah array
        $proposedData = $assetRequest->proposed_data;
        if (!is_array($proposedData)) {
            $proposedData = json_decode($proposedData, true) ?? [];
        }

        // Update proposed data
        foreach ($validated as $key => $value) {
            // For geometry, only update if new value is provided, otherwise keep existing
            if ($key === 'geometry') {
                if (!empty($value)) {
                    $proposedData[$key] = $value;
                }
                // If geometry is empty but we have existing geometry, keep the existing one
                // This prevents losing geometry when user doesn't redraw the polygon
            } else {
                $proposedData[$key] = $value;
            }
        }

        $assetRequest->update([
            'proposed_data' => $proposedData,
        ]);

        // Log the activity
        ActivityLog::log(
            'request_update',
            'Updated ' . $assetRequest->type . ' asset request #' . $assetRequest->id,
            $request
        );

        return redirect()->route('surveyor.requests.show', $assetRequest)
            ->with('success', 'Asset request updated successfully.');
    }

    /**
     * Remove the specified asset request from storage.
     * Only allow deletion if request is pending and belongs to the current user.
     *
     * @param  \App\Models\AssetRequest  $assetRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(AssetRequest $assetRequest)
    {
        // Ensure surveyor can only delete their own requests
        if ($assetRequest->requested_by !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow deletion of pending requests
        if ($assetRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'You can only delete pending requests.']);
        }

        // Log the activity before deletion
        ActivityLog::log(
            'request_delete',
            'Deleted ' . $assetRequest->type . ' asset request #' . $assetRequest->id,
            request()
        );

        $assetRequest->delete();

        return redirect()->route('surveyor.requests.index')
            ->with('success', 'Asset request deleted successfully.');
    }
}
