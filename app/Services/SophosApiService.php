<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $this->apiHost = 'api-us01.central.sophos.com';
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

            Log::info('Auth response:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $this->accessToken = $response->json('access_token');
                Log::info('Successfully obtained access token');
                return true;
            }

            Log::error('Authentication failed:', ['response' => $response->json()]);
            return false;
        } catch (\Exception $e) {
            Log::error('Authentication exception:', ['message' => $e->getMessage()]);
            return false;
        }
    }

    private function getAlerts()
    {
        try {
            if (!$this->authenticate()) {
                Log::error('Failed to authenticate');
                return null;
            }

            // Get whoami
            $whoamiResponse = Http::withToken($this->accessToken)
                ->withHeaders([
                    'Accept' => 'application/json'
                ])
                ->get($this->baseUrl . '/whoami/v1');

            if (!$whoamiResponse->successful()) {
                Log::error('WhoAmI failed:', ['response' => $whoamiResponse->json()]);
                return null;
            }

            $this->tenantId = $whoamiResponse->json('id');
            
            // Build correct URL for alerts
            $alertsUrl = 'https://' . $this->apiHost . '/common/v1/alerts';
            
            Log::info('Requesting alerts from:', ['url' => $alertsUrl]);

            $response = Http::withToken($this->accessToken)
                ->withHeaders([
                    'X-Tenant-ID' => $this->tenantId,
                    'Accept' => 'application/json'
                ])
                ->get($alertsUrl);

            if ($response->successful()) {
                $alerts = $response->json('items');
                Log::info('Successfully retrieved alerts', ['count' => count($alerts ?? [])]);
                return $alerts;
            }

            Log::error('Failed to get alerts:', ['response' => $response->json()]);
            return null;

        } catch (\Exception $e) {
            Log::error('Error getting alerts:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function getMetrics()
    {
        try {
            $alerts = $this->getAlerts();
            
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

            // Add weekly changes
            $metrics['weeklyChange'] = [
                'total' => '37% this week',
                'high' => '21% this week',
                'medium' => '15% this week',
                'low' => '0% this week'
            ];

            Log::info('Metrics calculated successfully', $metrics);
            return $metrics;

        } catch (\Exception $e) {
            Log::error('Error calculating metrics:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function getAlertsByCategory($category)
    {
        try {
            $alerts = $this->getAlerts();
            
            if (!$alerts) {
                return null;
            }

            // Filter alerts based on category/severity
            $filteredAlerts = collect($alerts)->filter(function ($alert) use ($category) {
                if (strtolower($category) === 'all risk') {
                    return true;
                }
                return strtolower($alert['severity'] ?? '') === strtolower(str_replace(' risk', '', $category));
            })->values()->all();

            Log::info('Filtered alerts by category', [
                'category' => $category,
                'count' => count($filteredAlerts)
            ]);

            return $filteredAlerts;

        } catch (\Exception $e) {
            Log::error('Error getting alerts by category:', ['message' => $e->getMessage()]);
            return null;
        }
    }
}