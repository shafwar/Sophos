<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mendefinisikan data dropdownMenus
        $dropdownMenus = [
            'dashboards' => [
                ['icon' => 'monitor', 'title' => 'Overview Dashboard', 'description' => 'System-wide overview and stats'],
                ['icon' => 'shield', 'title' => 'Security Dashboard', 'description' => 'Threat monitoring and analysis'],
                ['icon' => 'server', 'title' => 'Network Dashboard', 'description' => 'Network traffic and performance'],
            ],
            'products' => [
                ['icon' => 'shield', 'title' => 'Endpoint Protection', 'description' => 'Secure your devices'],
                ['icon' => 'server', 'title' => 'Firewall', 'description' => 'Network security'],
                ['icon' => 'terminal', 'title' => 'Cloud Security', 'description' => 'Cloud workload protection'],
            ],
            'threat' => [
                ['icon' => 'alert-circle', 'title' => 'Threat Hunting', 'description' => 'Advanced threat detection'],
                ['icon' => 'file-text', 'title' => 'Analysis Reports', 'description' => 'Detailed security analysis'],
                ['icon' => 'shield', 'title' => 'Response Center', 'description' => 'Incident response management'],
            ],
        ];

        // Mengirimkan data ke view dashboard
        return view('dashboard', compact('dropdownMenus'));
    }
}
