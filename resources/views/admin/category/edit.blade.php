@extends('admin.layouts.app')
@section('title', 'Edit Feature')

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Feature </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.category.list') }}">Feature</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Feature</h4>

                <form id="edit-category" class="forms-sample" action="{{ route('admin.category.edit', $category->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
<<<<<<< HEAD
                        <label for="hotel_id">Select Hotel</label>
=======
                        <label for="hotel_id">Select Hotel<span class="text-danger">*</span></label>
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
                        <select class="form-control @error('hotel_id') is-invalid @enderror" name="hotel_id" id="hotel_id">
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                                <option value="{{ $hotel->id }}" {{ $category->hotel_id == $hotel->id ? 'selected' : '' }}>
                                    {{ $hotel->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('hotel_id')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
<<<<<<< HEAD
                        <label for="title">Title</label>
=======
                        <label for="title">Title<span class="text-danger">*</span></label>
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
                        <input 
                            type="text" 
                            name="title" 
                            class="form-control @error('title') is-invalid @enderror" 
                            id="title" 
                            placeholder="Enter title"
                            value="{{ old('title', $category->title) }}"
                        >
                        @error('title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description (optional)</label>
                        <textarea 
                            name="description" 
                            class="form-control @error('description') is-invalid @enderror" 
                            id="description" 
                            rows="3" 
                            placeholder="Enter description"
                        >{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
<<<<<<< HEAD
                        <label for="icon">Icon (optional)</label>
=======
                        <label for="icon">Icon (optional)<span class="text-danger">*</span></label>
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
                        <input 
                            type="file" 
                            name="icon" 
                            class="form-control @error('icon') is-invalid @enderror" 
                            id="icon" 
                            accept="image/*" 
                            onchange="previewIcon(event)"
                        >
                        @error('icon')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror

                        <div class="mt-2">
                            @if($category->icon)
<<<<<<< HEAD
                                <img src="{{ asset('storage/' . $category->icon) }}" alt="Current Icon" style="max-width: 200px; border: 1px solid #ccc; padding: 5px; border-radius: 6px;">
                            @endif
                            <img id="iconPreview" src="#" style="display:none; max-width: 200px; border: 1px solid #ccc; padding: 5px; border-radius: 6px;">
=======
                                <img id="iconPreview" src="{{ asset('storage/' . $category->icon) }}" alt="Current Icon" style="max-width: 200px; border: 1px solid #ccc; padding: 5px; border-radius: 6px;">
                            @endif
                           
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Update Feature</button>
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
        $("#edit-category").validate({
            rules: {
                hotel_id: {
                    required: true,
                },
                title: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                icon: {
                    extension: "jpg|jpeg|png|svg",
                    filesize: 2097152 // 2MB in bytes
                }
            },
            messages: {
                hotel_id: {
                    required: "Please select a hotel."
                },
                title: {
                    required: "Title is required.",
                    minlength: "Title must be at least 3 characters.",
                    maxlength: "Title must not exceed 255 characters."
                },
                icon: {
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
