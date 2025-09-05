@extends('admin.layouts.app')
@section('title', 'Roles')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Roles</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Roles</li>
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
            <h4 class="card-title m-0">Role Management</h4>

             @can('role-add')
              <a href="{{ route('admin.role.add') }}">
                  <button type="button" class="btn btn-primary btn-md">
                      <span class="menu-icon"><i class="fa-solid fa-plus"></i></span>
                      <span class="menu-text"> Add Role</span>
                  </button>
              </a>
          @endcan

          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Sr. No.</th>
                  <th> Name </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($roles as $key => $role)
                  <tr data-id="{{$role->id}}">
                    <td>{{++$key}}</td>
                    <td> {{ucfirst($role->name)}} </td>
                    <td> 
                        @can('role-edit')
                            <span class="menu-icon">
                                <a href="{{ route('admin.role.edit',['id' => $role->id]) }}" 
                                   title="Edit" 
                                   class="text-success">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                            </span>
                        @endcan

                        @can('role-delete')
                            <span class="menu-icon">
                                <a href="javascript:void(0);" 
                                   title="Delete" 
                                   class="text-danger deleteRole" 
                                   data-id="{{ $role->id }}">
                                    <i class="mdi mdi-delete"></i>
                                </a>
                            </span>
                        @endcan
                    </td>

                  </tr>
                @empty
                    <tr>
                      <td colspan="3" class="no-record"> <div class="col-12 text-center">No record found </div></td>
                    </tr>
                @endforelse
              </tbody>
            </table>
          </div>
            <div class="custom_pagination">
             {{ $roles->links('pagination::bootstrap-4') }}
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script>
  $('.deleteRole').on('click', function() {
    var id = $(this).attr('data-id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the Role?",
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
                  url: "/role/delete/" + id,
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

@stop
