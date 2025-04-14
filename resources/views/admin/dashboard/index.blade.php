@extends('admin.layouts.master')
@section('title', 'Dahsboard')
@section('content')
<div class="content-wrapper">
<div class="row">
    <div class="col-sm-4 grid-margin">
    <div class="card">
        <div class="card-body">
        <h5>Total Users</h5>
        <div class="row">
            <div class="col-8 col-sm-12 col-xl-8 my-auto">
            <div class="d-flex d-sm-block d-md-flex align-items-center">
                <h2 class="mb-0">{{$users_count}}</h2>
            </div>
            </div>
            <div class="col-4 col-sm-12 col-xl-4 text-center text-xl-right">
            <i class="icon-lg mdi mdi-codepen text-primary ml-auto"></i>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>

</div>
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif
    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endsection