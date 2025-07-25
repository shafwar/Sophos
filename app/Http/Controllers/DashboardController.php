<?php
namespace App\Http\Controllers;
use App\Services\SophosApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    private $sophosApi;

    public function __construct(SophosApiService $sophosApi)
    {
        $this->sophosApi = $sophosApi;
    }

    /**
     * Dashboard utama - Akses: Admin & User
     * Menampilkan ringkasan data dan metrik keamanan
     */
    public function index()
    {
        $user = Auth::user();
        // Catat activity log manual (tanpa model)
        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'activity' => 'Membuka dashboard',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Ambil data metrics
        if ($user->role === 'user') {
            // Ambil semua data alert dari API
            $allAlerts = $this->sophosApi->getAllAlerts();
            $userName = strtolower(trim($user->name));
            $userAlerts = collect($allAlerts)->filter(function($alert) use ($userName) {
                $desc = strtolower($alert['description'] ?? '');
                // Cek apakah nama user ada di path/file description
                return $userName && str_contains($desc, $userName);
            });
            // Hitung metrik hanya dari $userAlerts
            $metrics = [
                'total' => $userAlerts->count(),
                'high' => $userAlerts->where('severity', 'high')->count(),
                'medium' => $userAlerts->where('severity', 'medium')->count(),
                'low' => $userAlerts->where('severity', 'low')->count(),
                'weeklyChange' => [
                    'total' => '0% this week',
                    'high' => '0% this week',
                    'medium' => '0% this week',
                    'low' => '0% this week'
                ]
            ];
            return view('dashboard', ['riskData' => $metrics]);
        }
        // Admin: tetap tampilkan semua data
        if ($user->role === 'admin') {
            $metrics = $this->sophosApi->getMetrics() ?? $this->getDefaultMetrics();
            return view('admin_dashboard', ['riskData' => $metrics]);
        }
        abort(403);
    }

    /**
     * Overview dashboard - Akses: Admin & User
     * Menampilkan gambaran umum sistem keamanan
     */
    public function overview()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') abort(403);
        try {
            $usersData = $this->sophosApi->getUsers();

            if (!$usersData) {
                throw new \Exception('Tidak dapat mengambil data dari Sophos API');
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
            return view('overview', [
                'users' => [],
                'stats' => [
                    'all' => 0,
                    'active' => 0,
                    'inactive_2weeks' => 0,
                    'inactive_2months' => 0,
                    'no_devices' => 0
                ],
                'userGroups' => []
            ]);
        }
    }

    /**
     * Analytics dashboard - Akses: Admin & User
     * Menampilkan analisis dan statistik keamanan
     */
    public function analytics()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') abort(403);
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

    /**
     * Get Alerts by Category - Akses: Admin & User
     * Mendapatkan alert berdasarkan kategori
     */
    public function getAlertsByCategory($category)
    {
        try {
            Log::info('Getting alerts for category: ' . $category);

            if (strtolower($category) === 'all risk') {
                $alerts = $this->sophosApi->getAllAlerts();
            } else {
                $alerts = $this->sophosApi->getAlertsByCategory($category);
            }

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

    /**
     * Get Metrics - Akses: Admin & User
     * Mendapatkan metrik keamanan
     */
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

    /**
     * Get Weekly Traffic Risk - Akses: Admin & User
     * Mendapatkan data risiko traffic mingguan
     */
    public function getWeeklyTrafficRisk()
    {
        try {
            $user = Auth::user();
            $sophosService = app(SophosApiService::class);
            $alerts = $sophosService->getAllAlerts();

            // Jika user biasa, filter hanya data miliknya
            if ($user->role !== 'admin') {
                $userName = strtolower(trim($user->name));
                $alerts = collect($alerts)->filter(function($alert) use ($userName) {
                    $desc = strtolower($alert['description'] ?? '');
                    return $userName && str_contains($desc, $userName);
                });
            }

            // Daftar bulan Jan–Des
            $allMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

            // Group alerts by month and risk level
            $monthlyData = collect($alerts)->groupBy(function ($alert) {
                return \Carbon\Carbon::parse($alert['raisedAt'])->format('M');
            })->map(function ($monthAlerts) {
                return [
                    'highRisk' => $monthAlerts->where('severity', 'high')->count(),
                    'mediumRisk' => $monthAlerts->where('severity', 'medium')->count(),
                    'lowRisk' => $monthAlerts->where('severity', 'low')->count(),
                ];
            });

            // Pastikan semua bulan ada, meski 0
            $chartData = [];
            foreach ($allMonths as $month) {
                $chartData[] = array_merge(
                    ['month' => $month],
                    $monthlyData->get($month, ['highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0])
                );
            }

            // Jika data kosong, tambahkan data dummy untuk testing
            $hasData = collect($chartData)->sum(function($item) {
                return $item['highRisk'] + $item['mediumRisk'] + $item['lowRisk'];
            });
            if ($hasData === 0) {
                $chartData = [
                    ['month' => 'Jan', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'Feb', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'Mar', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'Apr', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'May', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'Jun', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'Jul', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'Aug', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'Sep', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'Oct', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'Nov', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                    ['month' => 'Dec', 'highRisk' => 0, 'mediumRisk' => 0, 'lowRisk' => 0],
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching traffic risk data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch traffic risk data'
            ], 500);
        }
    }

    /**
     * Get Traffic Risk Details - Akses: Admin & User
     * Mendapatkan detail risiko traffic
     */
    public function getTrafficRiskDetails($month, $level)
    {
        try {
            $user = Auth::user();
            $sophosService = app(SophosApiService::class);
            $alerts = $sophosService->getAllAlerts();

            // Filter alerts by month and risk level
            $filteredAlerts = collect($alerts)->filter(function ($alert) use ($month, $level, $user) {
                $matchesMonthAndLevel = Carbon::parse($alert['raisedAt'])->format('M') === $month
                    && strtolower($alert['severity']) === strtolower($level);
                
                // Jika user biasa, filter hanya yang mengandung nama mereka
                if ($user->role !== 'admin') {
                    return $matchesMonthAndLevel && 
                           str_contains(strtolower($alert['description']), strtolower($user->name));
                }
                
                return $matchesMonthAndLevel;
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

    /**
     * Get Monthly Details - Akses: Admin & User
     * Mendapatkan detail data bulanan
     */
    public function getMonthlyDetails($month)
    {
        try {
            $user = Auth::user();
            $alerts = $this->sophosApi->getAllAlerts();

            // Filter alerts untuk bulan yang dipilih
            $filteredAlerts = collect($alerts)->filter(function ($alert) use ($month, $user) {
                $matchesMonth = Carbon::parse($alert['raisedAt'])->format('M') === $month;
                
                // Jika user biasa, filter hanya yang mengandung nama mereka
                if ($user->role !== 'admin') {
                    return $matchesMonth && 
                           str_contains(strtolower($alert['description']), strtolower($user->name));
                }
                
                return $matchesMonth;
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

    /**
     * Reports page - Akses: Admin & User
     * Menampilkan laporan keamanan
     */
    public function reports()
    {
        $user = Auth::user();
        try {
            $computersData = $this->sophosApi->getComputers();
            $computers = collect($computersData['computers_list'] ?? []);
            // Jika user biasa, filter hanya milik sendiri (berdasarkan nama user)
            if ($user->role !== 'admin') {
                $computers = $computers->filter(function ($comp) use ($user) {
                    // Sesuaikan field 'logins' jika ingin filter berdasarkan email/ganti field
                    return strtolower($comp['logins'] ?? '') === strtolower($user->name);
                });
            }
            $stats = $computersData['stats'] ?? [
                'all' => 0,
                'active' => 0,
                'inactive_2weeks' => 0,
                'inactive_2months' => 0,
                'not_protected' => 0
            ];
            $computerGroups = $computers->pluck('group')->unique()->values()->all();
            $currentPage = request()->get('page', 1);
            $perPage = 15;
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $computers->forPage($currentPage, $perPage),
                $computers->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url()]
            );
            return view('reports', [
                'stats' => $stats,
                'computers' => $paginated,
                'computerGroups' => $computerGroups
            ]);
        } catch (\Exception $e) {
            Log::error('Error in reports:', ['message' => $e->getMessage()]);
            return view('reports', [
                'stats' => [
                    'all' => 0,
                    'active' => 0,
                    'inactive_2weeks' => 0,
                    'inactive_2months' => 0,
                    'not_protected' => 0
                ],
                'computers' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'computerGroups' => []
            ]);
        }
    }

    /**
     * Activity Log - Akses: Admin
     * Menampilkan log aktivitas semua user
     */
    public function activityLog()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $totalUsers = \App\Models\User::count();
            $activeUsers = \App\Models\User::count();
            $todaysLogins = DB::table('activity_logs')
                ->where('activity', 'Login')
                ->whereDate('created_at', now()->toDateString())
                ->count();
            $totalLogins = DB::table('activity_logs')
                ->where('activity', 'Login')
                ->count();
            $logs = DB::table('activity_logs')
                ->join('users', 'activity_logs.user_id', '=', 'users.id')
                ->select('users.name as user_name', 'activity_logs.activity', 'activity_logs.created_at')
                ->orderByDesc('activity_logs.created_at')
                ->limit(50)
                ->get();
        } else {
            $totalUsers = 1;
            $activeUsers = 1;
            $todaysLogins = DB::table('activity_logs')
                ->where('activity', 'Login')
                ->where('user_id', $user->id)
                ->whereDate('created_at', now()->toDateString())
                ->count();
            $totalLogins = DB::table('activity_logs')
                ->where('activity', 'Login')
                ->where('user_id', $user->id)
                ->count();
            $logs = DB::table('activity_logs')
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();
        }
        return view('activity_log', compact('logs', 'totalUsers', 'activeUsers', 'todaysLogins', 'totalLogins'));
    }

    /**
     * Pending Users - Akses: Admin
     * Menampilkan daftar user yang menunggu persetujuan
     */
    public function pendingUsers()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') abort(403);
        $pendingUsers = \App\Models\User::pending()->get();
        return view('admin_pending_users', compact('pendingUsers'));
    }

    /**
     * Approve User - Akses: Admin
     * Menyetujui pendaftaran user baru
     */
    public function approveUser($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') abort(403);
        $pendingUser = \App\Models\User::findOrFail($id);
        $pendingUser->approve();
        // Log ke activity_logs
        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'activity' => 'Approve user: ' . $pendingUser->email,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->back()->with('success', 'User approved!');
    }

    /**
     * Decline User - Akses: Admin
     * Menolak pendaftaran user baru
     */
    public function declineUser($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') abort(403);
        $pendingUser = \App\Models\User::findOrFail($id);
        $pendingUser->status = 'declined';
        $pendingUser->save();
        // Log ke activity_logs
        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'activity' => 'Decline user: ' . $pendingUser->email,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->back()->with('error', 'User declined!');
    }

    /**
     * User List - Akses: Admin
     * Menampilkan daftar semua user
     */
    public function userList()
    {
        $user = Auth::user();
        if ($user->role !== 'admin') abort(403);
        $users = \App\Models\User::select('id', 'name', 'email')->get();
        return response()->json($users);
    }

    /**
     * Delete User - Akses: Admin
     * Menghapus user dari sistem
     */
    public function deleteUser($id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') abort(403);
        $target = \App\Models\User::findOrFail($id);
        if ($target->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Admin tidak bisa dihapus'], 403);
        }
        $target->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Export User Log - Akses: User
     * Mengekspor log aktivitas user yang login
     */
    public function exportUserLog()
    {
        $user = Auth::user();
        $logs = \DB::table('activity_logs')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        $filename = 'activity_log_' . strtolower(str_replace(' ', '_', $user->name)) . '_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['Activity', 'Tanggal', 'Waktu'];

        $callback = function() use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($logs as $log) {
                $date = \Carbon\Carbon::parse($log->created_at);
                fputcsv($file, [
                    $log->activity,
                    $date->format('Y-m-d'),
                    $date->format('H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * History Data - Akses: User
     * Menampilkan riwayat risiko untuk user yang login
     */
    public function historyData(Request $request)
    {
        $user = Auth::user();
        $name = $user->name;
        $riskLevel = $request->get('level');
        $month = $request->get('month');
        $search = $request->get('search');
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        // Ambil data risk dari API
        $sophosApi = app(\App\Services\SophosApiService::class);
        $allRisks = $sophosApi->getAllAlerts();

        // Filter berdasarkan nama user di description (case-insensitive)
        $userName = strtolower(trim($name));
        $userRisks = collect($allRisks)->filter(function($risk) use ($userName) {
            $desc = strtolower($risk['description'] ?? '');
            return $userName && str_contains($desc, $userName);
        });

        // Filter risk level jika ada
        if ($riskLevel) {
            $userRisks = $userRisks->filter(function($risk) use ($riskLevel) {
                return strtolower($risk['severity'] ?? '') === strtolower($riskLevel);
            });
        }

        // Filter bulan jika ada
        if ($month) {
            $userRisks = $userRisks->filter(function($risk) use ($month) {
                if (!isset($risk['raisedAt'])) return false;
                return \Carbon\Carbon::parse($risk['raisedAt'])->format('Y-m') === $month;
            });
        }

        // Filter search jika ada
        if ($search) {
            $userRisks = $userRisks->filter(function($risk) use ($search) {
                return stripos($risk['description'] ?? '', $search) !== false;
            });
        }

        // Filter tanggal jika ada
        if ($tanggalAwal && $tanggalAkhir) {
            $userRisks = $userRisks->filter(function($risk) use ($tanggalAwal, $tanggalAkhir) {
                if (!isset($risk['raisedAt'])) return false;
                $date = \Carbon\Carbon::parse($risk['raisedAt'])->format('Y-m-d');
                return $date >= $tanggalAwal && $date <= $tanggalAkhir;
            });
        }

        // Ambil daftar bulan unik untuk filter
        $months = $userRisks->pluck('raisedAt')->filter()->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('Y-m');
        })->unique()->values();

        // DEBUG: Log contoh data risk mentah dari API
        \Log::info('Contoh data risk dari API:', [
            'sample' => array_slice($allRisks, 0, 3)
        ]);

        return view('history', [
            'risks' => $userRisks->values(),
            'months' => $months,
            'selectedLevel' => $riskLevel,
            'selectedMonth' => $month,
            'search' => $search,
        ]);
    }

    /**
     * Export History Data - Akses: User
     * Mengekspor riwayat risiko user dalam format yang dipilih
     */
    public function exportHistoryData(Request $request)
    {
        $user = Auth::user();
        $name = $user->name;
        $riskLevel = $request->get('level');
        $month = $request->get('month');
        $search = $request->get('search');
        $format = $request->get('format', 'csv');
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        // Ambil data risk dari API
        $sophosApi = app(\App\Services\SophosApiService::class);
        $allRisks = $sophosApi->getAllAlerts();

        // Filter berdasarkan nama user di description (case-insensitive)
        $userName = strtolower(trim($name));
        $userRisks = collect($allRisks)->filter(function($risk) use ($userName) {
            $desc = strtolower($risk['description'] ?? '');
            return $userName && str_contains($desc, $userName);
        });
        if ($riskLevel) {
            $userRisks = $userRisks->filter(function($risk) use ($riskLevel) {
                return strtolower($risk['severity'] ?? '') === strtolower($riskLevel);
            });
        }
        if ($month) {
            $userRisks = $userRisks->filter(function($risk) use ($month) {
                if (!isset($risk['raisedAt'])) return false;
                return \Carbon\Carbon::parse($risk['raisedAt'])->format('Y-m') === $month;
            });
        }
        if ($search) {
            $userRisks = $userRisks->filter(function($risk) use ($search) {
                return stripos($risk['description'] ?? '', $search) !== false;
            });
        }
        if ($tanggalAwal && $tanggalAkhir) {
            $userRisks = $userRisks->filter(function($risk) use ($tanggalAwal, $tanggalAkhir) {
                if (!isset($risk['raisedAt'])) return false;
                $date = \Carbon\Carbon::parse($risk['raisedAt'])->format('Y-m-d');
                return $date >= $tanggalAwal && $date <= $tanggalAkhir;
            });
        }
        $userRisks = $userRisks->values();

        // Export CSV
        if ($format === 'csv') {
            $filename = 'history_risk_' . strtolower(str_replace(' ', '_', $user->name)) . '_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            $columns = ['ID', 'Severity', 'Date', 'Category', 'Description'];
            $callback = function() use ($userRisks, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                foreach ($userRisks as $risk) {
                    fputcsv($file, [
                        $risk['id'] ?? '',
                        $risk['severity'] ?? '',
                        $risk['raisedAt'] ?? '',
                        $risk['category'] ?? '',
                        $risk['description'] ?? '',
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }
        // Export XLSX/PDF
        $export = new \App\Exports\RiskExport($userRisks->toArray());
        $filename = 'history_risk_' . strtolower(str_replace(' ', '_', $user->name)) . '_' . now()->format('Ymd_His') . '.' . $format;
        $excelFormat = $format === 'pdf' ? \Maatwebsite\Excel\Excel::DOMPDF : \Maatwebsite\Excel\Excel::XLSX;
        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename, $excelFormat);
    }

    public function getUserAlerts(Request $request)
    {
        $user = Auth::user();
        $category = $request->input('category');
        $allAlerts = $this->sophosApi->getAllAlerts();
        if (!$allAlerts || !is_array($allAlerts)) {
            return response()->json(['alerts' => []], 200);
        }
        $userName = strtolower(trim($user->name));
        $userAlerts = collect($allAlerts)->filter(function($alert) use ($userName) {
            $desc = strtolower($alert['description'] ?? '');
            return $userName && str_contains($desc, $userName);
        });
        if ($category && $category !== 'All Risk') {
            $severity = strtolower(explode(' ', $category)[0]);
            $userAlerts = $userAlerts->where('severity', $severity);
        }
        // Sort by date desc
        $userAlerts = $userAlerts->sortByDesc(function($alert) {
            return $alert['created_at'] ?? $alert['raisedAt'] ?? '';
        })->values();

        // Map agar field selalu ada
        $alerts = $userAlerts->map(function($alert) {
            return [
                'id' => $alert['id'] ?? '-',
                'category' => $alert['category'] ?? '-',
                'description' => $alert['description'] ?? '-',
                'severity' => $alert['severity'] ?? '-',
                'raisedAt' => $alert['raisedAt'] ?? ($alert['created_at'] ?? '-'),
                'created_at' => $alert['created_at'] ?? ($alert['raisedAt'] ?? '-'),
            ];
        })->all();

        return response()->json(['alerts' => $alerts], 200);
    }

    public function getUserAlertsByCategory($category)
    {
        $user = Auth::user();
        $allAlerts = $this->sophosApi->getAllAlerts();
        \Log::info('User Alert Debug', [
            'user' => $user->name,
            'category' => $category,
            'sample_alert' => is_array($allAlerts) ? array_slice($allAlerts, 0, 2) : $allAlerts
        ]);
        if (!$allAlerts || !is_array($allAlerts)) {
            return response()->json(['data' => [], 'debug' => 'No alerts from API'], 200);
        }
        $userName = strtolower(trim($user->name));
        $userAlerts = collect($allAlerts)->filter(function($alert) use ($userName) {
            $desc = strtolower($alert['description'] ?? '');
            return $userName && str_contains($desc, $userName);
        });
        \Log::info('User Alert Debug', [
            'filtered_count' => $userAlerts->count(),
            'filtered_sample' => $userAlerts->slice(0,2)->values()
        ]);
        if ($category && strtolower($category) !== 'all risk') {
            $severity = strtolower(explode(' ', $category)[0]);
            $userAlerts = $userAlerts->where('severity', $severity);
        }
        $userAlerts = $userAlerts->sortByDesc(function($alert) {
            return $alert['created_at'] ?? $alert['raisedAt'] ?? '';
        })->values();
        $alerts = $userAlerts->map(function($alert) {
            return [
                'id' => $alert['id'] ?? '-',
                'category' => $alert['category'] ?? '-',
                'description' => $alert['description'] ?? '-',
                'severity' => $alert['severity'] ?? '-',
                'raisedAt' => $alert['raisedAt'] ?? ($alert['created_at'] ?? '-'),
                'created_at' => $alert['created_at'] ?? ($alert['raisedAt'] ?? '-'),
            ];
        })->all();
        if (empty($alerts)) {
            return response()->json(['data' => [], 'debug' => 'No user alerts found after filter'], 200);
        }
        return response()->json(['data' => $alerts], 200);
    }
}
