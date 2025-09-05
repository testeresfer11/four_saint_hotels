@extends('admin.layouts.app')
@section('title', 'View Hotel')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Room Type</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.hotel.list') }}">Room Type</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Room Type</li>
        </ol>
    </nav>
</div>
@endsection
@section('content')
<div>
    <hr>
    <h4 class="mt-4">Room Type Details</h4>
    <div class="card mt-3">
        <div class="card-body">
            <h6><strong>Hotel Name:</strong> {{ $hotel->hotel->name }}</h6>
        </div>
        @if($hotel->images)
        <h4 class="mt-4">Room Types Images</h4>
        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    @foreach($hotel->images as $image)
                    <div class="col-md-3 mb-3">
                        <div class="border p-2 rounded">
                            <img src="{{ $image->image_path }}" alt="Hotel Image" class="img-fluid rounded" style="height: 150px; object-fit: cover;">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif


    </div>

      <h4 class="mt-4">Rooms</h4>
    <div class="card mt-3">
        <div class="card-body table-responsive">
            <table class="table table-stripe" id="roomsTable">

                <thead>
                    <tr>
                        <th>Room ID</th>
                        <th>Room Name</th>
                       
                    </tr>
                </thead>
                <tbody>
                    @foreach($hotel->rooms as $room)
                    <tr>
                        <td>{{ $room->room_id }}</td>
                        <td>{{ $room->room_name }}</td>
                    
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

   <h4 class="mt-4">Rate Plans</h4>
    <div class="card mt-3">
        <div class="card-body table-responsive">
        <table class="table table-stripe" id="ratesTable">
                <thead>
                    <tr>
                        <th>Rate Plan ID</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Number of Guests</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hotel->rates as $rate)
                    <tr>
                        <td>{{ $rate->rateplan_id }}</td>
                        <td>{{ $rate->start_rate_date }}</td>
                        <td>{{ $rate->end_rate_date }}</td>
                        <td>{{ $rate->number_of_guests }}</td>
                        <td>{{ $rate->currency}} {{ $rate->price}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



</div>
@endsection
@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#roomsTable').DataTable();
        $('#ratesTable').DataTable();
    });
</script>


@endsection