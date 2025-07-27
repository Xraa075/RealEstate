<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\LandAsset;
use App\Models\AssetRequest;
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
     * Display the surveyor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));

        $totalAssets = LandAsset::count();
        $myRequests = AssetRequest::where('requested_by', auth()->id())->count();
        $pendingRequests = AssetRequest::where('requested_by', auth()->id())
            ->where('status', 'pending')
            ->count();
        $approvedRequests = AssetRequest::where('requested_by', auth()->id())
            ->where('status', 'approved')
            ->count();
        $recentAssets = LandAsset::latest()->take(5)->get();
        $myRecentRequests = AssetRequest::where('requested_by', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        // Get statistics for charts
        $monthlyAssetStats = $this->statisticsService->getMonthlyAssetStats($year);
        $monthlyRequestStats = $this->statisticsService->getMonthlyRequestStats($year);
        $assetStatusDistribution = $this->statisticsService->getAssetStatusDistribution();
        $requestStatusDistribution = $this->statisticsService->getRequestStatusDistribution();
        $summaryStats = $this->statisticsService->getSummaryStats();

        return view('surveyor.dashboard', compact(
            'totalAssets',
            'myRequests',
            'pendingRequests',
            'approvedRequests',
            'recentAssets',
            'myRecentRequests',
            'monthlyAssetStats',
            'monthlyRequestStats',
            'assetStatusDistribution',
            'requestStatusDistribution',
            'summaryStats',
            'year'
        ));
    }
}
