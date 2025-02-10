<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SophosApiService
{
    private $clientId;
    private $clientSecret;
    private $baseUrl;
    private $accessToken;
    private $tenantId;
    private $apiHost;
    private const CACHE_DURATION = 300; // 5 minutes
    private const MAX_RETRIES = 3;

    public function __construct()
    {
        $this->clientId = config('sophos.client_id');
        $this->clientSecret = config('sophos.client_secret');
        $this->baseUrl = 'https://api.central.sophos.com';
        $this->apiHost = config('sophos.api_host', 'api-us01.central.sophos.com');

        // Get cached credentials if available
        $this->accessToken = Cache::get('sophos_access_token');
        $this->tenantId = Cache::get('sophos_tenant_id');
    }

    private function authenticate()
    {
        try {
            // Check if we already have valid credentials
            if ($this->accessToken && $this->tenantId) {
                return true;
            }

            Log::info('Starting authentication process');

            $response = Http::timeout(10)->withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post('https://id.sophos.com/api/v2/oauth2/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => 'token'
                ]);

            if ($response->successful()) {
                $this->accessToken = $response->json('access_token');

                // Get whoami information
                $whoamiResponse = Http::timeout(10)
                    ->withToken($this->accessToken)
                    ->withHeaders(['Accept' => 'application/json'])
                    ->get($this->baseUrl . '/whoami/v1');

                if ($whoamiResponse->successful()) {
                    $this->tenantId = $whoamiResponse->json('id');

                    // Cache the credentials
                    Cache::put('sophos_access_token', $this->accessToken, now()->addMinutes(55));
                    Cache::put('sophos_tenant_id', $this->tenantId, now()->addMinutes(55));

                    return true;
                }
            }

            Log::error('Authentication failed:', ['response' => $response->json()]);
            return false;
        } catch (\Exception $e) {
            Log::error('Authentication exception:', ['message' => $e->getMessage()]);
            return false;
        }
    }

    private function makeApiRequest($endpoint, $method = 'GET', $params = [])
    {
        $retries = 0;

        do {
            try {
                if (!$this->authenticate()) {
                    return null;
                }

                $url = "https://{$this->apiHost}{$endpoint}";

                $request = Http::timeout(30)
                    ->withToken($this->accessToken)
                    ->withHeaders([
                        'X-Tenant-ID' => $this->tenantId,
                        'Accept' => 'application/json'
                    ]);

                $response = $method === 'GET'
                    ? $request->get($url, $params)
                    : $request->post($url, $params);

                if ($response->successful()) {
                    return $response->json();
                }

                // Handle rate limiting
                if ($response->status() === 429) {
                    $retryAfter = (int) $response->header('Retry-After', 5);
                    Log::warning("Rate limited, waiting {$retryAfter} seconds");
                    sleep($retryAfter);
                    continue;
                }

                Log::error("API request failed for {$endpoint}:", [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);

                return null;

            } catch (\Exception $e) {
                Log::error("API request exception for {$endpoint}:", ['message' => $e->getMessage()]);
                if (++$retries >= self::MAX_RETRIES) {
                    return null;
                }
                sleep(1); // Wait before retry
            }
        } while ($retries < self::MAX_RETRIES);

        return null;
    }

    public function getAllAlerts()
    {
        $cacheKey = 'sophos_all_alerts';

        try {
            // Try to get from cache first
            if ($cachedAlerts = Cache::get($cacheKey)) {
                return $cachedAlerts;
            }

            if (!$this->authenticate()) {
                return null;
            }

            $allAlerts = [];
            $endpoints = [
                '/common/v1/alerts',
                '/siem/v1/events',
                '/endpoint/v1/alerts',
                '/xdr/v1/alerts'
            ];

            foreach ($endpoints as $endpoint) {
                Log::info("Fetching alerts from endpoint: {$endpoint}");
                $response = $this->makeApiRequest($endpoint);

                if ($response && isset($response['items'])) {
                    $items = $response['items'];

                    // Transform SIEM events if necessary
                    if ($endpoint === '/siem/v1/events') {
                        $items = array_map(function ($event) {
                            return $this->transformSiemEvent($event);
                        }, $items);
                    }

                    $allAlerts = array_merge($allAlerts, $items);
                }
            }

            // Cache the results
            if (!empty($allAlerts)) {
                Cache::put($cacheKey, $allAlerts, self::CACHE_DURATION);
            }

            Log::info('Total alerts fetched:', ['count' => count($allAlerts)]);
            return $allAlerts;

        } catch (\Exception $e) {
            Log::error('Error in getAllAlerts:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function transformSiemEvent($event)
    {
        $description = [];

        if (!empty($event['name'])) {
            $description[] = $event['name'];
        }
        if (!empty($event['source'])) {
            $description[] = "Source: " . $event['source'];
        }
        if (!empty($event['source_info']['ip'])) {
            $description[] = "Source IP: " . $event['source_info']['ip'];
        }
        if (!empty($event['location'])) {
            $description[] = "Location: " . $event['location'];
        }
        if (!empty($event['endpoint_type'])) {
            $description[] = "Endpoint Type: " . $event['endpoint_type'];
        }

        return [
            'id' => $event['id'] ?? null,
            'severity' => $event['severity'] ?? 'low',
            'category' => $event['type'] ?? 'Event',
            'description' => implode("\n", $description),
            'raisedAt' => $event['created_at'] ?? null,
            'type' => 'SIEM Event',
            'location' => $event['location'] ?? null,
            'name' => $event['name'] ?? null,
            'source' => $event['source'] ?? null,
            'source_ip' => $event['source_info']['ip'] ?? null,
            'endpoint_type' => $event['endpoint_type'] ?? null,
            'endpoint_id' => $event['endpoint_id'] ?? null,
            'user_id' => $event['user_id'] ?? null,
            'group' => $event['group'] ?? null
        ];
    }

    public function getMetrics()
    {
        try {
            $alerts = $this->getAllAlerts();

            if (!$alerts) {
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

            // Initialize metrics
            $metrics = [
                'total' => count($alerts),
                'high' => 0,
                'medium' => 0,
                'low' => 0
            ];

            // Count alerts by severity
            foreach ($alerts as $alert) {
                $severity = strtolower($alert['severity'] ?? '');
                if (isset($metrics[$severity])) {
                    $metrics[$severity]++;
                }
            }

            // Calculate weekly changes
            $metrics['weeklyChange'] = [
                'total' => $this->calculateWeeklyChange($alerts, 'total'),
                'high' => $this->calculateWeeklyChange($alerts, 'high'),
                'medium' => $this->calculateWeeklyChange($alerts, 'medium'),
                'low' => $this->calculateWeeklyChange($alerts, 'low')
            ];

            Log::info('Metrics calculated successfully', $metrics);
            return $metrics;

        } catch (\Exception $e) {
            Log::error('Error calculating metrics:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function calculateWeeklyChange($alerts, $severity)
    {
        try {
            // Get current week's alerts
            $currentWeek = collect($alerts)->filter(function ($alert) {
                $raisedAt = $alert['raisedAt'] ?? null;
                if (!$raisedAt) return false;
                $alertDate = strtotime($raisedAt);
                return $alertDate >= strtotime('-7 days');
            });

            // Get previous week's alerts
            $previousWeek = collect($alerts)->filter(function ($alert) {
                $raisedAt = $alert['raisedAt'] ?? null;
                if (!$raisedAt) return false;
                $alertDate = strtotime($raisedAt);
                return $alertDate >= strtotime('-14 days') && $alertDate < strtotime('-7 days');
            });

            if ($severity === 'total') {
                $currentCount = $currentWeek->count();
                $previousCount = $previousWeek->count();
            } else {
                $currentCount = $currentWeek->filter(function ($alert) use ($severity) {
                    return strtolower($alert['severity'] ?? '') === $severity;
                })->count();

                $previousCount = $previousWeek->filter(function ($alert) use ($severity) {
                    return strtolower($alert['severity'] ?? '') === $severity;
                })->count();
            }

            if ($previousCount === 0) {
                return $currentCount > 0 ? '100% this week' : '0% this week';
            }

            $percentageChange = round((($currentCount - $previousCount) / $previousCount) * 100);
            return "{$percentageChange}% this week";

        } catch (\Exception $e) {
            Log::error('Error calculating weekly change:', ['message' => $e->getMessage()]);
            return '0% this week';
        }
    }

    public function getAlertsByCategory($category)
    {
        try {
            $alerts = $this->getAllAlerts();

            if (!$alerts) {
                return null;
            }

            // Filter alerts based on category/severity
            return collect($alerts)->filter(function ($alert) use ($category) {
                if (strtolower($category) === 'all risk') {
                    return true;
                }
                return strtolower($alert['severity'] ?? '') === strtolower(str_replace(' risk', '', $category));
            })->values()->all();

        } catch (\Exception $e) {
            Log::error('Error getting alerts by category:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function getUsers()
    {
        try {
            // Get endpoints data using makeApiRequest
            $response = $this->makeApiRequest('/endpoint/v1/endpoints');

            if (!$response || !isset($response['items'])) {
                Log::error('Failed to get endpoints data or invalid response format');
                return null;
            }

            $users = [];
            $stats = [
                'all' => 0,
                'active' => 0,
                'inactive_2weeks' => 0,
                'inactive_2months' => 0,
                'no_devices' => 0
            ];

            foreach ($response['items'] as $endpoint) {
                $lastSeen = $endpoint['lastSeenAt'] ?? null;
                $status = $this->calculateUserStatus($lastSeen);

                $user = [
                    'name' => $endpoint['hostname'] ?? 'Unknown',
                    'email' => $endpoint['associatedPerson']['viaLogin'] ?? 'N/A',
                    'last_online' => $lastSeen ? date('Y-m-d H:i:s', strtotime($lastSeen)) : 'Never',
                    'devices' => $endpoint['id'] ?? '',
                    'logins' => $endpoint['associatedPerson']['name'] ?? 'N/A',
                    'groups' => $endpoint['group'] ?? 'N/A',
                    'health_status' => $endpoint['health']['overall'] ?? 'unknown'
                ];

                $users[] = $user;

                // Update statistics
                $stats['all']++;
                switch ($status) {
                    case 'active':
                        $stats['active']++;
                        break;
                    case 'inactive_2weeks':
                        $stats['inactive_2weeks']++;
                        break;
                    case 'inactive_2months':
                        $stats['inactive_2months']++;
                        break;
                }
            }

            // Calculate no_devices
            $stats['no_devices'] = count($users) === 0 ? $stats['all'] : 0;

            return [
                'users_list' => $users,
                'all' => $stats['all'],
                'active' => $stats['active'],
                'inactive_2weeks' => $stats['inactive_2weeks'],
                'inactive_2months' => $stats['inactive_2months'],
                'no_devices' => $stats['no_devices']
            ];

        } catch (\Exception $e) {
            Log::error('Error getting users from Sophos API:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    private function calculateUserStatus($lastSeen)
    {
        if (!$lastSeen) {
            return 'inactive_2months';
        }

        $lastSeenDate = strtotime($lastSeen);
        $twoWeeksAgo = strtotime('-2 weeks');
        $twoMonthsAgo = strtotime('-2 months');

        if ($lastSeenDate > $twoWeeksAgo) {
            return 'active';
        } elseif ($lastSeenDate > $twoMonthsAgo) {
            return 'inactive_2weeks';
        } else {
            return 'inactive_2months';
        }
    }

    public function getComputers()
    {
        try {
        // Ambil data dari API Sophos
        $response = Http::withHeaders([
            'X-API-KEY' => config('sophos.api_key'),
            // Tambahkan header lain yang diperlukan
        ])->get(config('sophos.api_url') . '/endpoints/computers');

        if ($response->successful()) {
            $data = $response->json();

            // Transform response sesuai kebutuhan
            return [
                'computers_list' => collect($data['items'] ?? [])->map(function ($computer) {
                    return [
                        'name' => $computer['hostname'] ?? 'Unknown',
                        'online' => $this->formatLastSeen($computer['lastSeenAt'] ?? null),
                        'last_user' => $computer['lastUser'] ?? 'N/A',
                        'real_time_scan' => $computer['realTimeScan'] ?? 'No',
                        'last_update' => $this->formatLastSeen($computer['lastUpdateTime'] ?? null),
                        'last_scan' => $this->formatLastSeen($computer['lastScanTime'] ?? null),
                        'health_status' => $computer['health']['overall'] ?? 'unknown',
                        'group' => $computer['group'] ?? 'N/A',
                        'agent_installed' => $computer['sophos']['isInstalled'] ? 'Yes' : 'No'
                    ];
                })->all(),
                'stats' => [
                    'all' => 640, // Sesuaikan dengan perhitungan aktual
                    'active' => 599,
                    'inactive_2weeks' => 37,
                    'inactive_2months' => 0,
                    'not_protected' => 4
                ]
            ];
        }

        Log::error('Failed to get computers from Sophos API', [
            'status' => $response->status(),
            'response' => $response->json()
        ]);

        return null;

        } catch (\Exception $e) {
        Log::error('Error getting computers from Sophos API:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return null;
        }
    }

    private function formatLastSeen($timestamp)
    {
        if (!$timestamp) return 'Never';

        try {
        $date = Carbon::parse($timestamp);
        $now = Carbon::now();

        if ($date->diffInMinutes($now) < 60) {
            return $date->diffInMinutes($now) . ' minutes ago';
        } elseif ($date->diffInHours($now) < 24) {
            return $date->diffInHours($now) . ' hours ago';
        } elseif ($date->diffInDays($now) < 7) {
            return $date->diffInDays($now) . ' days ago';
        } else {
            return $date->format('Y-m-d H:i:s');
        }
        } catch (\Exception $e) {
        return 'Never';
        }
    }
}
