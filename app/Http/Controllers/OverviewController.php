<?php

namespace App\Http\Controllers;

use App\Services\SophosApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OverviewController extends Controller
{
    protected $sophosApiService;

    public function __construct(SophosApiService $sophosApiService)
    {
        $this->sophosApiService = $sophosApiService;
    }

    public function index()
    {
        try {
            Log::info('Mencoba mengambil data Sophos');

            $usersData = $this->sophosApiService->getUsers();
            Log::debug('Response dari Sophos API:', ['data' => json_encode($usersData)]);

            if (!$usersData) {
                Log::warning('SophosAPI mengembalikan null');
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
            Log::error('Error pada OverviewController: ' . $e->getMessage());

            $usersData = [
                'users_list' => [
                    ['name' => 'DESKTOP-3J5K2LM', 'email' => 'user1@example.com', 'last_online' => '3 days ago', 'devices' => '1', 'logins' => 'User1', 'groups' => 'Group A', 'health_status' => 'good'],
                    ['name' => 'DESKPC-HR125', 'email' => 'user2@example.com', 'last_online' => 'Jan 25, 2025', 'devices' => '1', 'logins' => 'User2', 'groups' => 'Group B', 'health_status' => 'warning']
                ],
                'all' => 640,
                'active' => 599,
                'inactive_2weeks' => 37,
                'inactive_2months' => 0,
                'no_devices' => 4
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
}
