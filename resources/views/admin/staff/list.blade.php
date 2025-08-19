@extends('admin.layouts.app')
@section('title', 'Staff')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Staff</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Staff</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row ">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body p-0">
          <div class="d-flex justify-content-between flex-column flex-xl-row row-gap-3 px-3 py-3 align-items-xl-center align-items-start">
            <h4 class="card-title m-0">Staff Management</h4>
            <div class="d-flex align-items-center justify-content-between">
              <div class="admin-filters mr-2">
                <x-filter />
              </div>
              @can('staff-add')
                <a href="{{route('admin.staff.add')}}" class="add-btn">
                  <button type="button" class="btn btn-primary btn-md">
                    <span class="menu-icon"><i class="fa-solid fa-plus"></i></span>
                    <span class="menu-text">Add Staff</span>
                  </button>
                </a>
              @endcan
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Profile </th>
                  <th> Staff Id </th>
                  <th> Name </th>
                  <th> Email </th>
                  <th> Role </th>
                  @can('staff-edit')
                    <th> Status </th>
                  @endcan
                  @canany(['staff-edit','staff-view','staff-delete'])
                    <th> Action </th>
                  @endcanany
                </tr>
              </thead>
              <tbody>
                @forelse ($users as $user)
                  <tr data-id="{{$user->id}}">
                    <td class="py-1">
                      <img src="{{userImageById($user->id)}}"
                      alt="User profile picture">
                    </td>
                    <td>{{$user->uuid}}</td>
                    <td> {{$user->full_name}} </td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->role ? ucfirst($user->role->name) : 'N/A'}}</td>
                    @can('staff-edit')
                      <td> 
                        <div class="toggle-user dark-toggle">
                          <input type="checkbox" name="is_active" data-id="{{$user->id}}" class="switch" @if ($user->status == 1) checked @endif data-value="{{$user->status}}">
                        </div> 
                      </td>
                    @endcan
                    @canany(['staff-edit','staff-view','staff-delete'])
                      <td> 
                        @can('staff-view')
                        <span class="menu-icon">
                          <a href="{{route('admin.staff.view',['id' => $user->id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                        </span>&nbsp;&nbsp;&nbsp;
                        @endcan
                        @can('staff-edit')
                        <span class="menu-icon">
                          <a href="{{route('admin.staff.edit',['id' => $user->id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                        </span>&nbsp;&nbsp;
                        @endcan
                        @can('staff-delete')
                        <span class="menu-icon">
                          <a href="#" title="Delete" class="text-danger deleteStaff" data-id="{{$user->id}}"><i class="mdi mdi-delete"></i></a>
                        </span> 
                        @endcan
                      </td>
                    @endcanany
                  </tr>
                @empty
                    <tr>
                      <td colspan="7" class="no-record"> <div class="col-12 text-center">No record found </div></td>
                    </tr>
                @endforelse
              </tbody>
            </table>
          </div>
            <div class="custom_pagination">
             {{ $users->links('pagination::bootstrap-4') }}
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script>
    $(document).on('click', '.deleteStaff', function() {
    var id = $(this).attr('data-id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the staff?",
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
                  url: "/staff/delete/" + id,
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

  $('.switch').on('click', function() {
    var status = $(this).data('value');
    var action = (status == 1) ? 0 : 1;
    var id = $(this).data('id');

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to change the status of the staff?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ea57c",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, mark as status"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/staff/changeStatus",
                type: "GET",
                data: { id: id, status: action },
                success: function(response) {
                    if (response.status == "success") {
                        $('.switch[data-id="' + id + '"]').data('value',!action);
                        toastr.success(response.message);
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
