@extends('admin.layouts.app')
@section('title', 'Room Type')

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Room Type</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Room Type List</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between flex-column row-gap-3 flex-sm-row px-3 py-3 align-items-md-center align-items-center">
                    <h4 class="card-title mb-0">Room Type Management</h4>
                    <div class="d-flex justify-content-between align-items-center ">
                    
                        <input type="text" name="search_keyword" class="form-control mr-2 " placeholder="Search by name" value="{{ request('search_keyword') }}">
                    
                    <button id="fetchHotelsBtn" class="btn btn-sm btn-primary w-100  fetch-hotels-btn">
                        <span class="fetch-icon" id="fetchBtnLoader"><i class="fa-solid fa-arrows-rotate spinner-icon"></i></span>
                        <span id="fetchBtnText" class="fetch-btn-text">Fetch Room Type</span>
                        {{-- <span  class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span> --}}
                    </button>
                </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-stripe">
                       <thead>
                            <tr>
                                <th>Room Name</th>
                                <th>Property Type</th>
                                <th>Max Occupancy</th>
                                <th>Number of Rooms</th>
                                <th>Hotel Name</th>
                                <th>Room Avaliable Today</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $room)
                            <tr>
                                <td>{{ $room->room_name }}</td>
                                <td>{{ $room->property_type }}</td>
                                <td>{{ $room->max_occupancy }}</td>
                                <td>{{ $room->number_of_rooms }}</td>
                                <td>{{ $room->hotel->name ?? 'N/A' }}</td>
                                 <td>
                                    {{ $room->availabilities->first()->available_rooms ?? 'N/A' }}
                                </td>

                                
                                  <td>
                                  <span class="menu-icon">
                                        <a href="{{route('admin.roomtype.view',['id' => $room->room_type_id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                                    </span>&nbsp;&nbsp;&nbsp;

                                    <span class="menu-icon">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#uploadImagesModal" data-room-id="{{ $room->room_type_id }}" data-description="{{ $room->description }}" title="Add Images" class="text-success openImageUploadModal">
                                            <i class="mdi mdi-pen"></i>
                                        </a>
                                    </span>

                                </td>
                                
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No Room Types Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Optional pagination --}}
                {{-- <div class="custom_pagination">
                    {{ $hotels->links('pagination::bootstrap-4') }}
            </div> --}}
        </div>
    </div>
</div>
</div>


<!-- Upload Images Modal -->
<div class="modal fade" id="uploadImagesModal" tabindex="-1" aria-labelledby="uploadImagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="uploadImagesForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="room_type_id" id="modal_hotel_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadImagesModalLabel">Update Room Type Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="images">Select Images</label>
                        <input type="file" name="images[]" id="imageInput" multiple class="form-control" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                      <textarea  name="description" id="description"required maxlength="200"></textarea>
                    </div>
                   <div class="form-group">
                        <label for="service_categories">Room Features</label>
                        <select name="service_categories[]" id="service_categories" class="form-control" multiple>
                            @foreach($service_categories as $category)
                                <option value="{{ $category->id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="mt-3">
                        <h6>Saved Images</h6>
                        <div class="row" id="savedHotelImages"></div>
                    </div>

                    <div class="mt-3">
                        <h6>Selected Images Preview</h6>
                        <div class="row" id="imagePreviewContainer"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('#service_categories').select2({
            placeholder: "Select room features",
            width: '100%'
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".fetch-hotels-btn").forEach(button => {
        button.addEventListener("click", function () {
            const spinner = this.querySelector(".spinner-icon");

            if (spinner) {
                spinner.classList.add("spin");
                setTimeout(() => {
                    spinner.classList.remove("spin");
                }, 3000);
            }
        });
    });
});

</script>

<script>
    document.getElementById('fetchHotelsBtn').addEventListener('click', function() {
        const fetchBtnText = document.getElementById('fetchBtnText');
        const fetchBtnLoader = document.getElementById('fetchBtnLoader');

        fetch('/api/sabee/roomtype/fetch')
            .then(response => response.json())
            .then(data => {
          
                fetchBtnText.classList.remove('d-none');
                fetchBtnLoader.classList.add('d-none');
              
                if (data.status_code == 200) {
                    toastr.success(data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 2000); 
                } else {
                    toastr.error(data.message || 'Something went wrong');
                }
            })
            .catch(error => {
                fetchBtnText.classList.remove('d-none');
                fetchBtnLoader.classList.add('d-none');
                toastr.error('Something went wrong: ' + error.message);
            });
    });
</script>

<script>
    // Modal Open Handler
    document.querySelectorAll('.openImageUploadModal').forEach(button => {
        button.addEventListener('click', function () {
            const hotelId = this.getAttribute('data-room-id');
            const description = this.getAttribute('data-description');
            document.getElementById('modal_hotel_id').value = hotelId;
            document.getElementById('description').value = description;

            $("textarea#description").val(description);
            document.getElementById('savedHotelImages').innerHTML = '';
            document.getElementById('imagePreviewContainer').innerHTML = '';
            document.getElementById('imageInput').value = '';

            // Fetch and display saved images
            fetch(`/admin/roomtype/${hotelId}/images`)
                .then(res => res.json())
                .then(data => {
                    if (data.status) {
                        const container = document.getElementById('savedHotelImages');
                        data.images.forEach(image => {
                            const col = document.createElement('div');
                            col.classList.add('col-md-3', 'mb-2', 'position-relative');
                            col.setAttribute('data-image-id', image.id);

                            col.innerHTML = `
                                <img src="${image.image_path}" class="img-fluid rounded border" style="height: 120px; object-fit: cover;" alt="Saved Image">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-1 bg-danger text-white rounded-circle" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                            `;
                            container.appendChild(col);

                            // Delete button handler
                            col.querySelector('button.btn-close').addEventListener('click', function () {
                                if (!confirm('Are you sure you want to delete this image?')) return;

                                const imageId = col.getAttribute('data-image-id');
                                fetch(`/admin/roomtype/image/delete/${imageId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(resp => {
                                    if (resp.status) {
                                        toastr.success(resp.message);
                                        col.remove();
                                    } else {
                                        toastr.error(resp.message || 'Failed to delete image');
                                    }
                                })
                                .catch(err => {
                                   
                                    toastr.error('Error deleting image');
                                });
                            });
                        });
                    }
                });
        });
    });

    // Image Preview Handler
    document.getElementById('imageInput').addEventListener('change', function (e) {
        const previewContainer = document.getElementById('imagePreviewContainer');
        previewContainer.innerHTML = '';
        Array.from(e.target.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const col = document.createElement('div');
                col.classList.add('col-md-3', 'mb-2');
                col.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded border" style="height: 120px; object-fit: cover;" alt="Selected Image">`;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    });

    // Image Upload Form Submit
    document.getElementById('uploadImagesForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
      
        fetch("{{ route('admin.roomtype.upload.images') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            }
        })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            if (data.status === true) {
                toastr.success(data.message);
                form.reset();

                const modal = bootstrap.Modal.getInstance(document.getElementById('uploadImagesModal'));
                modal.hide();

                // Refresh saved images
                const hotelId = formData.get('hotel_id');
                fetch(`/admin/hotel/${hotelId}/images`)
                    .then(res => res.json())
                    .then(data => {
                        
                        if (data.status) {
                            const container = document.getElementById('savedHotelImages');
                            container.innerHTML = '';
                            data.images.forEach(image => {
                                const col = document.createElement('div');
                                col.classList.add('col-md-3', 'mb-2', 'position-relative');
                                col.setAttribute('data-image-id', image.id);

                                col.innerHTML = `
                                    <img src="${image.image_path}" class="img-fluid rounded border" style="height: 120px; object-fit: cover;" alt="Saved Image">
                                    <button type="button" class="btn-close position-absolute top-0 end-0 m-1 bg-danger text-white rounded-circle" aria-label="Close"></button>
                                `;
                                container.appendChild(col);

                                col.querySelector('button.btn-close').addEventListener('click', function () {
                                    if (!confirm('Are you sure you want to delete this image?')) return;

                                    const imageId = col.getAttribute('data-image-id');
                                    fetch(`/admin/hotel/image/${imageId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(res => res.json())
                                    .then(resp => {
                                        if (resp.status) {
                                            toastr.success(resp.message);
                                            col.remove();
                                        } else {
                                            toastr.error(resp.message || 'Failed to delete image');
                                        }
                                    })
                                    .catch(err => {
                                        toastr.error('Error deleting image');
                                    });
                                });
                            });
                        }
                    });
            } else {
                toastr.error(data.message || 'Failed to upload images');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            toastr.error('Something went wrong.');
        });
    });
</script>


@endsection