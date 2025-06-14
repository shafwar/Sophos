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
        $metrics = $this->sophosApi->getMetrics() ?? $this->getDefaultMetrics();

        if ($user->role === 'user') {
            return view('dashboard', ['riskData' => $metrics]);
        }
        if ($user->role === 'admin') {
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
            $sophosService = app(SophosApiService::class);
            $alerts = $sophosService->getAllAlerts();

            Log::info('Alerts received:', ['count' => count($alerts)]);

            // Daftar bulan Jan–Des
            $allMonths = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

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
                    ['month' => 'Jan', 'highRisk' => 2, 'mediumRisk' => 1, 'lowRisk' => 3],
                    ['month' => 'Feb', 'highRisk' => 1, 'mediumRisk' => 2, 'lowRisk' => 2],
                    ['month' => 'Mar', 'highRisk' => 0, 'mediumRisk' => 1, 'lowRisk' => 4],
                    ['month' => 'Apr', 'highRisk' => 3, 'mediumRisk' => 0, 'lowRisk' => 1],
                    ['month' => 'May', 'highRisk' => 1, 'mediumRisk' => 1, 'lowRisk' => 2],
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
            Log::error('Error fetching traffic risk data: ' . $e->getMessage());
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

    /**
     * Get Monthly Details - Akses: Admin & User
     * Mendapatkan detail data bulanan
     */
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
        // Jika admin, tampilkan semua log. Jika user, hanya log milik sendiri.
        if ($user->role === 'admin') {
            $totalUsers = \App\Models\User::count();
            $activeUsers = \App\Models\User::count();
            $todaysLogins = DB::table('activity_logs')
                ->where('activity', 'like', '%login%')
                ->whereDate('created_at', now()->toDateString())
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
                ->where('activity', 'like', '%login%')
                ->where('user_id', $user->id)
                ->whereDate('created_at', now()->toDateString())
                ->count();
            $logs = DB::table('activity_logs')
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();
        }
        return view('activity_log', compact('logs', 'totalUsers', 'activeUsers', 'todaysLogins'));
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

        // Filter berdasarkan nama user (person['name']) - longgar, gunakan str_contains
        $userRisks = collect($allRisks)->filter(function($risk) use ($name) {
            $personName = isset($risk['person']['name']) ? strtolower($risk['person']['name']) : null;
            $userName = strtolower(trim($name));
            return $personName && $userName && str_contains($personName, $userName);
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

        // Filter berdasarkan nama user (str_contains)
        $userRisks = collect($allRisks)->filter(function($risk) use ($name) {
            $personName = isset($risk['person']['name']) ? strtolower($risk['person']['name']) : null;
            $userName = strtolower(trim($name));
            return $personName && $userName && str_contains($personName, $userName);
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
}
