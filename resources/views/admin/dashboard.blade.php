@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Dashboard </h3>
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
        <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <a href="{{route('admin.user.list')}}">
                    <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{$responseData['total_registered_user'] ?? 0}}</h3>
                        </div>
                        </div>
                        <div class="col-3">
                        <div class="icon icon-box-success ">
                            <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Total Registered Users</h6>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <a href="{{route('admin.user.list',['status' => 1])}}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                            <h3 class="mb-0">{{$responseData['total_active_user'] ?? 0}}</h3>
                        </div>
                        </div>
                        <div class="col-3">
                        <div class="icon icon-box-success">
                            <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                        </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Total Active Users</h6>
                </div>
            </a>
        </div>
        </div>
        <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
        <div class="card">
                <a href="#">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0">${{$responseData['total_registered_user'] ?? 0}}</h3>
                            </div>
                            </div>
                            <div class="col-3">
                            <div class="icon icon-box-success ">
                                <span class="mdi mdi-arrow-top-right icon-item"></span>
                            </div>
                            </div>
                        </div>
                        <h6 class="text-muted font-weight-normal">Total Revenue Earned</h6>
                    </div>
                </a>
            </div>
        </div>
        {{--<div class="col-xl-4 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <a href="{{route('admin.transaction.list')}}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-9">
                            <div class="d-flex align-items-center align-self-start">
                                <h3 class="mb-0">${{$responseData['total_earning'] ?? 0}}</h3>
                            </div>
                            </div>
                            <div class="col-3">
                            <div class="icon icon-box-success ">
                                <span class="mdi mdi-arrow-top-right icon-item"></span>
                            </div>
                            </div>
                        </div>
                        <h6 class="text-muted font-weight-normal">Total Revenue Earned</h6>
                    </div>
                </a>
            </div>
        </div> --}}
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
</div>
@endsection
@section('scripts')
<script src="{{asset('admin/js/dashboard.js')}}"></script>
<script src="{{asset('admin/js/chart.js')}}"></script>
<script>
      
  var doughnutPieData = {
    datasets: [{
      data: <?php echo $responseData['total_registered_user']; ?>,
      backgroundColor: [
        'rgba(255, 99, 132, 0.5)',
        'rgba(54, 162, 235, 0.5)',
        'rgba(255, 206, 86, 0.5)',
        'rgba(75, 192, 192, 0.5)',
        'rgba(153, 102, 255, 0.5)',
        'rgba(255, 159, 64, 0.5)'
      ],
      borderColor: [
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
      ],
    }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: [
      "Option",
      'Confirmed',
      'CheckedIn',
      'Onboard',
      'CheckedOut'
    ]
  };
  var doughnutPieOptions = {
    responsive: true,
    animation: {
      animateScale: true,
      animateRotate: true
    }
  };

  var data = {
    labels: <?php echo $responseData['months']; ?>,
    datasets: [{
      label: '$ revenue',
      data: <?php echo $responseData['total_registered_user']; ?>,
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)'
      ],
      borderColor: [
        'rgba(255,99,132,1)',
        'rgba(54, 162, 235, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 159, 64, 1)'
      ],
      borderWidth: 1,
      fill: false
    }]
  };

  var options = {
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero: true
        },
        gridLines: {
          color: "rgba(204, 204, 204,0.1)"
        }
      }],
      xAxes: [{
        gridLines: {
          color: "rgba(204, 204, 204,0.1)"
        }
      }]
    },
    legend: {
      display: false
    },
    elements: {
      point: {
        radius: 0
      }
    }
  };

  var lineChartCanvas = $("#lineChart1").get(0).getContext("2d");
    var lineChart = new Chart(lineChartCanvas, {
      type: 'line',
      data: data,
      options: options
    });
</script>
@endsection