@extends('admin.layouts.app')
@section('title', 'Sub Feature')
@section('breadcrum')
<div class="page-header">
  <h3 class="page-title">Other  Feature</h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Sub Feature</li>
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
          <h4 class="card-title m-0">Other Feature Management</h4>
          <a href="{{ route('admin.other_services.add') }}" class="btn btn-primary btn-md">
            + Add Other Service
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Price</th>
                <th>Total Quantity</th>
                <th>Total Left</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($categories as $subCategory)
              <tr data-id="{{ $subCategory->id }}">
                {{--<td>
                  @if ($subCategory->image && file_exists(public_path('storage/' . $subCategory->image)))
                    <img src="{{ asset('storage/' . $subCategory->image) }}" alt="Image" width="50" height="50">
                  @else
                    <img src="{{ asset('admin/images/default-icon.png') }}" alt="Default Image" width="50" height="50">
                  @endif
                </td>--}}
                <td>{{ $subCategory->name }}</td>
                
                <td>{{ \Illuminate\Support\Str::words($subCategory->description, 10, '...') }}</td>
                 <td>{{ $subCategory->price }}</td>
                 <td>{{ $subCategory->total_quantity }}</td>
                 <td>{{ $subCategory->total_left ??  $subCategory->total_quantity}}</td>
                <td>{{ $subCategory->created_at->format('d M, Y') }}</td>
                <td>
                  <a href="{{ route('admin.other_services.edit', $subCategory->id) }}" class="text-success me-2">
                    <i class="mdi mdi-pencil"></i>
                  </a>
                  <a href="javascript:void(0);" 
                    class="text-danger deleteSubCategory" 
                    data-id="{{ $subCategory->id }}">
                    <i class="mdi mdi-delete"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center">No Sub Categories found.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $categories->links('pagination::bootstrap-4') }}
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).on('click', '.deleteSubCategory', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
   

    Swal.fire({
      title: "Are you sure?",
      text: "You want to delete this Other Service?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#B46326",
      cancelButtonColor: "#fff",
      confirmButtonText: "Yes, delete it!",
      customClass: {
        cancelButton: 'swal-cancel-custom'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "/admin/other_services/delete/" + id,
          type: "GET",
          success: function(response) {

           
              $(`tr[data-id="${id}"]`).remove();
              toastr.success(response.message || "Other Service deleted successfully.");
           
          },
          error: function() {
            toastr.error("Server error. Please try again.");
          }
        });
      }
    });
  });
</script>


@endsection
