<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class SophosApiService
{
    private $clientId;
    private $clientSecret;
    private $baseUrl;
    private $accessToken;
    private $tenantId;
    private $apiHost;

    public function __construct()
    {
        $this->clientId = config('sophos.client_id');
        $this->clientSecret = config('sophos.client_secret');
        $this->baseUrl = 'https://api.central.sophos.com';
        $this->apiHost = config('sophos.api_host', 'api-us01.central.sophos.com');
    }

    private function authenticate()
    {
        try {
            Log::info('Starting authentication process');
            
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post('https://id.sophos.com/api/v2/oauth2/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'scope' => 'token'
                ]);

            if ($response->successful()) {
                $this->accessToken = $response->json('access_token');
                return true;
            }

            Log::error('Authentication failed:', ['response' => $response->json()]);
            return false;
        } catch (\Exception $e) {
            Log::error('Authentication exception:', ['message' => $e->getMessage()]);
            return false;
        }
    }

    private function initializeApiAccess()
    {
        if (!$this->authenticate()) {
            Log::error('Failed to authenticate');
            return false;
        }

        // Get whoami information
        $whoamiResponse = Http::withToken($this->accessToken)
            ->withHeaders(['Accept' => 'application/json'])
            ->get($this->baseUrl . '/whoami/v1');

        if (!$whoamiResponse->successful()) {
            Log::error('WhoAmI failed:', ['response' => $whoamiResponse->json()]);
            return false;
        }

        $this->tenantId = $whoamiResponse->json('id');
        return true;
    }

    private function makeApiRequest($endpoint, $method = 'GET', $params = [])
    {
        try {
            $url = "https://{$this->apiHost}{$endpoint}";
            
            $request = Http::withToken($this->accessToken)
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

            Log::error("API request failed for {$endpoint}:", ['response' => $response->json()]);
            return null;
        } catch (\Exception $e) {
            Log::error("API request exception for {$endpoint}:", ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function getAllAlerts()
    {
        if (!$this->initializeApiAccess()) {
            return null;
        }

        $allAlerts = [];

        // Fetch alerts from Common API
        $commonAlerts = $this->makeApiRequest('/common/v1/alerts');
        if ($commonAlerts && isset($commonAlerts['items'])) {
            $allAlerts = array_merge($allAlerts, $commonAlerts['items']);
        }

        // Fetch events from SIEM Integration API
        $siemEvents = $this->makeApiRequest('/siem/v1/events');
        if ($siemEvents && isset($siemEvents['items'])) {
            // Transform SIEM events to match alert format
            $transformedEvents = array_map(function ($event) {
                return [
                    'id' => $event['id'] ?? null,
                    'severity' => $event['severity'] ?? 'low',
                    'category' => $event['type'] ?? 'Event',
                    'description' => $event['description'] ?? $event['type'] ?? '',
                    'raisedAt' => $event['created_at'] ?? null,
                    'type' => 'SIEM Event'
                ];
            }, $siemEvents['items']);
            $allAlerts = array_merge($allAlerts, $transformedEvents);
        }

        // Fetch from Endpoint API
        $endpointAlerts = $this->makeApiRequest('/endpoint/v1/alerts');
        if ($endpointAlerts && isset($endpointAlerts['items'])) {
            $allAlerts = array_merge($allAlerts, $endpointAlerts['items']);
        }

        // Fetch from XDR Query API
        $xdrAlerts = $this->makeApiRequest('/xdr/v1/alerts');
        if ($xdrAlerts && isset($xdrAlerts['items'])) {
            $allAlerts = array_merge($allAlerts, $xdrAlerts['items']);
        }

        Log::info('Total alerts fetched:', ['count' => count($allAlerts)]);
        return $allAlerts;
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

            // Calculate weekly changes (you might want to implement actual calculation logic here)
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