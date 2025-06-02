@extends('layouts.app')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Pending User Approvals</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($pendingUsers->isEmpty())
        <div class="alert alert-info">Tidak ada user yang menunggu persetujuan.</div>
    @else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingUsers as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td><span class="badge bg-warning text-dark">Pending</span></td>
                <td>
                    <form method="POST" action="{{ route('admin.approve-user', $user->id) }}" style="display:inline-block">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.decline-user', $user->id) }}" style="display:inline-block" onsubmit="return confirm('Tolak user ini?');">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm ms-1">Decline</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection 