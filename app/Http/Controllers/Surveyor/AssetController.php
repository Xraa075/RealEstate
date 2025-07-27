<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\LandAsset;
use Illuminate\Http\Request;

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

        // Get filtered assets for map display (same filters as table, but without pagination)
        $mapQuery = LandAsset::query();

        // Apply same filters for map
        if ($request->filled('search')) {
            $search = $request->search;
            $mapQuery->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('code', 'ILIKE', "%{$search}%")
                  ->orWhere('address', 'ILIKE', "%{$search}%")
                  ->orWhere('owner', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $mapQuery->where('status', $request->status);
        }

        if ($request->filled('value_range')) {
            $range = explode('-', $request->value_range);
            if (count($range) == 2) {
                $min = (float)$range[0];
                $max = (float)$range[1];
                $mapQuery->whereBetween('value', [$min, $max]);
            }
        }

        $allAssets = $mapQuery->get();

        return view('surveyor.assets.index', compact('assets', 'allAssets'));
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

        return view('surveyor.assets.show', compact('asset'));
    }
}
