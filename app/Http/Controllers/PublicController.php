<?php

namespace App\Http\Controllers;

use App\Models\LandAsset;
use App\Models\AssetDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Display the landing page with assets map and search
     */
    public function index(Request $request)
    {
        $query = LandAsset::query()
            ->where('status', '!=', 'dalam_sengketa') // Hide disputed assets from public
            ->orderBy('created_at', 'desc');

        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('asset_code', 'ILIKE', "%{$search}%")
                  ->orWhere('address', 'ILIKE', "%{$search}%")
                  ->orWhere('owner_name', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('min_value')) {
            $query->where('value', '>=', $request->get('min_value'));
        }

        if ($request->filled('max_value')) {
            $query->where('value', '<=', $request->get('max_value'));
        }

        // Get assets for table (paginated)
        $assets = $query->paginate(12);

        // Get all filtered assets for map display (without pagination)
        $allAssets = $query->get()->map(function ($asset) {
            return [
                'id' => $asset->id,
                'name' => $asset->name,
                'code' => $asset->asset_code,
                'address' => $asset->address,
                'status' => $asset->status,
                'value' => $asset->value,
                'area_sqm' => $asset->area_sqm,
                'geometry' => $asset->geometry,
                'url' => route('public.asset.show', $asset),
            ];
        });

        // Get statistics for display
        $statistics = [
            'total_assets' => LandAsset::where('status', '!=', 'dalam_sengketa')->count(),
            'available_assets' => LandAsset::where('status', 'tersedia')->count(),
            'sold_assets' => LandAsset::where('status', 'terjual')->count(),
            'rented_assets' => LandAsset::where('status', 'disewakan')->count(),
            'total_value' => LandAsset::where('status', '!=', 'dalam_sengketa')->sum('value'),
            'total_area' => LandAsset::where('status', '!=', 'dalam_sengketa')->sum('area_sqm'),
        ];

        // Status options for filter
        $statusOptions = [
            'tersedia' => 'Available',
            'disewakan' => 'Rented',
            'terjual' => 'Sold',
        ];

        return view('public.landing', compact('assets', 'allAssets', 'statistics', 'statusOptions'));
    }

    /**
     * Display asset detail for public view
     */
    public function show(LandAsset $asset)
    {
        // Only show non-disputed assets to public
        if ($asset->status === 'dalam_sengketa') {
            abort(404);
        }

        // Get public documents (non-sensitive documents only)
        $documents = AssetDocument::where('asset_id', $asset->id)
            ->whereIn('document_type', ['Certificate', 'Survey Report', 'Property Photos'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('public.asset-detail', compact('asset', 'documents'));
    }

    /**
     * Search assets via AJAX
     */
    public function search(Request $request)
    {
        $query = LandAsset::query()
            ->where('status', '!=', 'dalam_sengketa');

        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('asset_code', 'ILIKE', "%{$search}%")
                  ->orWhere('address', 'ILIKE', "%{$search}%");
            });
        }

        $assets = $query->limit(10)->get(['id', 'name', 'asset_code as code', 'address', 'status', 'value']);

        return response()->json($assets);
    }
}
