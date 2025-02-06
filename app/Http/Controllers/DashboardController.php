<?php

namespace App\Http\Controllers;

use App\Services\SophosApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private $sophosApi;

    public function __construct(SophosApiService $sophosApi)
    {
        $this->sophosApi = $sophosApi;
    }

    public function index()
    {
        try {
            $metrics = $this->sophosApi->getMetrics();
            
            if (!$metrics) {
                $metrics = [
                    'total' => 0,
                    'high' => 0,
                    'medium' => 0,
                    'low' => 0,
                    'weeklyChange' => [
                        'total' => '0% this week',
                        'high' => '0% this week',
                        'medium' => '0% this week',
                        'low' => '0% this week'
                    ]
                ];
            }

            return view('dashboard', ['riskData' => $metrics]);

        } catch (\Exception $e) {
            Log::error('Error in dashboard:', ['message' => $e->getMessage()]);
            return view('dashboard', [
                'riskData' => [
                    'total' => 0,
                    'high' => 0,
                    'medium' => 0,
                    'low' => 0,
                    'weeklyChange' => [
                        'total' => '0% this week',
                        'high' => '0% this week',
                        'medium' => '0% this week',
                        'low' => '0% this week'
                    ]
                ]
            ]);
        }
    }

    public function getAlertsByCategory($category)
    {
        try {
            Log::info('Getting alerts for category: ' . $category);
            
            $alerts = $this->sophosApi->getAlertsByCategory($category);
            
            if ($alerts === null) {
                Log::error('Failed to get alerts from SophosApiService');
                return response()->json([
                    'error' => 'Failed to fetch alerts',
                    'message' => 'Could not retrieve alerts from Sophos API'
                ], 500);
            }

            return response()->json($alerts);

        } catch (\Exception $e) {
            Log::error('Error in getAlertsByCategory controller:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Server error',
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }

    public function getMetrics()
    {
        try {
            $metrics = $this->sophosApi->getMetrics();
            return response()->json($metrics);
        } catch (\Exception $e) {
            Log::error('Error getting metrics:', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to fetch metrics',
                'message' => 'Could not retrieve metrics data'
            ], 500);
        }
    }
}