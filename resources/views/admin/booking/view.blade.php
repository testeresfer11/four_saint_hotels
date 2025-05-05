@extends('admin.layouts.app')
@section('title', 'View Booking')
@section('breadcrum')
    <div class="page-header">
        <h3 class="page-title">Bookings</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.booking.list') }}">Bookings</a></li>
                <li class="breadcrumb-item active" aria-current="page">View Booking</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
<div>
    <h4 class="user-title">Booking Details</h4>
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <h6><strong>Reservation Code:</strong> {{ $booking->reservation_code }}</h6>
                    <h6><strong>Door Code:</strong> {{ $booking->door_code ?? 'N/A' }}</h6>
                    <h6><strong>Status:</strong> {{ $booking->status }}</h6>
                    <h6><strong>Created At:</strong> {{ convertDate($booking->created_at) }}</h6>
                </div>
                <div class="col-md-4">
                    <h6><strong>Room Type:</strong> {{ $booking->room_type_name }}</h6>
                    <h6><strong>Room Name:</strong> {{ $booking->room_name }}</h6>
                    <h6><strong>Check-in:</strong> {{ $booking->checkin_date }}</h6>
                    <h6><strong>Check-out:</strong> {{ $booking->checkout_date }}</h6>
                </div>
                <div class="col-md-4">
                    <h6><strong>Guests:</strong> {{ $booking->number_of_guests }}</h6>
                    <h6><strong>Adults:</strong> {{ $booking->guest_count['adults'] }}</h6>
                    <h6><strong>Children:</strong> {{ $booking->guest_count['children'] }}</h6>
                    <h6><strong>Infants:</strong> {{ $booking->guest_count['infants'] }}</h6>

                </div>
            </div>

            <hr>

            <h5 class="mb-3">Customer Details</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6><strong>Name:</strong> {{ $booking->customer->first_name }} {{ $booking->customer->last_name }}</h6>
                    <h6><strong>Email:</strong> {{ $booking->customer->email }}</h6>
                    <h6><strong>Phone:</strong> {{ $booking->customer->phone_number }}</h6>
                    <h6><strong>Country Code:</strong> {{ $booking->customer->country_code }}</h6>
                </div>
                <div class="col-md-6">
                    <h6><strong>Address:</strong> {{ $booking->customer->address ?? 'N/A' }}</h6>
                    <h6><strong>City:</strong> {{ $booking->customer->city ?? 'N/A' }}</h6>
                    <h6><strong>Zip:</strong> {{ $booking->customer->zip ?? 'N/A' }}</h6>
                    <h6><strong>Remarks:</strong> {{ $booking->customer->remarks ?? 'N/A' }}</h6>
                </div>
            </div>

            <hr>

           @forelse($booking->booking_guests ?? [] as $guest)
                <p>- {{ $guest->first_name }} {{ $guest->last_name }}</p>
            @empty
                <p>No guests found.</p>
            @endforelse

            <hr>

            <h5 class="mb-3">Pricing</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>VAT</th>
                            <th>City Tax</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalAmount = 0; @endphp
                        @forelse($booking->booking_prices ?? [] as $price)
                            @php $totalAmount += (float) $price->amount; @endphp
                            <tr>
                                <td>{{ $price->date }}</td>
                                <td>{{ number_format($price->amount, 2) }} {{ $booking->currency }}</td>
                                <td>{{ $price->vat }}</td>
                                <td>{{ $price->city_tax }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No pricing data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total Calculated:</th>
                            <th>{{ number_format($totalAmount, 2) }} {{ $booking->currency }}</th>
                        </tr>
                        <tr>
                            <th colspan="3" class="text-end">Total Paid:</th>
                            <th>{{ number_format($booking->paid ?? 0, 2) }} {{ $booking->currency }}</th>
                        </tr>
                    </tfoot>
                </table>


            <hr>

            <h5 class="mb-3">Comment</h5>
            <div class="p-3 border rounded">
                {!!$booking->comment!!}
            </div>
        </div>
    </div>
</div>
@endsection
