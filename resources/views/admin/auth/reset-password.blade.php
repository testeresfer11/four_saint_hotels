@extends('admin.auth.layout')
@section('title','Reset Password')
@section('content')
<div class="row">
  <div class="col-lg-6 auth login-bg p-0">
    {{-- <img src="{{asset('admin/images/auth/new-login-bg.png')}}" class="img-fluid" alt=""> --}}
    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
      <ol class="carousel-indicators">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner">
        <div class="carousel-item active bg-slide bg-slide-1">
          <div class="overlay"></div>
          <div class="carousel-caption  text-white">
            <h3 class="pt-4 my-2">Find Hotels Anytime,<br> Anywhere</h3>
            <p class="w-75 mx-auto">Lorem ipsum dolor sit amet consectetur. Lorem posuere at odio nullam pulvinar enim consequat at vitae. Elit ullamcorper ultrices magna malesuada erat.</p>
          </div>
        </div>
        <div class="carousel-item bg-slide bg-slide-2">
          <div class="overlay"></div>
          <div class="carousel-caption  text-white">
            <h3 class="pt-4 my-2">Find Hotels Anytime,<br> Anywhere</h3>
            <p class="w-75 mx-auto">Lorem ipsum dolor sit amet consectetur. Lorem posuere at odio nullam pulvinar enim consequat at vitae. Elit ullamcorper ultrices magna malesuada erat.</p>
          </div>
        </div>
        <div class="carousel-item bg-slide bg-slide-3">
          <div class="overlay"></div>
          <div class="carousel-caption text-white">
            <h3 class="pt-4 my-2">Find Hotels Anytime,<br> Anywhere</h3>
            <p class="w-75 mx-auto">Lorem ipsum dolor sit amet consectetur. Lorem posuere at odio nullam pulvinar enim consequat at vitae. Elit ullamcorper ultrices magna malesuada erat.</p>
          </div>
        </div>
      </div>
    
      <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
    
  </div>

  <div class="col-lg-6 bg-white px-md-5 py-md-5 px-2 py-2  d-grid justify-content-center align-items-center">
    <div class="card-body login-form px-md-5 py-md-3 px-4 py-2">
      <div class="text-center">
        <img src="{{asset('admin/images/auth/new_logo.png')}}" class="img-fluid" alt="">
        <h3 class="heading-primary">Reset Password</h3>
        <p class="grey">Enter your email and password to access your account</p>
      </div>
     
        {{-- <x-alert /> --}}
      <form action="{{ route('reset-password',['token' => $token]) }}" method="POST" id="loginForm">
          @csrf
          <div class="form-group mb-1">
              <label for="password">{{ __('Password') }} *</label>
              <div class="form-input">
                <input name="password" id="password" type="password" class="form-control  @error('password') is-invalid @enderror" autocomplete="current-password">
                <span class="togglePassword eye-icon" data-toggle="password">
                  <i class="fa fa-eye-slash"></i>
                </span>
              </div>
              @error('password')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>

          <div class="form-group mt-1 pt-2">
              <label for="password-confirm" >{{ __('Confirm Password') }}</label>
                <div class="form-input">
                  <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                  <span class="togglePassword eye-icon" data-toggle="password-confirm">
                    <i class="fa fa-eye-slash"></i>
                  </span>
                </div>
                  @error('password_confirmation')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
          </div>

        <div class="text-center">
          <button type="submit" class="btn btn-primary btn-block enter-btn">{{ __('Reset Password') }}</button>
        </div>
        
      </form>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  $('#loginForm').validate({
    rules: {
        password: {
          required: true,
          noSpace: true,
          minlength: 8,
        },
        password_confirmation: {
          required: true,
          noSpace: true,
          minlength: 8,
          equalTo: "#password",
        },
    },
    messages: {
        password: {
        required: 'Password is required.',
        minlength: 'Password length must contain 8 charcter.',
        },
        password_confirmation: {
            required: 'Confirm password is required.',
            minlength: 'Confirm password length must contain 8 charcter.',
            equalTo: "Password and confirm password must be same"
        },
    },
    submitHandler: function (form) {
      form.submit();
    }
  });
</script>
@endsection