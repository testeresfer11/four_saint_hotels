@extends('admin.layouts.app')
@section('title', 'Roles')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Roles</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Roles</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h4 class="card-title">Roles Management</h4>
            <div class="admin-filters">
             
            </div>
            @can('role-add')
            <a href="{{route('admin.role.add')}}">
              <button type="button" class="btn default-btn btn-md">
                <span class="menu-icon">+ Add Roles</span>
              </button>
            </a>
            @endcan
        </div>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th> Sr. No. </th>
                <th> Name </th>
                <th> Action </th>
              </tr>
            </thead>
              @can('role-list')
            <tbody>
              @forelse ($roles as $key => $data)
                <tr data-id="{{$data->id}}">
                  <td>{{++$key}}</td>
                  <td> {{$data->name}} </td>
                  <td> 
                    @can('role-edit')
                    <span class="menu-icon">
                      <a href="{{route('admin.role.edit',['id' => $data->id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                    </span>&nbsp;&nbsp;
                    @endcan
                     @can('role-delete')
                    <span class="menu-icon">
                      <a href="#" title="Delete" class="text-danger deleteBanner" data-id="{{$data->id}}"><i class="mdi mdi-delete"></i></a>
                    </span> 
                    @endcan
                  </td>
                </tr>
              @empty
                  <tr>
                    <td colspan="6" class="no-record"> <center>No record found </center></td>
                  </tr>    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  
              @endforelse
            </tbody>
             @endcan
          </table>
        </div>
        <div class="custom_pagination">
          {{ $roles->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  $('.deleteBanner').on('click', function() {
    var category_id = $(this).data('id');
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete the Role?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ea57c",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/admin/role/delete/" + category_id,
                type: "GET", 
                success: function(response) {
                  if (response.status == "success") {
                      $(`tr[data-id="${category_id}"]`).remove();
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
