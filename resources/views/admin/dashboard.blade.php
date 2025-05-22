@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('breadcrum')
@php
    $hour = now()->format('H');
  
    if ($hour >= 5 && $hour < 12) {
        $greeting = 'Good Morning';
    } elseif ($hour >= 12 && $hour < 17) {
        $greeting = 'Good Afternoon';
    } elseif ($hour >= 17 && $hour < 21) {
        $greeting = 'Good Evening';
    } else {
        $greeting = 'Good Night';
    }
@endphp

<h2 class="main-title">{{ $greeting }}, {{ auth()->user()->full_name }}!</h2>

<div class="page-header">
   
    <h5 class="m-0 page-title">Booking Summary</h5>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div>
    <div class="row">
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <a href="{{route('admin.user.list')}}">
                    <div class="card-body">
                        <div class="icon icon-box-success ">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                         <h3 class="my-2 count-text">{{$responseData['total_bookings'] ?? 0}}</h3>
                        <h6 class="glove-text  font-weight-normal">Total Booking</h6>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <a href="{{route('admin.user.list',['status' => 1])}}">
                    <div class="card-body">
                        <div class="icon icon-box-success">
                            <i class="fa-solid fa-check"></i>
                        </div>
                         <h3 class="my-2 count-text">{{$responseData['CheckedIn'] ?? 0}}</h3>
                        <h6 class="glove-text  font-weight-normal">CheckedIn Booking</h6>
                    </div>
                </a>
            </div>
        </div>
         <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <a href="{{route('admin.user.list',['status' => 1])}}">
                    <div class="card-body">
                        <div class="icon icon-box-success">
                            <i class="fa-solid fa-check"></i>
                        </div>
                         <h3 class="my-2 count-text">{{$responseData['total_Option'] ?? 0}}</h3>
                        <h6 class="glove-text  font-weight-normal">Cancelled Booking</h6>
                    </div>
                </a>
            </div>
        </div>
          <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <a href="{{route('admin.user.list',['status' => 1])}}">
                    <div class="card-body">
                        <div class="icon icon-box-success">
                            <i class="fa-solid fa-check"></i>
                        </div>
                         <h3 class="my-2 count-text">{{$responseData['total_confirmed'] ?? 0}}</h3>
                        <h6 class="glove-text  font-weight-normal">Option Booking</h6>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card">
                <a href="#">
                    <div class="card-body">
                        <div class="icon icon-box-success ">
                            <i class="fa-solid fa-ellipsis"></i>
                        </div>
                        <h3 class="my-2 count-text">{{$responseData['total_onboared'] ?? 0}}</h3>
                        <h6 class="glove-text  font-weight-normal">OnBoard Booking</h6>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card">
                <a href="#">
                    <div class="card-body">
                        <div class="icon icon-box-success ">
                            <i class="fa-solid fa-ellipsis"></i>
                        </div>
                        <h3 class="my-2 count-text">{{$responseData['total_checkedOut'] ?? 0}}</h3>
                        <h6 class="glove-text  font-weight-normal">CheckedOut Booking</h6>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <a href="{{route('admin.transaction.list')}}">
                    <div class="card-body">
                        <div class="icon icon-box-success ">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <h3 class="my-2 count-text">${{$responseData['total_earning'] ?? 0}}</h3>
                        <h6 class="glove-text font-weight-normal">Revenue</h6>
                    </div>
                </a>
            </div>
        </div>
  
    
    </div>
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Booking chart</h4>
                <canvas id="pieChart" style="height:250px"></canvas>
            </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Earning chart</h4>
                <canvas id="lineChart1" style="height:250px"></canvas>
            </div>
            </div>
        </div>
    </div>
   
<div class="row">
  <div class="col-12 grid-margin">
    <h5 class="mb-3 page-title">Recent Bookings</h5>
    <table class="table">
  <thead>
    <tr>
      <th>Res. Code</th>
      <th>Guest Name</th>
      <th>Guest Email</th>
      <th>Check‑In</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($bookings as $booking)
      @foreach($booking->bookingGuests as $guest)
        <tr>
          <td>{{ $booking->reservation_code }}</td>
          <td>{{ $guest->first_name }} {{ $guest->last_name }}</td>
          <td>{{ $guest->email }}</td>
          <td>{{ \Carbon\Carbon::parse($booking->checkin_date)->format('d M Y') }}</td>
          <td>{{ $booking->status }}</td>
        </tr>
      @endforeach
    @empty
      <tr>
        <td colspan="5" class="text-center">No bookings found.</td>
      </tr>
    @endforelse
  </tbody>
</table>

  </div>
</div>

@endsection
@section('scripts')
<script src="{{asset('admin/js/dashboard.js')}}"></script>
<script src="https://unpkg.com/@adminkit/core@latest/dist/js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const bookingStatusData = {
    option: {{ $responseData['total_Option'] }},
    confirmed: {{ $responseData['total_confirmed'] }},
    checkedIn: {{ $responseData['total_checkedIn'] }},
    onboard: {{ $responseData['total_onboared'] }},
    checkedOut: {{ $responseData['total_checkedOut'] }},
    
  };
  const doughnutPieData = {
    labels: ["Cancelled", "Confirmed", "CheckedIn", "Onboard", "CheckedOut"],
    datasets: [{
      data: [
      bookingStatusData.option,
      bookingStatusData.confirmed,
      bookingStatusData.checkedIn,
      bookingStatusData.onboard,
      bookingStatusData.checkedOut
    ],
      backgroundColor: [
        'rgba(255, 99, 132, 0.5)',
        'rgba(54, 162, 235, 0.5)',
        'rgba(255, 206, 86, 0.5)',
        'rgba(75, 192, 192, 0.5)',
        'rgba(153, 102, 255, 0.5)'
      ],
      borderColor: [
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)'
      ],
      borderWidth: 1
    }]
  };

  const doughnutPieOptions = {
    responsive: true,
    animation: {
      animateScale: true,
      animateRotate: true
    }
  };

  const pieChartCanvas = document.getElementById("pieChart").getContext("2d");
  new Chart(pieChartCanvas, {
    type: 'doughnut',
    data: doughnutPieData,
    options: doughnutPieOptions
  });

  const lineChartCanvas = document.getElementById("lineChart1").getContext("2d");
  new Chart(lineChartCanvas, {
    type: 'line',
    data: {
      labels: {!! $responseData['months'] !!},
      datasets: [{
        label: 'Monthly Earnings ($)',
        data: {!! $responseData['monthly_earnings'] !!},
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        fill: true,
        borderWidth: 2,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: true
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 200
          }
        }
      }
    }
  });

  const doughnutCanvas = document.getElementById("chartjs-doughnut").getContext("2d");
  new Chart(doughnutCanvas, {
    type: "doughnut",
    data: {
      labels: ["Social", "Search Engines", "Direct", "Other"],
      datasets: [{
        data: [260, 125, 54, 146],
        backgroundColor: [
          window.theme.primary || "#007bff",
          window.theme.success || "#28a745",
          window.theme.warning || "#ffc107",
          "#dee2e6"
        ],
        borderColor: "transparent"
      }]
    },
    options: {
      maintainAspectRatio: false,
      cutout: "65%",
    }
  });
</script>
@endsection
