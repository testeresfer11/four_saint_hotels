@extends('admin.layouts.app')
@section('title', 'FAQ')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Content Management</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Content Management</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
         {{-- <x-alert /> --}}
         {{-- <div class="flash-message"></div> --}}
        <div class="card-body p-0">
          <div class="d-flex justify-content-between px-3 py-3 pb-0">
            <h4 class="card-title m-0">Listing</h4>
             
             
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Sr. No. </th>
                  <th> Title </th>
                  <th class="text-end pr-4"> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($faq as $key => $data)
                  <tr data-id="{{$data->id}}">
                    <td>{{++$key}}</td>
                    <td class="text-capitalize"> {{Str::limit($data->title,50, '...')}} </td>
                   
                    
                    <td class="text-end pr-4"> 
                      <span class="menu-icon ">
                        @can('contentPages-detail')
                        <a href="{{ route('admin.contentPages.detail', ['slug' => $data->slug]) }}" title="Edit" class="text-success edit-btn"><i class="fa-solid fa-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      @endcan
                      
                    </td>
                  </tr>
                @empty
                    <tr>
                      <td colspan="6" class="no-record"> <center>No record found </center></td>
                    </tr>    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="custom_pagination">
            {{ $faq->appends(request()->query())->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script>
  $('.deleteFaq').on('click', function() {
    var id = $(this).data('id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the FAQ?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/f-a-q/delete/" + id,
                  type: "GET", 
                  success: function(response) {
                    if (response.status == "success") {
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

  $('.switch').on('click', function() {
    var status = $(this).data('value');
    var action = (status == 1) ? 0 : 1;
    var id = $(this).data('id');

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to change the status of the FAQ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ea57c",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, mark as status"
    }).then((result) => {
        if (result.isConfirmed) {
          $('.preloader').show();
            $.ajax({
                url: "/admin/f-a-q/changeStatus",
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
