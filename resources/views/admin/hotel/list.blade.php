@extends('admin.layouts.app')
@section('title', 'Hotels')
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
                                          <a href="{{route('admin.hotel.view',['id' => $hotel->id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                                        </span>&nbsp;&nbsp;&nbsp;
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
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    document.getElementById('fetchHotelsBtn').addEventListener('click', function () {
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
                if (data.status_code ==200) {
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
@endsection

