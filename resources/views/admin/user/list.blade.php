@extends('admin.layouts.app')
@section('title', 'Users')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Users</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Users</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body p-0">
          <div class="d-flex justify-content-between flex-column row-gap-3 flex-md-row px-3 py-3 align-items-md-center align-items-start">
            <h4 class="card-title m-0">User Management</h4>
            <div class="d-flex align-items-center justify-content-between">
              <div class="admin-filters mr-2">
                <x-filter />
              </div>
               @can('user-add')
              <a href="{{route('admin.user.add')}}" class="add-btn">
                <button type="button" class="btn btn-primary btn-md">
                  <span class="menu-icon"><i class="fa-solid fa-plus"></i></span>
                    <span class="menu-text"> Add User</span>
                </button></a>
              @endcan
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Profile </th>
                  <th> Name </th>
                  <th> Email </th>
                  <th> Status </th>
                  <th> Action </th>
                </tr>
              </thead>
               @can('user-list')
              <tbody>
                @forelse ($users as $user)
                  <tr data-id="{{$user->id}}">
                    <td class="py-1">
                      <img src="{{userImageById($user->id)}}" onerror="this.src = '{{ asset('admin/images/faces/face15.jpg') }}'"
                      alt="User profile picture">
                    </td>
                    <td> {{$user->full_name}} </td>
                    <td>{{$user->email}}</td>
                    
                    
                    <td> 
                       @can('user-change-status')
                      <div class="toggle-user dark-toggle">
                      <input type="checkbox" name="is_active" data-id="{{$user->id}}" class="switch" @if ($user->status == 1) checked @endif data-value="{{$user->status}}">
                       @endcan
                    </div>
                     </td>
                    <td> 
                   @can('user-view')
                      <span class="menu-icon">
                        <a href="{{route('admin.user.view',['id' => $user->id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp;&nbsp;
                      @endcan
                      @can('user-edit')
                      <span class="menu-icon">
                        <a href="{{route('admin.user.edit',['id' => $user->id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      @endcan
                       @can('user-delete')
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteUser" data-id="{{$user->id}}"><i class="mdi mdi-delete"></i></a>
                      </span> 
                      @endcan
                    </td>
                  </tr>
                @empty
                    <tr>
                      <td colspan="6" class="no-record"> <center>No record found </center></td>
                    </tr>
                @endforelse
              </tbody>
              @endcan
            </table>
          </div>
            <div class="custom_pagination">
              {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script>
  $('.deleteUser').on('click', function() {
    var user_id = $(this).data('id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the User?",
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
                  url: "/admin/user/delete/" + user_id,
                  type: "GET", 
                  success: function(response) {
                    if (response.status == "success") {
                       $(`tr[data-id="${user_id}"]`).remove();
                        toastr.success(response.message);
                      } else {
                        toastr.error(response.message);
                      }
                  }
              });
          }
      });
  });

  $('.changeUserSubscription').on('click', function() {
    var user_id = $(this).data('id');
      Swal.fire({
          title: "Are you sure?",
          text: "Do you want to change the user subscription from Basic to Premium?",
          icon: "info",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, proceed it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/user/changeSubscription/" + user_id,
                  type: "GET", 
                  success: function(response) {
                    if (response.status == "success") {
                      toastr.success(response.message);
                       setTimeout(() => {
                        location.reload();
                       }, 1000);
                      } else {
                        toastr.error(response.message);
                      }
                  }
              });
          }
      });
  });

  $('.switch').on('click', function() {
    var status = $(this).data('value');
    var action = (status == 1) ? 0 : 1;
    var id = $(this).data('id');

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to change the status of the user?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ea57c",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, mark as status"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/admin/user/changeStatus",
                type: "GET",
                data: { id: id, status: action },
                success: function(response) {
                    if (response.status == "success") {
                        toastr.success(response.message);
                        $('.switch[data-id="' + id + '"]').data('value',!action);
                    } else {
                        $('.switch[data-id="' + id + '"]').data('value',action);
                        toastr.error(response.message);
                    }
                },
                error: function(error) {
                    console.log('error', error);
                }
            });
        } else {
          var switchToToggle = $('.switch[data-id="' + id + '"]');
          switchToToggle.prop('checked', !switchToToggle.prop('checked'));
        }
    });
  });

</script>

@stop
