@extends('admin.layouts.app')
@section('title', 'Coupans')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Coupans</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Coupans</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body p-0">
        <div class="d-flex justify-content-between flex-column flex-md-row px-3 py-3 align-items-md-center align-items-start">
          <h4 class="card-title m-0">Coupans Management</h4>
          <div class="d-flex align-items-center justify-content-between">
            <div class="admin-filters">
              <x-filter />
            </div>
            <a href="{{route('admin.vouchers.sync')}}">
              <button type="button" class="btn btn-primary btn-md">
                <span class="menu-icon"><i class="fa-solid fa-plus"></i></span>
                <span class="menu-text">Get Coupan</span>
              </button>
            </a>
          </div>
        </div>
        <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Available</th>
                    <th>Expiration Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->coupon_code }}</td>
                        <td>{{ $coupon->coupon_name }}</td>
                        <td>{{ $coupon->type }}</td>
                        <td>{{ $coupon->value }}</td>
                        <td>{{ $coupon->available }}</td>
                        <td>{{ $coupon->expiration_date ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  $('.deleteBanner').on('click', function() {
    var category_id = $(this).data('id');
    Swal.fire({
        title: "Are you sure?",
        text: "You want to delete the Banner?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ea57c",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/admin/vouchers/delete/" + category_id,
                type: "GET", 
                success: function(response) {
                  if (response.status == "success") {
                      $(`tr[data-id="${category_id}"]`).remove();
                      toastr.success(response.message);
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
        text: "Do you want to change the status of the vouchers?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ea57c",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, mark as status"
    }).then((result) => {
        if (result.isConfirmed) {
          $('.preloader').show();
            $.ajax({
                url: "/admin/vouchers/changeStatus",
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

@stop
