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
                        <div class="bg-success text-white rounded p-2 mb-2" id="showUserList" style="cursor:pointer;">Total Users
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
<!-- Modal List User -->
<div class="modal fade" id="userListModal" tabindex="-1" aria-labelledby="userListModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userListModalLabel">Daftar User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Email</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="userListTableBody">
            <!-- Data user akan diisi via AJAX -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script>
$(function() {
    $('#showUserList').on('click', function() {
        $('#userListModal').modal('show');
        // Ambil data user via AJAX
        $.get("{{ route('admin.user-list') }}", function(data) {
            let html = '';
            data.forEach(function(user) {
                html += `<tr>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>`;
                if(user.role === 'admin') {
                    html += `<button class='btn btn-secondary btn-sm' disabled>Admin</button>`;
                } else {
                    html += `<form method="POST" action="/admin/delete-user/${user.id}" class="delete-user-form" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini?')">Hapus</button>
                        </form>`;
                }
                html += `</td></tr>`;
            });
            $('#userListTableBody').html(html);
        });
    });
    // Optional: handle hapus via AJAX agar tidak reload
    $(document).on('submit', '.delete-user-form', function(e) {
        e.preventDefault();
        if(confirm('Yakin hapus user ini?')) {
            let form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function() {
                    form.closest('tr').remove();
                },
                error: function(xhr) {
                    if(xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.message) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert('Gagal menghapus user');
                    }
                }
            });
        }
    });
});
</script>
@endpush
@endsection 