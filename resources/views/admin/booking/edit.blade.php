@extends('admin.layouts.app')
@section('title', 'Edit Reservation')
@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.6.1/build/css/intlTelInput.css">
<style>
    .reservation-timeline {
        position: relative;
        padding-left: 2rem;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1rem;
    }
    .timeline-dot {
        position: absolute;
        left: -2rem;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #ddd;
    }
    .timeline-dot.completed {
        background-color: #28a745;
        box-shadow: 0 0 0 2px #28a745;
    }
    .timeline-dot.current {
        background-color: #17a2b8;
        box-shadow: 0 0 0 2px #17a2b8;
    }
    .timeline-dot.pending {
        background-color: #6c757d;
        box-shadow: 0 0 0 2px #6c757d;
    }
    .timeline-line {
        position: absolute;
        left: -1.5rem;
        top: 20px;
        width: 2px;
        height: calc(100% - 20px);
        background-color: #ddd;
    }
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .status-confirmed {
        background-color: #d4edda;
        color: #155724;
    }
    .status-option {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }
    .form-section {
        background: #fff;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        margin-bottom: 1.5rem;
    }
    .form-section-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 1rem 1.25rem;
        font-weight: 600;
        color: #5a5c69;
    }
    .form-section-body {
        padding: 1.25rem;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-control {
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
    .form-control:focus {
        border-color: #5a9bd4;
        box-shadow: 0 0 0 0.2rem rgba(90, 155, 212, 0.25);
    }
    .btn-save {
        background-color: #5cb85c;
        border-color: #5cb85c;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.35rem;
        font-weight: 500;
    }
    .btn-cancel {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.35rem;
        font-weight: 500;
        margin-right: 1rem;
    }
    .time-format-note {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    .external-id-field {
        background-color: #f8f9fa;
    }
    .add-btn {
        background-color: #28a745;
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        cursor: pointer;
    }
    .reservation-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }
    .res-code {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .res-id {
        opacity: 0.9;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Reservation Management</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('admin.booking.list')}}">Reservations</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Reservation</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <form method="POST" action="{{ route('admin.booking.edit', $booking->id) }}">
        @csrf
        
        <div class="row">
            <!-- Left Column - Status Timeline -->
            <div class="col-md-3">
                <div class="form-section">
                    <div class="form-section-header">Reservation Status</div>
                    <div class="form-section-body">
                        <div class="mb-3">
                            <span class="status-badge status-{{ strtolower($booking->status ?? 'confirmed') }}">
                                {{ $booking->status ?? 'Confirmed' }}
                            </span>
                        </div>
                        
                        <div class="reservation-timeline">
                            <div class="timeline-item">
                                <div class="timeline-dot completed"></div>
                                <div class="timeline-line"></div>
                                <div>
                                    <strong>Reserved</strong><br>
                                    <small class="text-muted">{{ $booking->created_at ? $booking->created_at->format('d-m-Y H:i') : '21-05-2025 10:56' }}</small>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot current"></div>
                                <div class="timeline-line"></div>
                                <div>
                                    <strong>Check-In</strong>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot pending"></div>
                                <div>
                                    <strong>Check-Out</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="res-code">{{ $booking->reservation_code ?? '62MYR8V' }}</div>
                            <div class="res-id">ResID: {{ $booking->id ?? '32666231' }} <small>(internal use only)</small></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Main Form -->
            <div class="col-md-9">
                <!-- Room and Dates Section -->
                <div class="form-section">
                    <div class="form-section-header">Room & Stay Details</div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Room type:</label>
                                    <select class="form-control" name="room_type_id" id="room_type_select">
                                        <option value="">--select room type--</option>
                                        @foreach($roomTypes as $roomType)
                                            <option value="{{ $roomType->room_type_id }}" 
                                                {{ ($booking->room_type_id ?? '') == $roomType->room_type_id ? 'selected' : '' }}>
                                                {{ $roomType->room_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Arrival date:</label>
                                    <input type="date" class="form-control" name="checkin_date" value="{{ $booking->checkin_date ?? '2025-06-10' }}">
                                    <div class="time-format-note">use 24 hour format</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Room Name:</label>
                                    <select class="form-control" name="room_id" id="room_name_select">
                                        <option value="">--select from list--</option>
                                        @if(isset($booking->room_type_id))
                                            @foreach($rooms as $room)
                                                @if($room->room_type_id == $booking->room_type_id)
                                                    <option value="{{ $room->room_id }}" 
                                                        {{ ($booking->room_id ?? '') == $room->room_id ? 'selected' : '' }}>
                                                        {{ $room->room_id }} - {{ $room->room_name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Departure Date:</label>
                                    <input type="date" class="form-control" name="checkout_date" value="{{ $booking->checkout_date ?? '2025-06-12' }}">
                                    <div class="time-format-note">use 24 hour format</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Source:</label>
                                    <input type="text" class="form-control" name="booking_source" value="{{ $booking->booking_source ?? 'SabeeApp Connect API' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nr. of nights:</label>
                                    <input type="number" class="form-control" name="nights" value="{{ $booking->nights ?? '2' }}" readonly>
                                </div>
                            </div>
                        </div>

                  

                        <div class="row">
                          
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Reservation Date:</label>
                                    <input type="text" class="form-control" name="reservation_date" value="{{ $booking->created_at ? $booking->created_at->format('d-m-Y H:i') : '21-05-2025 10:56' }}" readonly>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            
                <!-- Status Update Section -->
                <div class="form-section">
                    <div class="form-section-header">Reservation Status</div>
                    <div class="form-section-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select class="form-control" name="status">
                                        <option value="Confirmed" {{ ($booking->status ?? 'Confirmed') === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="Option" {{ ($booking->status ?? '') === 'Option' ? 'selected' : '' }}>Option</option>
                                        <option value="Cancelled" {{ ($booking->status ?? '') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="NoShow" {{ ($booking->status ?? '') === 'NoShow' ? 'selected' : '' }}>NoShow</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

       
              

                <!-- Action Buttons -->
                <div class="text-right mb-4">
                    <button type="button" class="btn btn-cancel" onclick="window.history.back()">Cancel</button>
                    <button type="submit" class="btn btn-save"> Update Reservation</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.6.1/build/js/intlTelInput.min.js"></script>
<script>
    $(document).ready(function() {
        // Dynamic room loading based on room type selection
        $('#room_type_select').change(function() {
            const roomTypeId = $(this).val();
            const roomSelect = $('#room_name_select');
            
            // Clear current room options
            roomSelect.html('<option value="">--select from list--</option>');
            
            if (roomTypeId) {
                // Show loading state
                roomSelect.html('<option value="">Loading rooms...</option>');
                
                // Make AJAX request to get rooms for selected room type
                $.ajax({
                    url: '{{ route("admin.booking.get-rooms") }}',
                    type: 'GET',
                    data: {
                        room_type_id: roomTypeId
                    },
                    success: function(response) {
                        roomSelect.html('<option value="">--select from list--</option>');
                        
                        if (response.rooms && response.rooms.length > 0) {
                            $.each(response.rooms, function(index, room) {
                                roomSelect.append(
                                    '<option value="' + room.room_id + '">' + 
                                    room.room_id + ' - ' + room.room_name + 
                                    '</option>'
                                );
                            });
                        } else {
                            roomSelect.append('<option value="">No rooms available</option>');
                        }
                    },
                    error: function() {
                        roomSelect.html('<option value="">Error loading rooms</option>');
                    }
                });
            }
        });

        // Auto-calculate nights when dates change
        function calculateNights() {
            const checkinDate = new Date($('input[name="checkin_date"]').val());
            const checkoutDate = new Date($('input[name="checkout_date"]').val());
            
            if (checkinDate && checkoutDate && checkoutDate > checkinDate) {
                const timeDiff = checkoutDate.getTime() - checkinDate.getTime();
                const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
                $('input[name="nights"]').val(nights);
            }
        }

        $('input[name="checkin_date"], input[name="checkout_date"]').change(calculateNights);

        // Form validation
        $('form').submit(function(e) {
            const checkinDate = new Date($('input[name="checkin_date"]').val());
            const checkoutDate = new Date($('input[name="checkout_date"]').val());
            
            if (checkoutDate <= checkinDate) {
                e.preventDefault();
                alert('Check-out date must be after check-in date');
                return false;
            }

            const adults = parseInt($('input[name="adults"]').val());
            if (adults < 1) {
                e.preventDefault();
                alert('At least one adult is required');
                return false;
            }
        });

        // Status change confirmation
        $('select[name="status"]').change(function() {
            const newStatus = $(this).val();
            if (newStatus === 'Cancelled' || newStatus === 'NoShow') {
                if (!confirm('Are you sure you want to change the status to ' + newStatus + '?')) {
                    $(this).val('{{ $booking->status ?? "Confirmed" }}');
                }
            }
        });
    });
</script>
@endsection