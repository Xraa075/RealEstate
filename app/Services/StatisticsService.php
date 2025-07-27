<?php

namespace App\Services;

use App\Models\LandAsset;
use App\Models\AssetRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    /**
     * Get monthly asset statistics
     */
    public function getMonthlyAssetStats($year = null)
    {
        $year = $year ?? Carbon::now()->year;

        $stats = LandAsset::select(
            DB::raw('EXTRACT(MONTH FROM created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', $year)
        ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
        ->orderBy('month')
        ->get()
        ->keyBy('month');

        // Fill missing months with 0
        $monthlyStats = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyStats[] = [
                'month' => Carbon::create()->month($i)->format('M'),
                'count' => $stats->get($i)->count ?? 0
            ];
        }

        return $monthlyStats;
    }

    /**
     * Get monthly asset request statistics
     */
    public function getMonthlyRequestStats($year = null)
    {
        $year = $year ?? Carbon::now()->year;

        $stats = AssetRequest::select(
            DB::raw('EXTRACT(MONTH FROM created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->whereYear('created_at', $year)
        ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
        ->orderBy('month')
        ->get()
        ->keyBy('month');

        // Fill missing months with 0
        $monthlyStats = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyStats[] = [
                'month' => Carbon::create()->month($i)->format('M'),
                'count' => $stats->get($i)->count ?? 0
            ];
        }

        return $monthlyStats;
    }

    /**
     * Get asset status distribution
     */
    public function getAssetStatusDistribution()
    {
        return LandAsset::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => ucfirst(str_replace('_', ' ', $item->status)),
                    'count' => $item->count
                ];
            });
    }

    /**
     * Get request status distribution
     */
    public function getRequestStatusDistribution()
    {
        return AssetRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => ucfirst(str_replace('_', ' ', $item->status)),
                    'count' => $item->count
                ];
            });
    }

    /**
     * Get total asset value by month
     */
    public function getMonthlyAssetValue($year = null)
    {
        $year = $year ?? Carbon::now()->year;

        $stats = LandAsset::select(
            DB::raw('EXTRACT(MONTH FROM created_at) as month'),
            DB::raw('SUM(value) as total_value')
        )
        ->whereYear('created_at', $year)
        ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
        ->orderBy('month')
        ->get()
        ->keyBy('month');

        // Fill missing months with 0
        $monthlyStats = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyStats[] = [
                'month' => Carbon::create()->month($i)->format('M'),
                'value' => $stats->get($i)->total_value ?? 0
            ];
        }

        return $monthlyStats;
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStats()
    {
        return [
            'total_assets' => LandAsset::count(),
            'total_requests' => AssetRequest::count(),
            'pending_requests' => AssetRequest::where('status', 'pending')->count(),
            'approved_requests' => AssetRequest::where('status', 'approved')->count(),
            'rejected_requests' => AssetRequest::where('status', 'rejected')->count(),
            'total_asset_value' => LandAsset::sum('value'),
            'assets_this_month' => LandAsset::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'requests_this_month' => AssetRequest::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];
    }
}
