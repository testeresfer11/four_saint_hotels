@extends('admin.layouts.app')
@section('title', 'Feedbacks')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Feedbacks</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Feedback</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body p-0">
          <div class="d-flex justify-content-between flex-column flex-md-row px-3 row-gap-3 py-3 align-items-md-center align-items-start">
            <h4 class="card-title m-0">Feedback Management</h4>
            
              <div class="admin-filters">
                <x-filter />
              </div>

             
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                 <th>  </th>
                  <th> User </th>
                  <th> Feedback </th>
                  <th> Rating </th>
                  <th> Status </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($feedbacks as $feedback)
                  <tr data-id="{{$feedback->id}}">
                    <td class="py-1">
                      <img src="{{userImageById($feedback->user->id)}}" onerror="this.src = '{{ asset('admin/images/faces/face15.jpg') }}'"
                      alt="User profile picture">
                    </td>
                    <td> {{$feedback->user->full_name}} </td>
                    <td> {{Str::limit($feedback->message,50, '...')}} </td>

                    <td>{{$feedback->rating}}</td>
                    
                    <td> <div class="toggle-user dark-toggle">
                      <input type="checkbox" name="is_active" data-id="{{$feedback->id}}" class="switch" @if ($feedback->status == 1) checked @endif data-value="{{$feedback->status}}">

                    </div> </td>
                    <td> 
                     
                      <span class="menu-icon">
                        <a href="{{route('admin.feedback.view',['id' => $feedback->id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp;&nbsp;
                      
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteUser" data-id="{{$feedback->id}}"><i class="mdi mdi-delete"></i></a>
                      </span> 
                    </td>
                  </tr>
                @empty
                    <tr>
                      <td colspan="6" class="no-record"> <center>No record found </center></td>
                    </tr>
                @endforelse
              </tbody>
            </table>
          </div>
            <div class="custom_pagination">
              {{ $feedbacks->appends(request()->query())->links('pagination::bootstrap-4') }}
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
          text: "You want to delete the Feedback?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/feedback/delete/" + user_id,
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

  
   

  $('.switch').on('click', function() {
    var status = $(this).data('value');
    var action = (status == 1) ? 0 : 1;
    var id = $(this).data('id');
    
    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to change the status of the Feedback?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ea57c",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, mark as status"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/admin/feedback/changeStatus",
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
