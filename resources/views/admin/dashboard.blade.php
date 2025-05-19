@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('breadcrum')
<h2 class="main-title">Good Morning, Gavano !</h2>
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
                        <h3 class="my-2 count-text">{{$responseData['total_registered_user'] ?? 0}}</h3>
                        <h6 class="glove-text font-weight-normal">Active Bookings</h6>
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
                        <h3 class="my-2 count-text">{{$responseData['total_active_user'] ?? 0}}</h3>
                        <h6 class="glove-text  font-weight-normal">Confirmed Booking</h6>
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
                        <h3 class="my-2 count-text">${{$responseData['total_registered_user'] ?? 0}}</h3>
                        <h6 class="glove-text  font-weight-normal">Pending Booking</h6>
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
       {{--<div class="col-xl-4 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <a href="{{route('admin.category.list')}}">
                    <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{$responseData['total_category'] ?? 0}}</h3>
                        </div>
                        </div>
                        <div class="col-3">
                        <div class="icon icon-box-success ">
                            <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Total Categories </h6>
                    </div>
                </a>
            </div>
        </div>--}}
        {{-- <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <a href="{{route('admin.card.list')}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0">{{$responseData['total_cards'] ?? 0}}</h3>
                            </div>
                            </div>
                            <div class="col-3">
                            <div class="icon icon-box-success ">
                                <span class="mdi mdi-arrow-top-right icon-item"></span>
                            </div>
                            </div>
                        </div>
                        <h6 class="text-muted font-weight-normal">Total Scratch Cards on the Platform  </h6>
                    </div>
                </a>
            </div>
        </div> --}}
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
      <div class="col-md-7 grid-margin">
        <h5 class="mb-3 page-title">Pending Payments</h5>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">User Name</th>
              <th scope="col">email</th>
              <th scope="col">amount</th>
              <th scope="col">Due Date</th>
              <th scope="col">Status</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td scope="row">Henry</td>
              <td>example12@gmail.com</td>
              <td>$100</td>
              <td>25 May 2025</td>
              <td><span class="status pnd-status">Pending</span></td>
              <td><i class="fa-solid fa-ellipsis"></i></td>
            </tr>
            <tr>
              <td scope="row">Henry</td>
              <td>example12@gmail.com</td>
              <td>$100</td>
              <td>25 May 2025</td>
              <td><span class="status pnd-status">Pending</span></td>
              <td><i class="fa-solid fa-ellipsis"></i></td>
            </tr>
            <tr>
              <td scope="row">Henry</td>
              <td>example12@gmail.com</td>
              <td>$100</td>
              <td>25 May 2025</td>
              <td><span class="status pnd-status">Pending</span></td>
              <td><i class="fa-solid fa-ellipsis"></i></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-5 grid-margin">
        <h5 class="mb-3 page-title">Pending Payments</h5>
        <div class="container text-center mt-5" style="width: 300px;">
          <canvas id="chartjs-doughnut"></canvas>
          <div id="chartCenterText">Bookings</div>
        </div>
    </div>
</div>
<div class="row">
  <div class="col-12 grid-margin">
    <h5 class="mb-3 page-title">Recent Bookings</h5>
    <table class="table">
      <thead>
        <tr>
          <th scope="col">User Name</th>
          <th scope="col">email</th>
          <th scope="col">amount</th>
          <th scope="col">Due Date</th>
          <th scope="col">Status</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td scope="row">Henry</td>
          <td>example12@gmail.com</td>
          <td>$100</td>
          <td>25 May 2025</td>
          <td><span class="status pnd-status">Pending</span></td>
          <td><i class="fa-solid fa-ellipsis"></i></td>
        </tr>
        <tr>
          <td scope="row">Henry</td>
          <td>example12@gmail.com</td>
          <td>$100</td>
          <td>25 May 2025</td>
          <td><span class="status paid-status">Paid</span></td>
          <td><i class="fa-solid fa-ellipsis"></i></td>
        </tr>
        <tr>
          <td scope="row">Henry</td>
          <td>example12@gmail.com</td>
          <td>$100</td>
          <td>25 May 2025</td>
          <td><span class="status pnd-status">Pending</span></td>
          <td><i class="fa-solid fa-ellipsis"></i></td>
        </tr>
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
  const doughnutPieData = {
    labels: ["Option", "Confirmed", "CheckedIn", "Onboard", "CheckedOut"],
    datasets: [{
      data: [10, 20, 15, 30, 25], // Replace with dynamic booking statuses if needed
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
