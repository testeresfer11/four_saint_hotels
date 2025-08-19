@extends('admin.layouts.app')
@section('title', 'Add Sub Feature')

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Sub Feature</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.category.list') }}">Sub Feature</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add</li>
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
                <h4 class="card-title">Add Sub Feature</h4>

                <form id="add-category" class="forms-sample" action="{{ route('admin.sub_category.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
<<<<<<< HEAD
                        <label for="category_id">Select Feature</label>
=======
                        <label for="category_id">Select Feature <span class="text-danger">*</span></label>
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
                        <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="category_id">
                            <option value="">Select Feature</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
<<<<<<< HEAD
                        <label for="title">Title</label>
=======
                        <label for="title">Title <span class="text-danger">*</span></label>
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" id="title" placeholder="Enter title">
                        @error('title')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description (optional)</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" id="description" rows="3" placeholder="Enter description"></textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="icon">Icon (optional)</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" id="image" accept="image/*" onchange="previewIcon(event)">
                        @error('image')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <div class="mt-2">
                            <img id="iconPreview" src="#" style="display:none; max-width: 200px; border: 1px solid #ccc; padding: 5px; border-radius: 6px;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Add Category</button>
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
        $("#add-category").validate({
            rules: {
<<<<<<< HEAD
                hotel_id: {
=======
                category_id: {
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
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
<<<<<<< HEAD
                hotel_id: {
                    required: "Please select a hotel."
=======
                category_id: {
                    required: "Please select a Category."
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
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
