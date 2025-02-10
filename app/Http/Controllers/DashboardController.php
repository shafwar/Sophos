<?php
namespace App\Http\Controllers;
use App\Services\SophosApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

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
            // Cache metrics data for 5 minutes
            $metrics = Cache::remember('dashboard_metrics', 300, function () {
                return $this->sophosApi->getMetrics();
            });

            if (!$metrics) {
                $metrics = $this->getDefaultMetrics();
            }

            return view('dashboard', [
                'riskData' => $metrics,
                'pageTitle' => 'Dashboard'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in dashboard:', ['message' => $e->getMessage()]);
            return view('dashboard', [
                'riskData' => $this->getDefaultMetrics(),
                'pageTitle' => 'Dashboard'
            ]);
        }
    }

    public function analytics()
    {
        try {
            // Cache user data for 5 minutes
            $userData = Cache::remember('sophos_users_data', 300, function () {
                return $this->sophosApi->getUsers();
            });

            if (!$userData || !isset($userData['users_list'])) {
                $userData = $this->getDefaultUserData();
            }

            // Transform user data
            $users = collect($userData['users_list'])->map(function ($user) {
                return [
                    'name' => $user['name'] ?? 'Unknown',
                    'email' => $user['email'] ?? 'N/A',
                    'last_online' => $this->formatLastOnline($user['last_online'] ?? null),
                    'devices' => $user['devices'] ?? 'None',
                    'logins' => $user['logins'] ?? 'N/A',
                    'groups' => $this->formatGroups($user['groups'] ?? null),
                    'health_status' => $user['health_status'] ?? 'unknown'
                ];
            })->all();

            // Calculate statistics
            $stats = $this->calculateUserStats($users);

            return view('analytics', [
                'users' => $users,
                'stats' => $stats,
                'userGroups' => $stats['groups'],
                'pageTitle' => 'User Analytics'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in analytics view:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('analytics', [
                'users' => [],
                'stats' => $this->getDefaultStats(),
                'userGroups' => [],
                'pageTitle' => 'User Analytics'
            ]);
        }
    }

    private function formatLastOnline($lastOnline)
    {
        if (!$lastOnline) return 'Never';

        $date = strtotime($lastOnline);
        if (!$date) return 'Never';

        return date('Y-m-d H:i:s', $date);
    }

    private function formatGroups($groups)
    {
        if (is_array($groups)) return $groups;
        if ($groups === null) return ['N/A'];
        return [$groups];
    }

    private function calculateUserStats($users)
    {
        $users = collect($users);

        return [
            'all' => $users->count(),
            'active' => $users->where('last_online', '!=', 'Never')->count(),
            'inactive_2weeks' => $users->filter(function ($user) {
                return $user['last_online'] !== 'Never' &&
                       strtotime($user['last_online']) < strtotime('-2 weeks');
            })->count(),
            'inactive_2months' => $users->filter(function ($user) {
                return $user['last_online'] !== 'Never' &&
                       strtotime($user['last_online']) < strtotime('-2 months');
            })->count(),
            'no_devices' => $users->where('devices', 'None')->count(),
            'groups' => $users->pluck('groups')
                            ->flatten()
                            ->unique()
                            ->values()
                            ->all()
        ];
    }

    private function getDefaultMetrics()
    {
        return [
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

    private function getDefaultUserData()
    {
        return [
            'users_list' => [],
            'stats' => $this->getDefaultStats()
        ];
    }

    private function getDefaultStats()
    {
        return [
            'all' => 0,
            'active' => 0,
            'inactive_2weeks' => 0,
            'inactive_2months' => 0,
            'no_devices' => 0,
            'groups' => []
        ];
    }

    public function getAlertsByCategory($category)
    {
        try {
            Log::info('Getting alerts for category: ' . $category);
            $alerts = Cache::remember("alerts_$category", 300, function () use ($category) {
                return $this->sophosApi->getAlertsByCategory($category);
            });

            if ($alerts === null) {
                Log::error('Failed to get alerts from SophosApiService');
                return response()->json([
                    'error' => 'Failed to fetch alerts',
                    'message' => 'Could not retrieve alerts from Sophos API'
                ], 500);
            }

            return response()->json($alerts);
        } catch (\Exception $e) {
            Log::error('Error in getAlertsByCategory:', [
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
            $metrics = Cache::remember('dashboard_metrics', 300, function () {
                return $this->sophosApi->getMetrics();
            });

            return response()->json($metrics);
        } catch (\Exception $e) {
            Log::error('Error getting metrics:', ['message' => $e->getMessage()]);
            return response()->json([
                'error' => 'Failed to fetch metrics',
                'message' => 'Could not retrieve metrics data'
            ], 500);
        }
    }

    public function overview()
    {
        return view('overview', ['pageTitle' => 'Overview']);
    }

    public function reports()
    {
        $stats = [
            'all' => 640,
            'active' => 599,
            'inactive_2weeks' => 37,
            'inactive_2months' => 0,
            'not_protected' => 4
        ];

        // Buat data computers sebagai array biasa
        $computersData = [
            [
                'name' => '604020000879',
                'online' => '10 minutes ago',
                'last_user' => '604020000879\Rino',
                'real_time_scan' => 'Yes',
                'last_update' => '6 hours ago',
                'last_scan' => 'Never',
                'health_status' => 'warning',
                'group' => 'EJA',
                'agent_installed' => 'Yes'
            ]
        ];

        // Gunakan LengthAwarePaginator untuk membuat pagination manual
        $currentPage = request()->get('page', 1);
        $perPage = 15;

        $computers = new \Illuminate\Pagination\LengthAwarePaginator(
            collect($computersData),
            count($computersData),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        $computerGroups = ['EJA'];

        return view('reports', compact('stats', 'computers', 'computerGroups'));
    }
}
