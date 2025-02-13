@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if($user->profile_picture)
                        <img src="{{ Storage::url($user->profile_picture) }}" 
                            alt="Profile Picture"
                            class="rounded-circle img-fluid mb-3" 
                            style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img src="{{ asset('images/default-avatar.png') }}" 
                            alt="Default Profile"
                            class="rounded-circle img-fluid mb-3" 
                            style="width: 150px; height: 150px; object-fit: cover;">
                     @endif
                    
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    <form action="{{ route('profile.upload-photo') }}" method="POST" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control form-control-sm" accept="image/*">
                            <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                        </div>
                    </form>
                    
                    @if($user->profile_picture)
                        <form action="{{ route('profile.delete-photo') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete Photo</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <label class="form-label">Full Name</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <label class="form-label">Email</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-3">
                                <label class="form-label">New Password</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection