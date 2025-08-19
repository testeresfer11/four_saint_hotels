@extends('admin.layouts.app')
@section('title', 'Edit Feature')

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Other Services </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.other_services.list') }}">Feature</a></li>
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
                <h4 class="card-title">Other Services</h4>

                <form id="edit-category" class="forms-sample" action="{{ route('admin.other_services.edit', $category->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

              

                @php
                    $selectedHotelRoomId = old('hotel_room_id', $category->hotel_room_type_id);

                @endphp

                <div class="form-group">
                    <label for="hotel_room_id">Select Hotel Room</label>
                   <select class="form-control @error('hotel_room_id') is-invalid @enderror" name="hotel_room_id[]" id="hotel_room_id" multiple>
                    @foreach($hotel_rooms as $room_type)
                        <option value="{{ $room_type->room_type_id }}" {{ $selectedHotelRoomId == $room_type->room_type_id ? 'selected' : '' }}>
                                {{ $room_type->room_name }}
                            </option>
                    @endforeach
                </select>

                    @error('hotel_room_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>



                    <div class="form-group">
<<<<<<< HEAD
                        <label for="title">Title</label>
=======
                        <label for="title">Title <span class="text-danger">*</span></label>
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
                        <input 
                            type="text" 
                            name="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            placeholder="Enter title"
                            value="{{ old('title', $category->name) }}"
                        >
                        @error('name')
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
                        <label for="title">Price</label>
=======
                        <label for="title">Price <span class="text-danger">*</span></label>
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
                        <input 
                            type="text" 
                            name="price" 
                            class="form-control @error('price') is-invalid @enderror" 
                            id="price" 
                            placeholder="Enter price"
                            value="{{ old('price', $category->price) }}"
                        >
                        @error('price')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                       <div class="form-group">
<<<<<<< HEAD
                        <label for="price">Quantity</label>
=======
                        <label for="price">Quantity<span class="text-danger">*</span></label>
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
                        <input 
                            type="text" 
                            name="total_quantity" 
                            class="form-control @error('total_quantity') is-invalid @enderror" 
                            id="total_quantity" 
                            placeholder="Enter total_quantity"
                            value="{{$category->total_quantity }}"
                        >
                        @error('price')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>


                    {{--<div class="form-group">
                        <label for="icon">Icon (optional)</label>
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
                                <img src="{{ asset('storage/' . $category->icon) }}" alt="Current Icon" style="max-width: 200px; border: 1px solid #ccc; padding: 5px; border-radius: 6px;">
                            @endif
                            <img id="iconPreview" src="#" style="display:none; max-width: 200px; border: 1px solid #ccc; padding: 5px; border-radius: 6px;">
                        </div>
                    </div>--}}

                    <button type="submit" class="btn btn-primary mt-3">Update other service</button>
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
                hotel_room_type_id: {
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
                hotel_room_type_id: {
                    required: "Please select a hotel Room Type."
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
