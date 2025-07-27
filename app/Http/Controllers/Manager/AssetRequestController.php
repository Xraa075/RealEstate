<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\AssetRequest;
use App\Models\LandAsset;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetRequestController extends Controller
{
    /**
     * Display a listing of the asset requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = AssetRequest::with(['requester', 'asset']);

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('requester', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('asset', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%");
            });
        }

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

        return view('manager.asset-requests.index', compact('assetRequests'));
    }

    /**
     * Display the specified asset request.
     *
     * @param  \App\Models\AssetRequest  $assetRequest
     * @return \Illuminate\View\View
     */
    public function show(AssetRequest $assetRequest)
    {
        $assetRequest->load(['requester', 'asset', 'approver']);

        // Pastikan proposed_data selalu berupa array
        if (!is_array($assetRequest->proposed_data)) {
            $assetRequest->proposed_data = json_decode($assetRequest->proposed_data, true) ?? [];
        }

        return view('manager.asset-requests.show', compact('assetRequest'));
    }

    /**
     * Approve the specified asset request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AssetRequest  $assetRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, AssetRequest $assetRequest)
    {
        // Only pending requests can be approved
        if ($assetRequest->status !== 'pending') {
            return redirect()->route('manager.asset-requests.show', $assetRequest)
                ->with('error', 'This request has already been processed.');
        }

        try {
            DB::beginTransaction();

            // Update the request status
            $assetRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'notes' => $request->notes,
                'reviewed_at' => now(),
            ]);

            // Process the request based on its type
            if ($assetRequest->type === 'create') {
                // Prepare data for creation
                $assetData = $assetRequest->proposed_data;

                // Calculate area from geometry if area_sqm is null or empty
                if (empty($assetData['area_sqm']) && !empty($assetData['geometry'])) {
                    $assetData['area_sqm'] = $this->calculateAreaFromGeometry($assetData['geometry']);
                }

                // Create a new land asset
                $asset = LandAsset::create(array_merge(
                    $assetData,
                    [
                        'created_by' => $assetRequest->requested_by,
                        'updated_by' => auth()->id(),
                    ]
                ));

                // Log the activity
                ActivityLog::log(
                    'asset_create',
                    'Created new land asset "' . $asset->name . '" from request #' . $assetRequest->id,
                    $request
                );
            } elseif ($assetRequest->type === 'update') {
                // Prepare data for update
                $updateData = $assetRequest->proposed_data;

                // Calculate area from geometry if area_sqm is null or empty
                if (empty($updateData['area_sqm']) && !empty($updateData['geometry'])) {
                    $updateData['area_sqm'] = $this->calculateAreaFromGeometry($updateData['geometry']);
                }

                // Update the existing land asset
                $asset = LandAsset::findOrFail($assetRequest->asset_id);
                $asset->update(array_merge(
                    $updateData,
                    ['updated_by' => auth()->id()]
                ));

                // Log the activity
                ActivityLog::log(
                    'asset_update',
                    'Updated land asset "' . $asset->name . '" from request #' . $assetRequest->id,
                    $request
                );
            }

            // Log the approval
            ActivityLog::log(
                'request_approve',
                'Approved asset request #' . $assetRequest->id . ' from ' . $assetRequest->requester->name,
                $request
            );

            DB::commit();

            return redirect()->route('manager.asset-requests.show', $assetRequest)
                ->with('success', 'Asset request approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('manager.asset-requests.show', $assetRequest)
                ->with('error', 'Error approving request: ' . $e->getMessage());
        }
    }

    /**
     * Reject the specified asset request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AssetRequest  $assetRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, AssetRequest $assetRequest)
    {
        // Only pending requests can be rejected
        if ($assetRequest->status !== 'pending') {
            return redirect()->route('manager.asset-requests.show', $assetRequest)
                ->with('error', 'This request has already been processed.');
        }

        // Validate notes (optional for rejection)
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        // Update the request status
        $assetRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'notes' => $request->notes,
            'reviewed_at' => now(),
        ]);

        // Log the rejection
        ActivityLog::log(
            'request_reject',
            'Rejected asset request #' . $assetRequest->id . ' from ' . $assetRequest->requester->name,
            $request
        );

        return redirect()->route('manager.asset-requests.show', $assetRequest)
            ->with('success', 'Asset request rejected successfully.');
    }

    /**
     * Calculate area from geometry in square meters
     *
     * @param string $geometry JSON geometry string
     * @return float
     */
    private function calculateAreaFromGeometry($geometry)
    {
        try {
            $geom = is_string($geometry) ? json_decode($geometry, true) : $geometry;

            if (!$geom || !isset($geom['coordinates'])) {
                return 0;
            }

            // Simple area calculation using shoelace formula for polygon
            $coordinates = $geom['coordinates'][0]; // First ring (outer boundary)
            $area = 0;
            $n = count($coordinates) - 1; // Exclude the last coordinate which is the same as first

            for ($i = 0; $i < $n; $i++) {
                $j = ($i + 1) % $n;
                $area += ($coordinates[$i][0] * $coordinates[$j][1]);
                $area -= ($coordinates[$j][0] * $coordinates[$i][1]);
            }

            $area = abs($area) / 2;

            // Convert from decimal degrees to square meters (approximation)
            // 1 degree latitude ≈ 111,000 meters
            // 1 degree longitude varies by latitude, but approximately 111,000 * cos(latitude)
            // This is a rough calculation for small areas
            $avgLat = array_sum(array_column($coordinates, 1)) / count($coordinates);
            $mPerDegLat = 111000;
            $mPerDegLng = 111000 * cos(deg2rad($avgLat));

            $areaInSqMeters = $area * $mPerDegLat * $mPerDegLng;

            return round($areaInSqMeters, 2);
        } catch (\Exception $e) {
            Log::error('Error calculating area from geometry: ' . $e->getMessage());
            return 0;
        }
    }
}
