@extends('admin.auth.layout')
@section('title','Foget Password')
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
            <h3 class="pt-5 my-2">Find Hotels Anytime,<br> Anywhere</h3>
            <p class="w-75 mx-auto">Lorem ipsum dolor sit amet consectetur. Lorem posuere at odio nullam pulvinar enim consequat at vitae. Elit ullamcorper ultrices magna malesuada erat.</p>
          </div>
        </div>
        <div class="carousel-item bg-slide bg-slide-2">
          <div class="overlay"></div>
          <div class="carousel-caption  text-white">
            <h3 class="pt-5 my-2">Find Hotels Anytime,<br> Anywhere</h3>
            <p class="w-75 mx-auto">Lorem ipsum dolor sit amet consectetur. Lorem posuere at odio nullam pulvinar enim consequat at vitae. Elit ullamcorper ultrices magna malesuada erat.</p>
          </div>
        </div>
        <div class="carousel-item bg-slide bg-slide-3">
          <div class="overlay"></div>
          <div class="carousel-caption text-white">
            <h3 class="pt-5 my-2">Find Hotels Anytime,<br> Anywhere</h3>
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
<<<<<<< HEAD
  <div class=" col-lg-6 bg-white px-5 py-5  d-grid justify-content-center align-items-center">
=======
  <div class=" col-lg-6 bg-white px-5 py-5">
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
    <div class="card-body login-form px-5 py-5">
      <div class="text-center">
        <img src="{{asset('admin/images/auth/new_logo.png')}}" class="img-fluid" alt="">
        <h1 class=" heading-primary my-3">{{ __('Reset Password') }}</h1>
        <p class="grey">Don’t worry happens to all of us. enter your email below to recover your password</p>
      </div>
        {{-- <x-alert /> --}}
      <form action="{{ route('forget-password') }}" method="POST" id="loginForm">
          @csrf
          <div class="form-group">
              <label for="email">{{ __('Email Address') }} *</label>
              <input type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>

              @error('email')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
          </div>

        <div class="text-center mt-3">
          <button type="submit" class="btn btn-primary btn-block enter-btn">{{ __('Send Password Reset Link') }}</button>
        </div>
        <div class="text-center mt-3 humb-line">
          <a href="{{route('login')}}"> Back to login </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  $(document).ready(function () {
    $('#loginForm').validate({
      rules: {
        email: {
          required: true,
          email: true
        },
      },
      messages: {
        email: {
          required: 'Please enter Email Address.',
          email: 'Please enter a valid Email Address.',
        },
      },
      submitHandler: function (form) {
        form.submit();
      }
    });
  });
</script>
@endsection