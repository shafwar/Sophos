<?php
namespace App\Http\Controllers;
use App\Services\SophosApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

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

    public function overview()
    {
        Log::info('Start overview method');
        try {
            // FORCE DUMMY DATA untuk development - HAPUS saat produksi
            $useDummyData = true;

            if ($useDummyData) {
                Log::info('Using forced dummy data');
                $usersData = [
                    'users_list' => [
                        ['name' => 'DESKTOP-3J5K2LM', 'email' => 'user1@example.com', 'last_online' => '3 days ago', 'devices' => '1', 'logins' => 'User1', 'groups' => 'Group A', 'health_status' => 'good'],
                        ['name' => 'DESKPC-HR125', 'email' => 'user2@example.com', 'last_online' => 'Jan 25, 2025', 'devices' => '1', 'logins' => 'User2', 'groups' => 'Group B', 'health_status' => 'warning'],
                        ['name' => 'LAPTOP-RT7X', 'email' => 'user3@example.com', 'last_online' => '1 day ago', 'devices' => '1', 'logins' => 'User3', 'groups' => 'Group C', 'health_status' => 'good'],
                        ['name' => 'DESKTOP-NODEV', 'email' => 'user4@example.com', 'last_online' => 'Never', 'devices' => '', 'logins' => 'User4', 'groups' => 'Group A', 'health_status' => 'warning']
                    ],
                    'all' => 50,
                    'active' => 45,
                    'inactive_2weeks' => 3,
                    'inactive_2months' => 1,
                    'no_devices' => 1
                ];
            } else {
                Log::info('Trying to get data from Sophos API');
                $usersData = $this->sophosApi->getUsers();

                if (!$usersData) {
                    throw new \Exception('Tidak dapat mengambil data dari Sophos API');
                }
            }

            $userGroups = [];
            foreach ($usersData['users_list'] as $user) {
                if (is_array($user['groups'])) {
                    foreach ($user['groups'] as $group) {
                        if (!in_array($group, $userGroups)) {
                            $userGroups[] = $group;
                        }
                    }
                } else if (!empty($user['groups']) && !in_array($user['groups'], $userGroups)) {
                    $userGroups[] = $user['groups'];
                }
            }

            Log::info('Returning view with data', [
                'stats' => [
                    'all' => $usersData['all'],
                    'active' => $usersData['active'],
                    'inactive_2weeks' => $usersData['inactive_2weeks'],
                    'inactive_2months' => $usersData['inactive_2months'],
                    'no_devices' => $usersData['no_devices']
                ]
            ]);

            return view('overview', [
                'users' => $usersData['users_list'],
                'stats' => [
                    'all' => $usersData['all'],
                    'active' => $usersData['active'],
                    'inactive_2weeks' => $usersData['inactive_2weeks'],
                    'inactive_2months' => $usersData['inactive_2months'],
                    'no_devices' => $usersData['no_devices']
                ],
                'userGroups' => $userGroups
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada DashboardController@overview: ' . $e->getMessage());

            // Fallback data jika terjadi error
            $usersData = [
                'users_list' => [
                    ['name' => 'DESKTOP-3J5K2LM', 'email' => 'user1@example.com', 'last_online' => '3 days ago', 'devices' => '1', 'logins' => 'User1', 'groups' => 'Group A', 'health_status' => 'good'],
                    ['name' => 'DESKPC-HR125', 'email' => 'user2@example.com', 'last_online' => 'Jan 25, 2025', 'devices' => '1', 'logins' => 'User2', 'groups' => 'Group B', 'health_status' => 'warning'],
                    ['name' => 'DESKTOP-NODEV', 'email' => 'user4@example.com', 'last_online' => 'Never', 'devices' => '', 'logins' => 'User4', 'groups' => 'Group A', 'health_status' => 'warning']
                ],
                'all' => 50,
                'active' => 45,
                'inactive_2weeks' => 3,
                'inactive_2months' => 1,
                'no_devices' => 1
            ];

            $userGroups = ['Group A', 'Group B'];

            return view('overview', [
                'users' => $usersData['users_list'],
                'stats' => [
                    'all' => $usersData['all'],
                    'active' => $usersData['active'],
                    'inactive_2weeks' => $usersData['inactive_2weeks'],
                    'inactive_2months' => $usersData['inactive_2months'],
                    'no_devices' => $usersData['no_devices']
                ],
                'userGroups' => $userGroups
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

            // Ensure we always return an array
            $alerts = is_array($alerts) ? $alerts : [];

            return response()->json([
                'success' => true,
                'data' => $alerts,
                'category' => $category,
                'total' => count($alerts)
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getAlertsByCategory:', [
                'category' => $category,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the data.',
                'data' => [],
                'category' => $category
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

    public function getWeeklyTrafficRisk()
    {
        try {
            $sophosService = app(SophosApiService::class);
            $alerts = $sophosService->getAllAlerts();

            Log::info('Alerts received:', ['count' => count($alerts)]);

            // Group alerts by month and risk level
            $monthlyData = collect($alerts)->groupBy(function ($alert) {
                return Carbon::parse($alert['raisedAt'])->format('M');
            })->map(function ($monthAlerts) {
                return [
                    'highRisk' => $monthAlerts->where('severity', 'high')->count(),
                    'mediumRisk' => $monthAlerts->where('severity', 'medium')->count(),
                    'lowRisk' => $monthAlerts->where('severity', 'low')->count(),
                ];
            });

            // Transform into the format needed for the chart
            $chartData = $monthlyData->map(function ($risks, $month) {
                return array_merge(['month' => $month], $risks);
            })->values();

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching traffic risk data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch traffic risk data'
            ], 500);
        }
    }

    public function getTrafficRiskDetails($month, $level)
    {
        try {
            $sophosService = app(SophosApiService::class);
            $alerts = $sophosService->getAllAlerts();

            // Filter alerts by month and risk level
            $filteredAlerts = collect($alerts)->filter(function ($alert) use ($month, $level) {
                return Carbon::parse($alert['raisedAt'])->format('M') === $month
                    && strtolower($alert['severity']) === strtolower($level);
            })->map(function ($alert) {
                return [
                    'id' => $alert['id'],
                    'category' => $alert['category'],
                    'description' => $alert['description'],
                    'date' => $alert['raisedAt'],
                    'device' => $alert['endpoint_id'] ?? 'Unknown Device',
                    'source' => $alert['source'] ?? 'Unknown Source',
                    'location' => $alert['location'] ?? 'Unknown Location'
                ];
            })->values();

            return response()->json([
                'success' => true,
                'month' => $month,
                'riskLevel' => $level,
                'incidents' => $filteredAlerts
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching traffic risk details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch risk details'
            ], 500);
        }
    }

    public function getMonthlyDetails($month)
    {
        try {
            $alerts = $this->sophosApi->getAllAlerts();

            // Filter alerts untuk bulan yang dipilih
            $filteredAlerts = collect($alerts)->filter(function ($alert) use ($month) {
                return Carbon::parse($alert['raisedAt'])->format('M') === $month;
            })->map(function ($alert) {
                return [
                    'id' => $alert['id'],
                    'category' => $alert['category'],
                    'description' => $alert['description'],
                    'severity' => $alert['severity'],
                    'date' => $alert['raisedAt'],
                    'source' => $alert['source'] ?? null,
                    'location' => $alert['location'] ?? null,
                    'endpoint_type' => $alert['endpoint_type'] ?? null,
                    'endpoint_id' => $alert['endpoint_id'] ?? null
                ];
            })->values();

            return response()->json([
                'success' => true,
                'month' => $month,
                'incidents' => $filteredAlerts
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching monthly details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch monthly details'
            ], 500);
        }
    }

    public function metricsOverview()
    {
        try {
            // Dapatkan metrics dari Sophos API dan cache selama 5 menit
            $metrics = Cache::remember('dashboard_metrics', 300, function () {
                return $this->sophosApi->getMetrics();
            });

            // Jika metrics gagal didapat, gunakan default
            if (!$metrics) {
                $metrics = $this->getDefaultMetrics();
            }

            // Siapkan data dashboard
            $data = [
                'metrics' => $metrics,
                'workToBill' => 500000,  // Contoh nilai statis
                'quotedWork' => 120000,
                'contracted' => 350000,
                'jobMargin' => 90000,
                'monthlyJobs' => collect([
                    ['month' => 'Jan', 'count' => 15],
                    ['month' => 'Feb', 'count' => 18],
                    ['month' => 'Mar', 'count' => 12],
                    ['month' => 'Apr', 'count' => 20],
                    ['month' => 'May', 'count' => 16],
                    ['month' => 'Jun', 'count' => 22]
                ]),
                'winPercentage' => 60,
                'receivables' => collect([
                    [
                        'invoice_number' => 'INV-2024-001',
                        'date' => Carbon::now()->subDays(5),
                        'amount' => 32000
                    ],
                    [
                        'invoice_number' => 'INV-2024-002',
                        'date' => Carbon::now()->subDays(3),
                        'amount' => 14000
                    ]
                ]),
                'activeJobs' => collect([
                    [
                        'customer' => [
                            'name' => 'ABC Company'
                        ],
                        'job_number' => '15001',
                        'status' => 'In Progress',
                        'value' => 22000
                    ],
                    [
                        'customer' => [
                            'name' => 'Westgate School'
                        ],
                        'job_number' => '65110',
                        'status' => 'Contracted',
                        'value' => 44000
                    ]
                ]),
                'pageTitle' => 'Overview'
            ];

            return view('metrics_overview', $data); // Ubah view juga

        } catch (\Exception $e) {
            Log::error('Error in metricsOverview:', ['message' => $e->getMessage()]);

            // Return view dengan data default jika terjadi error
            return view('metrics_overview', [
                'metrics' => $this->getDefaultMetrics(),
                'workToBill' => 0,
                'quotedWork' => 0,
                'contracted' => 0,
                'jobMargin' => 0,
                'monthlyJobs' => collect([]),
                'winPercentage' => 0,
                'receivables' => collect([]),
                'activeJobs' => collect([]),
                'pageTitle' => 'Metrics Overview'
            ]);
        }
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
