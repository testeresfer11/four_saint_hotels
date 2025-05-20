@extends('admin.layouts.app')
@section('title', 'Booking')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Bookings</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Bookings</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="d-flex gap-2 align-items-right">
        
    </div>
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">

        <div class="card-body p-0">
            <div class="d-flex justify-content-between flex-column flex-md-row px-3 row-gap-3 py-3 align-items-md-center align-items-start">
                <h4 class="card-title m-0">Booking Management</h4>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="admin-filters">
                        <form id="filter">
                            <div class="row align-items-center justify-content-end">
                                <div class="col-6 d-flex gap-2">
                                    <input type="text" class="form-control"  placeholder="Search" name="search_keyword" value="{{request()->filled('search_keyword') ? request()->search_keyword : ''}}">            
                                </div>
                                <div class="col-3">
                                    <select class="form-control" name="status" style="width:100%">
                                        <option value="">All</option>
                                        <option value="CheckedOut" {{(request()->filled('status') && request()->status == "CheckedOut")? 'selected' : ''}}>CheckedOut</option>
                                        <option value="Confirmed" {{(request()->filled('status') && request()->status == "Confirmed")? 'selected' : ''}}>Confirmed</option>
                                        <option value="Onboard" {{(request()->filled('status') && request()->status == "Onboard")? 'selected' : ''}}>Onboard</option>

                                    </select>
                                </div>
                                <div class="col-3">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    @if(request()->filled('search_keyword') || request()->filled('status') || request()->filled('category_id'))
                                        <button class="btn btn-danger" id="clear_filter">Clear Filter</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                    <button id="fetchBookingsBtn" class="btn btn-sm btn-primary fetch-btn fetch-hotels-btn" style="">
                        <span class="fetch-icon" id="fetchBookingBtnLoader"><i class="fa-solid fa-arrows-rotate spinner-icon"></i></span>
                        <span id="fetchBookingBtnText" class="fetch-text">Fetch Bookings</span>
                        {{-- <span id="fetchBookingBtnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> --}}
                    </button>
                </div>
            </div>
          <div class="table-responsive">
           <table class="table table-striped">
        <thead>
            <tr>
                <th>Reservation Code</th>
                <th>Room</th>
                <th>Guests</th>
                <th>Prices</th>
                <th>Services</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Check-in</th>
                <th>Check-out</th>
                 <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($bookings as $booking)
            <tr>
                <td>{{ $booking->reservation_code }}</td>
                <td>
                    {{ $booking->room_type_name }}<br>
                    <small>{{ $booking->room_name }}</small>
                </td>
                <td>
                    @foreach($booking->bookingGuests as $guest)
                        {{ $guest->first_name }} {{ $guest->last_name }}<br>
                        <small>{{ $guest->email }}</small><br>
                    @endforeach
                </td>
                <td>
                    @foreach($booking->bookingPrices as $price)
                        Date: {{ $price->date }}<br>
                        Amount: {{ $price->amount }} {{ $booking->currency }}<br>
                        VAT: {{ $price->vat }}<br><br>
                    @endforeach
                </td>
                <td>
                    @forelse($booking->bookingServices as $service)
                        {{ $service->service_name }} - £{{ $service->total_price }}<br>
                    @empty
                        <em>No Services</em>
                    @endforelse
                </td>
                <td>
                    @if($booking->customer)
                        {{ $booking->customer->first_name }} {{ $booking->customer->last_name }}<br>
                        <small>{{ $booking->customer->email }}</small><br>
                        <small>{{ $booking->customer->phone_number }}</small>
                    @else
                        <em>No Customer</em>
                    @endif
                </td>
                <td>{{ $booking->status }}</td>
                <td>{{ $booking->checkin_date }}</td>
                <td>{{ $booking->checkout_date }}</td>
                 <td> 
                <span class="menu-icon">
                        <a href="{{route('admin.booking.view',['id' => $booking->id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">No bookings found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
          </div>
            <div class="custom_pagination">
              {{ $bookings->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script>
  $('.deleteUser').on('click', function() {
    var user_id = $(this).data('id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the User?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/user/delete/" + user_id,
                  type: "GET", 
                  success: function(response) {
                    if (response.status == "success") {
                       $(`tr[data-id="${user_id}"]`).remove();
                        toastr.success(response.message);
                      } else {
                        toastr.error(response.message);
                      }
                  }
              });
          }
      });
  });

  $('.changeUserSubscription').on('click', function() {
    var user_id = $(this).data('id');
      Swal.fire({
          title: "Are you sure?",
          text: "Do you want to change the user subscription from Basic to Premium?",
          icon: "info",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, proceed it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/user/changeSubscription/" + user_id,
                  type: "GET", 
                  success: function(response) {
                    if (response.status == "success") {
                      toastr.success(response.message);
                       setTimeout(() => {
                        location.reload();
                       }, 1000);
                      } else {
                        toastr.error(response.message);
                      }
                  }
              });
          }
      });
  });

  $('.switch').on('click', function() {
    var status = $(this).data('value');
    var action = (status == 1) ? 0 : 1;
    var id = $(this).data('id');

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to change the status of the user?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ea57c",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, mark as status"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/admin/user/changeStatus",
                type: "GET",
                data: { id: id, status: action },
                success: function(response) {
                    if (response.status == "success") {
                        toastr.success(response.message);
                        $('.switch[data-id="' + id + '"]').data('value',!action);
                    } else {
                        $('.switch[data-id="' + id + '"]').data('value',action);
                        toastr.error(response.message);
                    }
                },
                error: function(error) {
                    console.log('error', error);
                }
            });
        } else {
          var switchToToggle = $('.switch[data-id="' + id + '"]');
          switchToToggle.prop('checked', !switchToToggle.prop('checked'));
        }
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
    const fetchBookingsUrl = @json(route('admin.booking.get'));

    document.getElementById('fetchBookingsBtn').addEventListener('click', function () {
        const fetchText = document.getElementById('fetchBookingBtnText');
        const fetchLoader = document.getElementById('fetchBookingBtnLoader');

        // Show loader, hide text
        // fetchText.classList.add('d-none');
        // fetchLoader.classList.remove('d-none');

        // Get today's date in YYYY-MM-DD format
        const today = new Date().toISOString().split('T')[0];

        const params = new URLSearchParams({
            hotel_id: 8618, // change this if dynamic
            start_date: today, // you can change this if needed
            end_date: today,
            extended_list: 1,
            services: 1,
            guest_details: 1
        });

        fetch(`${fetchBookingsUrl}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                // Show text, hide loader
                fetchText.classList.remove('d-none');
                fetchLoader.classList.add('d-none');

                if (data.status_code ==200) {
                    toastr.success(data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    toastr.error(data.message || "Failed to fetch bookings.");
                }
            })
            .catch(error => {
                fetchText.classList.remove('d-none');
                fetchLoader.classList.add('d-none');
                toastr.error("Something went wrong: " + error.message);
            });
    });
</script>

@endsection



