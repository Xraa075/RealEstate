<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandAsset;
use Illuminate\Http\Request;
use Exception;

class AssetController extends Controller
{
    /**
     * Display a listing of the land assets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = LandAsset::query();

        // Enhanced search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('code', 'ILIKE', "%{$search}%")
                  ->orWhere('address', 'ILIKE', "%{$search}%")
                  ->orWhere('owner', 'ILIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Value range filter
        if ($request->filled('value_range')) {
            $range = explode('-', $request->value_range);
            if (count($range) == 2) {
                $min = (float)$range[0];
                $max = (float)$range[1];
                $query->whereBetween('value', [$min, $max]);
            }
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new land asset.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.assets.create');
    }

    /**
     * Store a newly created land asset in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code' => 'required|string|unique:land_assets,asset_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'area_sqm' => 'nullable|numeric|min:0',
            'address' => 'required|string',
            'status' => 'required|string|in:tersedia,disewakan,terjual,dalam_sengketa',
            'value' => 'required|numeric|min:0',
            'geometry' => 'required|string',
            'owner_name' => 'required|string|max:255',
            'owner_contact' => 'required|string|max:255',
        ]);

        // Validate that geometry contains valid data
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

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        LandAsset::create($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Land asset created successfully.');
    }

    /**
     * Display the specified land asset.
     *
     * @param  \App\Models\LandAsset  $asset
     * @return \Illuminate\View\View
     */
    public function show(LandAsset $asset)
    {
        $asset->load('documents', 'creator');

        return view('admin.assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified land asset.
     *
     * @param  \App\Models\LandAsset  $asset
     * @return \Illuminate\View\View
     */
    public function edit(LandAsset $asset)
    {
        return view('admin.assets.edit', compact('asset'));
    }

    /**
     * Update the specified land asset in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LandAsset  $asset
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LandAsset $asset)
    {
        $validated = $request->validate([
            'asset_code' => 'required|string|unique:land_assets,asset_code,' . $asset->id,
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

        $validated['updated_by'] = auth()->id();

        // Handle geometry update logic
        if (empty($validated['geometry']) ||
            $validated['geometry'] === 'null' ||
            $validated['geometry'] === '' ||
            $validated['geometry'] === $request->input('original_geometry', '')) {
            // Don't update geometry if it's empty or unchanged
            unset($validated['geometry']);
        } else {
            // Validate that the geometry is valid JSON
            $geometryData = json_decode($validated['geometry']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['geometry' => 'Invalid geometry data.'])->withInput();
            }
        }

        $asset->update($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Land asset updated successfully.');
    }

    /**
     * Remove the specified land asset from storage.
     *
     * @param  \App\Models\LandAsset  $asset
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LandAsset $asset)
    {
        $asset->delete();

        return redirect()->route('admin.assets.index')
            ->with('success', 'Land asset deleted successfully.');
    }
}
