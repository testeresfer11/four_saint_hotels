@extends('admin.layouts.app')
@section('title', 'View Hotel')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Hotel</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.hotel.list') }}">Hotel</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Hotel</li>
        </ol>
    </nav>
</div>
@endsection
@section('content')
<div>
    <hr>
    <h4 class="mt-4">Hotel Details</h4>
    <div class="card mt-3">
        <div class="card-body">
            <h6><strong>Hotel Name:</strong> {{ $hotel->name }}</h6>
            <h6><strong>City:</strong> {{ $hotel->city }}</h6>
            <h6><strong>Country:</strong> {{ $hotel->country }}</h6>
            <h6><strong>Zip:</strong> {{ $hotel->zip }}</h6>
            <h6><strong>Address:</strong> {{ $hotel->address }}</h6>
            <h6><strong>Latitude:</strong> {{ $hotel->latitude }}</h6>
            <h6><strong>Longitude:</strong> {{ $hotel->longitude }}</h6>
            <h6><strong>Phone:</strong> {{ $hotel->phone }}</h6>
            <h6><strong>Email:</strong> {{ $hotel->email }}</h6>
            <h6><strong>Currency:</strong> {{ $hotel->currency }}</h6>
            <h6><strong>Room Rate per Night:</strong> {{ $hotel->rate_per_night }}</h6>
        </div>
        @if($hotel->hotel_images)
        <h4 class="mt-4">Hotel Images</h4>
        <div class="card mt-3">
            <div class="card-body">
                <div class="row">
                    @foreach($hotel->hotel_images as $image)
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

    <h4 class="mt-4">Room Types</h4>
    <div class="card mt-3">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Room Name</th>
                        <th>Property Type</th>
                        <th>Max Occupancy</th>
                        <th>Number of Rooms</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hotel->room_types as $room)
                    <tr>
                        <td>{{ $room->room_name }}</td>
                        <td>{{ $room->property_type }}</td>
                        <td>{{ $room->max_occupancy }}</td>
                        <td>{{ $room->number_of_rooms }}</td>
                        <td>{{ \Carbon\Carbon::parse($room->create_date_time)->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <h4 class="mt-4">Rate Plans</h4>
    <div class="card mt-3">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Rate Plan Name</th>
                        <th>Rate Plan ID</th>
                        <th>Linked to Master</th>
                        <th>Dynamic Pricing</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hotel->rate_plans as $rate)
                    <tr>
                        <td>{{ $rate->rateplan_name }}</td>
                        <td>{{ $rate->rateplan_id }}</td>
                        <td>{{ $rate->linked_to_master ? 'Yes' : 'No' }}</td>
                        <td>{{ $rate->dynamic_pricing ? 'Yes' : 'No' }}</td>
                        <td>{{ \Carbon\Carbon::parse($rate->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



</div>
@endsection