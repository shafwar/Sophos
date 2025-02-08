<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

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
}