<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandAsset;
use App\Models\AssetRequest;
use App\Models\User;
use App\Services\StatisticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $totalAssets = LandAsset::count();
        $pendingRequests = AssetRequest::where('status', 'pending')->count();
        $totalUsers = User::count();
        $recentAssets = LandAsset::latest()->take(5)->get();
        $recentRequests = AssetRequest::with('requester')->latest()->take(5)->get();

        // Get statistics for charts
        $monthlyAssetStats = $this->statisticsService->getMonthlyAssetStats($year);
        $monthlyRequestStats = $this->statisticsService->getMonthlyRequestStats($year);
        $assetStatusDistribution = $this->statisticsService->getAssetStatusDistribution();
        $requestStatusDistribution = $this->statisticsService->getRequestStatusDistribution();
        $monthlyAssetValue = $this->statisticsService->getMonthlyAssetValue($year);
        $summaryStats = $this->statisticsService->getSummaryStats();

        return view('admin.dashboard', compact(
            'totalAssets',
            'pendingRequests',
            'totalUsers',
            'recentAssets',
            'recentRequests',
            'monthlyAssetStats',
            'monthlyRequestStats',
            'assetStatusDistribution',
            'requestStatusDistribution',
            'monthlyAssetValue',
            'summaryStats',
            'year'
        ));
    }
}
