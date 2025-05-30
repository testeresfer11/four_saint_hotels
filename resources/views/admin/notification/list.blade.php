@extends('admin.layouts.app')
@section('title', 'Notification')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Notification</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body p-0">
          <div class="d-flex px-3 py-3 mb-2 flex-md-row flex-column row-gap-3 justify-content-between align-items-center header-wrapper">
            <h4 class="card-title m-0">Your Notifications</h4>
            <div class="btn-box">
              <button class="btn mark">
                <i class="fa-solid fa-check-double"></i><span>Mark All as read</span>
              </button>
              <button class="btn clear">
               <span>Clear All</span>
              </button>
            </div>
            {{-- @if($notifications->count())
              <button type="button" class="btn btn-danger btn-md ClearAllNotification">
                <span class="menu-icon"> Clear All</span>
              </button>
            @endif --}}
          </div>
          {{-- <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Notification </th>
                  <th> Created At </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($notifications as $notification)
                    <tr data-id="{{$notification->id}}">
                      <td>
                        @if(isset(($notification->data)['route']))
                          <a href="{{($notification->data)['route']}}">
                            {{($notification->data)['description']}}
                          </a>
                        @else
                          {{($notification->data)['description']}}
                        @endif
                      </td>

                        <td>{{ convertDate($notification->created_at) }}</td>
                        <td>
                            <a href="javascript:void(0)" class="deleteNotification text-danger " data-id="{{$notification->id}}"> <i class="mdi mdi-delete"></i> </a>
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="6" class="no-record"> <div class="col-12 text-center">No record found </div></td>
                    </tr>
                @endforelse
              
              </tbody>
            </table>
          </div> --}}
          {{-- <div class="custom_pagination">
            {{ $notifications->appends(request()->query())->links('pagination::bootstrap-4') }}
          </div> --}}
          <div class="pt-2 pb-3">
            <div class="status py-2 px-3 mb-3">
              <h5 class="title m-0">Today</h5>
            </div>
            <div class="py-2 px-3">
              <div class="card-content p-2">
                <div class="row align-items-center">
                  <div class="col-lg-11 col-10">
                    <div class="d-flex align-items-center">
                    <span class="notifi-icon">
                      <img src="{{ asset('images/calendar.png') }}" alt="" class="img-fluid">
                    </span>
                    <div class="body-content pl-2">
                      <p class="m-0 "><b>Lorem ipsum</b> is simply dummy text of the print and typesetting industry. Lorem Ipsum has been the industry's standard dummy text. Lorem ipsum is a dummy content you can use for dummy purpose only.</p>
                      <span class="time">09:00 AM</span>
                    </div>
                  </div>
                  </div>
                  <div class="col-lg-1 col-2">
                    <button class="btn del-btn">
                      <img src="{{ asset('images/delete.png') }}" alt="" class="img-fluid">
                    </button>
                  </div>
                </div>

              </div>
            </div>
            <div class="py-2 px-3">
              <div class="card-content p-2">
                <div class="row align-items-center">
                  <div class="col-lg-11 col-10">
                    <div class="d-flex align-items-center">
                    <span class="notifi-icon">
                      <img src="{{ asset('images/calendar.png') }}" alt="" class="img-fluid">
                    </span>
                    <div class="body-content pl-2">
                      <p class="m-0 "><b>Lorem ipsum</b> is simply dummy text of the print and typesetting industry. Lorem Ipsum has been the industry's standard dummy text. Lorem ipsum is a dummy content you can use for dummy purpose only.</p>
                      <span class="time">09:00 AM</span>
                    </div>
                  </div>
                  </div>
                  <div class="col-lg-1 col-2">
                    <button class="btn del-btn">
                      <img src="{{ asset('images/delete.png') }}" alt="" class="img-fluid">
                    </button>
                  </div>
                </div>

              </div>
            </div>
          </div>
          <div class="pt-2 pb-3">
            <div class="status py-2 px-3 mb-3">
              <h5 class="title m-0">11 Oct, 2024</h5>
            </div>
            <div class="py-2 px-3">
              <div class="card-content p-2">
                <div class="row align-items-center">
                  <div class="col-lg-11 col-10">
                    <div class="d-flex align-items-center">
                    <span class="notifi-icon">
                      <img src="{{ asset('images/calendar.png') }}" alt="" class="img-fluid">
                    </span>
                    <div class="body-content pl-2">
                      <p class="m-0 "><b>Lorem ipsum</b> is simply dummy text of the print and typesetting industry. Lorem Ipsum has been the industry's standard dummy text. Lorem ipsum is a dummy content you can use for dummy purpose only.</p>
                      <span class="time">09:00 AM</span>
                    </div>
                  </div>
                  </div>
                  <div class="col-lg-1 col-2">
                    <button class="btn del-btn">
                      <img src="{{ asset('images/delete.png') }}" alt="" class="img-fluid">
                    </button>
                  </div>
                </div>

              </div>
            </div>
            <div class="py-2 px-3">
              <div class="card-content p-2">
                <div class="row align-items-center">
                  <div class="col-lg-11 col-10">
                    <div class="d-flex align-items-center">
                    <span class="notifi-icon">
                      <img src="{{ asset('images/calendar.png') }}" alt="" class="img-fluid">
                    </span>
                    <div class="body-content pl-2">
                      <p class="m-0 "><b>Lorem ipsum</b> is simply dummy text of the print and typesetting industry. Lorem Ipsum has been the industry's standard dummy text. Lorem ipsum is a dummy content you can use for dummy purpose only.</p>
                      <span class="time">09:00 AM</span>
                    </div>
                  </div>
                  </div>
                  <div class="col-lg-1 col-2">
                    <button class="btn del-btn">
                      <img src="{{ asset('images/delete.png') }}" alt="" class="img-fluid">
                    </button>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script>
  $('.deleteNotification').on('click', function() {
    var id = $(this).data('id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the Notification?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/notification/delete/" + id,
                  type: "GET", 
                  success: function(response) {
                      if (response.api_response == "success") {
                          $(`tr[data-id="${id}"]`).remove();
                          toastr.success(response.message);
                      } else {
                          toastr.error(response.message);
                      }
                  }
              });
          }
      });
  });

  $('.ClearAllNotification').on('click', function() {
      Swal.fire({
          title: "Are you sure?",
          text: "You want to clear all Notifications?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/notification/delete/clear",
                  type: "GET", 
                  success: function(response) {
                      if (response.api_response == "success") {
                          setTimeout(function(){
                            location.reload();
                          }, 1000);
                          toastr.success(response.message);
                      } else {
                          toastr.error(response.message);
                      }
                  }
              });
          }
      });
  });
</script>

@stop
