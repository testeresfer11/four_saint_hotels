@extends('admin.auth.layout')
@section('title','2FA')
@section('content')
<div class="content-wrapper full-page-wrapper d-flex align-items-center auth login-bg">
  <div class="card col-lg-4 mx-auto">
    <div class="card-body px-5 py-5">
      <h3 class="card-title text-left mb-3">Two-Factor Authentication</h3>
   
    <p>Please enter the OTP code sent to your email.</p>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.2fa.verify') }}">
        @csrf
        <div class="form-group">
            <label for="otp">One-Time Password (OTP)</label>
            <input type="text" name="code" id="otp" class="form-control" required maxlength="6">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Verify</button>
    </form>
</div>
  </div>
</div>
@endsection
