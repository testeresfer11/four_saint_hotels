@extends('admin.layouts.app')
@section('title', 'Sub Feature')
@section('breadcrum')
<div class="page-header">
  <h3 class="page-title">Sub Feature</h3>
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
      <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="card-title">Sub Feature Management</h4>
          <a href="{{ route('admin.sub_category.add') }}" class="btn btn-primary btn-md">
            + Add Sub Feature
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Category</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($subCategories as $subCategory)
              <tr data-id="{{ $subCategory->id }}">
                <td>
                  @if ($subCategory->image && file_exists(public_path('storage/' . $subCategory->image)))
                    <img src="{{ asset('storage/' . $subCategory->image) }}" alt="Image" width="50" height="50">
                  @else
                    <img src="{{ asset('admin/images/default-icon.png') }}" alt="Default Image" width="50" height="50">
                  @endif
                </td>
                <td>{{ $subCategory->title }}</td>
                <td>{{ $subCategory->category ? $subCategory->category->title : '-' }}</td>
                <td>{{ \Illuminate\Support\Str::words($subCategory->description, 10, '...') }}</td>
                
                <td>{{ $subCategory->created_at->format('d M, Y') }}</td>
                <td>
                  <a href="{{ route('admin.sub_category.edit', $subCategory->id) }}" class="text-success me-2">
                    <i class="mdi mdi-pencil"></i>
                  </a>
                  <a href="{{ route('admin.sub_category.delete', $subCategory->id) }}" 
                     class="text-danger deleteSubCategory" 
                     onclick="return confirm('Are you sure to delete this sub-category?')">
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
          {{ $subCategories->links('pagination::bootstrap-4') }}
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $('.deleteSubCategory').on('click', function(e) {
    e.preventDefault();
    if(confirm('Are you sure you want to delete this sub-category?')) {
      window.location.href = $(this).attr('href');
    }
  });
</script>
@endsection
