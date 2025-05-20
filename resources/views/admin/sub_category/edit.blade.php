@extends('admin.layouts.app')
@section('title', 'Edit Sub Feature')

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Sub Feature</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.sub_category.list') }}">Sub Feature</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Sub Feature</h4>

                <form id="edit-sub-category" class="forms-sample" 
                      action="{{ route('admin.sub_category.edit', $subCategory->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- if you use POST for edit, no need for method spoofing --}}
                    
                    <div class="form-group">
                        <label for="category_id">Select Feature</label>
                        <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="category_id" required>
                            <option value="">Select Feature</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ (old('category_id', $subCategory->category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" id="title" 
                               placeholder="Enter title" value="{{ old('title', $subCategory->title) }}" required>
                        @error('title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description (optional)</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  id="description" rows="3" placeholder="Enter description">{{ old('description', $subCategory->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Icon (optional)</label>
                        @if($subCategory->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $subCategory->image) }}" alt="Current Icon" 
                                     style="max-width: 200px; max-height: 150px; border: 1px solid #ccc; padding: 5px; border-radius: 6px;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" id="image" accept="image/*" onchange="previewIcon(event)">
                        @error('image')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <div class="mt-2">
                            <img id="iconPreview" src="#" style="display:none; max-width: 200px; border: 1px solid #ccc; padding: 5px; border-radius: 6px;">
                        </div>
                        <small class="form-text text-muted">Upload new image to replace the existing one.</small>
                    </div>

                   

                    <button type="submit" class="btn btn-primary mt-3">Update Sub Category</button>
                    <a href="{{ route('admin.sub_category.list') }}" class="btn btn-secondary mt-3">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewIcon(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('iconPreview');
            preview.src = reader.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    $(document).ready(function() {
        $("#edit-sub-category").validate({
            rules: {
                category_id: {
                    required: true,
                },
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                image: {
                    extension: "jpg|jpeg|png|svg",
                    filesize: 2097152 // 2MB
                }
            },
            messages: {
                category_id: {
                    required: "Please select a category."
                },
                title: {
                    required: "Title is required.",
                    minlength: "Title must be at least 3 characters.",
                    maxlength: "Title must not exceed 255 characters."
                },
                image: {
                    extension: "Only jpg, jpeg, png, svg files are allowed.",
                }
            },
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

        // Custom file size validation method
        $.validator.addMethod('filesize', function(value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        }, 'File size must be less than 2MB');
    });
</script>
@endsection
