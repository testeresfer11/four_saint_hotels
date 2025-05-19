@extends('admin.layouts.app')
@section('title', 'Hotels')
<style type="text/css">
    
.modal-content button.btn-close {
    border-radius: 50%;
    height: 10px !important;
    width: 10px !important;
    border: none;
    font-size: 14px;
    background: #0090e7 !important;
    color: #fff;
}
</style>
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Hotels</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Hotel List</li>
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
                    <h4 class="card-title mb-0"></h4>
                    <button id="fetchHotelsBtn" class="btn btn-sm btn-primary">
                        <span id="fetchBtnText">Fetch Hotels</span>
                        <span id="fetchBtnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>

                <h4 class="card-title">Hotel Management</h4>
                <div class="table-responsive">
                    <table class="table table-stripe">
                        <thead>
                            <tr>
                                <th>Hotel Name</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Address</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th> Action </th>

                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $hotel)

                            <tr>
                                <td>{{ $hotel->name }}</td>
                                <td>{{ $hotel->city}}</td>
                                <td>{{ $hotel->country }}</td>
                                <td>{{ $hotel->address }}</td>
                                <td>{{ $hotel->email }}</td>
                                <td>{{ $hotel->phone }}</td>
                                <td>
                                    <span class="menu-icon">
                                        <a href="{{route('admin.hotel.view',['id' => $hotel->hotel_id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                                    </span>&nbsp;&nbsp;&nbsp;

                                    <span class="menu-icon">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#uploadImagesModal" data-hotel-id="{{ $hotel->id }}" title="Add Images" class="text-success openImageUploadModal">
                                            <i class="mdi mdi-image"></i>
                                        </a>
                                    </span>

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No Hotels Found</td>
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
    <div class="modal-dialog">
        <form id="uploadImagesForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="hotel_id" id="modal_hotel_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadImagesModalLabel">Upload Hotel Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="images">Select Images</label>
                        <input type="file" name="images[]" id="imageInput" multiple class="form-control" accept="image/*">
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
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    document.getElementById('fetchHotelsBtn').addEventListener('click', function() {
        const fetchBtnText = document.getElementById('fetchBtnText');
        const fetchBtnLoader = document.getElementById('fetchBtnLoader');

        // Show loader and hide button text
        fetchBtnText.classList.add('d-none');
        fetchBtnLoader.classList.remove('d-none');

        fetch('/api/sabee/hotels/fetch')
            .then(response => response.json())
            .then(data => {
                // Hide loader and show button text again
                fetchBtnText.classList.remove('d-none');
                fetchBtnLoader.classList.add('d-none');
                console.log(data)
                if (data.status_code == 200) {
                    toastr.success(data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 2000); // Reload after 1.5 seconds
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
            const hotelId = this.getAttribute('data-hotel-id');
            document.getElementById('modal_hotel_id').value = hotelId;

            document.getElementById('savedHotelImages').innerHTML = '';
            document.getElementById('imagePreviewContainer').innerHTML = '';
            document.getElementById('imageInput').value = '';

            // Fetch and display saved images
            fetch(`/admin/hotel/${hotelId}/images`)
                .then(res => res.json())
                .then(data => {
                    console.log(data);
                    if (data.status) {
                        const container = document.getElementById('savedHotelImages');
                        data.images.forEach(image => {
                            const col = document.createElement('div');
                            col.classList.add('col-md-3', 'mb-2', 'position-relative');
                            col.setAttribute('data-image-id', image.id);

                            col.innerHTML = `
                                <img src="${image.image_path}" class="img-fluid rounded border" style="height: 120px; object-fit: cover;" alt="Saved Image">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-1 bg-danger text-white rounded-circle" aria-label="Close"></button>
                            `;
                            container.appendChild(col);

                            // Delete button handler
                            col.querySelector('button.btn-close').addEventListener('click', function () {
                                if (!confirm('Are you sure you want to delete this image?')) return;

                                const imageId = col.getAttribute('data-image-id');
                                fetch(`/admin/hotel/image/delete/${imageId}`, {
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
                                    console.error(err);
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

        fetch("{{ route('admin.hotel.upload.images') }}", {
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
                        console.log(data);
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
                                        console.error(err);
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
            console.error(err);
            toastr.error('Something went wrong.');
        });
    });
</script>





@endsection