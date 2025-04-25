@extends('admin.auth.layout')
@section('title','Verify OTP')
@section('content')
<div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
  <div class="card col-lg-4 mx-auto">
    <div class="card-body px-5 py-5">
      <h3 class="card-title text-left mb-3">Verify Code</h3>
      <p>An authentication code has been sent to your email address.</p>

      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif


     <form method="POST" action="{{ route('verify') }}">
        @csrf
        <div class="form-group">
            <input type="hidden" name="email" value="{{ $email }}">

            <label for="otp">Enter Code</label>
            <input type="text" name="otp" id="otp" class="form-control" required maxlength="6">
        </div>

        <div class="mt-2">
            <small>Didn’t receive a code? 
                <a href="{{ route('resend') }}" class="text-danger">Resend</a>
            </small>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Continue</button>
    </form>

    </div>
  </div>
</div>
@endsection

