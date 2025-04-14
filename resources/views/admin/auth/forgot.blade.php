@extends('admin.layouts.master')
@section('title', 'Dahsboard')
@section('content')
<div class="container-fluid page-body-wrapper full-page-wrapper">
    <div class="row w-100 m-0">
        <div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
          <div class="img-box">
            <img src="{{asset('images/fram-2.png')}}" alt="" class="img-fluid img-2">
            <img src="{{asset('images/fram-1.png')}}" alt="" class="img-fluid img-1">
            <img src="{{asset('images/circle-2.png')}}" alt="" class="img-fluid img-3">
            <img src="{{asset('images/bg-circle.png')}}" alt="" class="img-fluid img-5">
            <img src="{{asset('images/circle-1.png')}}" alt="" class="img-fluid img-4">
        </div>
        <div class="card col-lg-6 login-form bg-white  mx-auto">
            <div class="card-body p-5">
              <div class="text-center">
                <img src="{{asset('images/logo.png')}}" alt="" class="img-fluid logo">
            </div>
            <h3 class="card-title text-center mb-3">Forgot Password</h3>
            <p class="card-sub-title text-center">Enter your email account to reset Password </p>
            <form action="{{route('admin-forgot-password')}}" method="post" id="admin_forgot_password" class="px-3 ">
                @csrf
                <div class="form-group">
                    <label class="title-label">Email *</label>
                    <input type="email" name="email" id="email" class="form-control p_input @error('email') is-invalid @enderror">
                    <span class="mdi mdi-email-outline eye-icon absolute"></span>
                    
                    @error('email')
                        <div class="invalid-feedback">{{$message}}</div>
                    @enderror
                </div>
               
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-block enter-btn">Continue</button>
                </div>
                <div  class="text-center">
                  <a href="{{route('login')}}">Back to Login</a>
                </div>
                
            </form>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script>
$("#admin_forgot_password").validate({
  rules: {
    email: {
      required: true,
    }
  },
  messages: {
    email: {
      required: "Email is required.",
    }
  }
});
</script>

<script>
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif
    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endsection