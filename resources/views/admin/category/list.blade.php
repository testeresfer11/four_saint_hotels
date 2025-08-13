@extends('admin.layouts.app')
@section('title', 'Feature')
@section('breadcrum')
<div class="page-header">
  <h3 class="page-title">Feature</h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Feature</li>
    </ol>
  </nav>
</div>
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body p-0">
        <div class="d-flex justify-content-between flex-column flex-md-row px-3 py-3 align-items-md-center align-items-start">
          <h4 class="card-title m-0">Feature Management</h4>
          <div class="d-flex align-items-center justify-content-between">
            <div class="admin-filters mr-2">
              <x-filter />
            </div>

            <a href="{{route('admin.category.add')}}"><button type="button" class="btn btn-primary btn-md">
                <span class="menu-icon">+ Add Feature</span></button></a>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-stripe">
            <thead>
              <tr>
                <th>Icon</th>
                <th>Title</th>
                <th>Hotel</th>
                <th>Description</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($categories as $category)
              <tr>
                <td>
                  @if ($category->icon && file_exists(public_path('storage/' . $category->icon)))
                      <img src="{{ asset('storage/' . $category->icon) }}" alt="Category Icon" width="50" height="50">
                  @else
                      <img src="{{ asset('admin/images/new-features.png') }}" alt="Default Icon" width="50" height="50">
                  @endif
              </td>

                <td>{{ $category->title }}</td>
                <td>{{ $category->hotel->name }}</td>
                
              <td>{{ \Illuminate\Support\Str::words($category->description, 10, '...') }}</td>
                <td>
                  <a href="{{ route('admin.category.edit', $category->id) }}" class="text-success" ><i class="mdi mdi-pencil"></i></a>
                  <a href="{{ route('admin.category.delete', $category->id) }}" class="text-danger deleteUser"  onclick="return confirm('Are you sure?')"><i class="mdi mdi-delete"></i></a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="custom_pagination">
          {{ $categories->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  $('.deleteCategory').on('click', function() {
    var category_id = $(this).data('id');
    Swal.fire({
      title: "Are you sure?",
      text: "You want to delete this Feature?",
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
          url: "/admin/category/delete/" + category_id,
          type: "GET",
          success: function(response) {
            if (response.status == "success") {
              if (response.count == 0) {
                $(`tr[data-id="${category_id}"]`).remove();
                toastr.success(response.message);
              } else {
                Swal.fire({
                  title: "OOPs! Unable to delete. ",
                  text: response.message,
                  icon: "info",
                  confirmButtonColor: "#2ea57c",
                  confirmButtonText: "Ok"
                });
              }
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
      text: "Do you want to change the status of the Feature?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#2ea57c",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, mark as status"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "/admin/category/changeStatus",
          type: "GET",
          data: {
            id: id,
            status: action
          },
          success: function(response) {
            if (response.status == "success") {
              toastr.success(response.message);
              $('.switch[data-id="' + id + '"]').data('value', !action);
            } else {
              $('.switch[data-id="' + id + '"]').data('value', action);
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