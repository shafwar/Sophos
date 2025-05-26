@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- User Statistics -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold">User Statistics</div>
                <div class="card-body">
                    <div class="mb-2">
                        <div class="bg-success text-white rounded p-2 mb-2">Total Users
                            <div class="fs-3 fw-bold">{{ $totalUsers }}</div>
                        </div>
                        <div class="bg-info text-white rounded p-2 mb-2">Active Users
                            <div class="fs-3 fw-bold">{{ $activeUsers }}</div>
                        </div>
                        <div class="bg-primary text-white rounded p-2">Today's Logins
                            <div class="fs-3 fw-bold">{{ $todaysLogins }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Recent Activities -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white fw-bold">Recent Activities</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->user_name }}</td>
                                    <td>{{ $log->activity }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 