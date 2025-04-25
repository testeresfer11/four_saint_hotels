@extends('admin.layouts.app')
@section('title', 'Edit Role')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Banners</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{route('admin.role.list')}}">Role</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
      </ol>
    </nav>
</div>
@endsection
@section('content')
<div>
    <div class="row justify-content-center">
      <div class="col-5 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Edit Role</h4>
             
            <form class="forms-sample" id="edit-banner" action="{{route('admin.role.edit',['id' => $role->id])}}" method="POST" enctype="multipart/form-data">
              @csrf
              
                <div class="form-group">
                    <div class="row">
                        <label for="exampleInputTitle">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="exampleInputTitle" placeholder="Name" name="name" value="{{$role->name ?? ''}}">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>  
                </div>
              
              <button type="submit" class="btn btn-primary mr-2" >Update</button>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
  $(document).ready(function() {
    
    $("#edit-banner").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            name: {
                required: true,
                noSpace: true,
                minlength: 3,
            }
          
        },
        messages: {
            name: {
                required: "Name is required.",
                minlength: "Name must consist of at least 3 characters."
            },
           
        },
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            if (element.prop('type') === 'file') {
                error.appendTo(element.closest('.row'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

});

  </script>
@stop