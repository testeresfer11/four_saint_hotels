@extends('admin.layouts.app')

@section('title', 'Push Notifications')

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Push Notifications</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Push Notifications</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body p-0">
                <div class="d-flex px-3 py-3 justify-content-between align-items-center">
                    <h4 class="card-title m-0">Push Notifications Management</h4>
                    <a href="{{ route('admin.pushnotification.add') }}">
                        <button type="button" class="btn btn-primary btn-md">
                            <i class="fa-solid fa-plus"></i> Add Push Notification
                        </button>
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Receiver</th>
                                <th>Title</th>
                                <th>Body</th>
                                <th>Image</th>
                                <th>Type</th>
                                <th>Notification Type</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $index => $notification)
                                <tr data-id="{{ $notification->id }}">
                                    <td>{{ $loop->iteration + ($notifications->currentPage() - 1) * $notifications->perPage() }}</td>
                                    <td>
                                        @php
                                            $receiver = \App\Models\User::find($notification->receiver_id);
                                        @endphp
                                        {{ $receiver ? $receiver->full_name : 'All / Multiple Users' }}
                                    </td>
                                    <td>{{ $notification->title }}</td>
                                    <td>{{ Str::limit($notification->body, 50) }}</td>
                                    <td>
                                        @if($notification->image)
                                            <img src="{{ $notification->image }}" alt="Notification Image" style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                    </td>
                                    <td>{{ $notification->type ?? '-' }}</td>
                                    <td>{{ $notification->notification_type ?? '-' }}</td>
                                    <td>{{ $notification->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.pushnotification.edit', $notification->id) }}" class="text-success">
                                           <i class="mdi mdi-pencil"></i>
                                        </a>
                                     
                                           <span class="menu-icon">
                                        <a href="#" title="Delete" class="text-danger deleteNotification" data-id="{{ $notification->id }}"><i class="mdi mdi-delete"></i></a>
                                      </span> 
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No push notifications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="custom_pagination p-3">
                    {{ $notifications->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).on('click', '.deleteNotification', function() {
    var id = $(this).data('id');
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete this notification?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: '#B46326',
        cancelButtonColor: '#fff',
        confirmButtonText: "Yes, delete it!",
        customClass: {
            cancelButton: 'swal-cancel-custom'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/admin/pushnotification/delete/" + id,
                type: "GET", 
                success: function(response) {
                    if (response.status == "success") {
                        $(`tr[data-id="${id}"]`).remove();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });
});
</script>
@endsection
