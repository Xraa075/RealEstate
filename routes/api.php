<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\LandAsset;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API for getting asset data (for form population)
Route::middleware('auth')->get('/assets/{id}', function ($id) {
    $asset = LandAsset::find($id);
    if (!$asset) {
        return response()->json(['error' => 'Asset not found'], 404);
    }

    return response()->json([
        'id' => $asset->id,
        'name' => $asset->name,
        'description' => $asset->description,
        'area_sqm' => $asset->area_sqm,
        'address' => $asset->address,
        'status' => $asset->status,
        'value' => $asset->value,
        'geometry' => $asset->geometry,
        'owner_name' => $asset->owner_name,
        'owner_contact' => $asset->owner_contact,
        'asset_code' => $asset->asset_code,
    ]);
});
