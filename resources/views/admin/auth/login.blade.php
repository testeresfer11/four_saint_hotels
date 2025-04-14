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
            
        <div class="card col-lg-6 login-form bg-white mx-auto">
            <div class="card-body ">
                <div class="text-center">
                    <img src="{{asset('images/logo.png')}}" alt="" class="img-fluid logo">
                </div>
                <h3 class="card-title text-center mb-3">Login</h3>
                <p class="card-sub-title text-center">Enter your Email and password details </p>
                <form action="{{route('login')}}" method="post" id="admin_login">
                    @csrf
                    <div class="form-group">
                        <label class="title-label">Email *</label>
                        <input type="email" name="email" id="email" class="form-control p_input @error('email') is-invalid @enderror">
                        <span class="mdi mdi-email-outline eye-icon absolute"></span>
                        @error('email')
                            <div class="invalid-feedback">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="title-label">Password *</label>
                        <input type="password" id="password" name="password" class="form-control p_input @error('password') is-invalid @enderror">
                        <span class="mdi mdi-eye-off-outline eye-icon absolute" id="togglePassword"></span>
                        @error('password')
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group d-flex align-items-center justify-content-between">
                        <div class="form-check">
                            <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember"> Remember me <i class="input-helper"></i></label>
                        </div>
                        <a href="{{route('admin-forgot-password')}}" class="forgot-pass">Forgot password</a>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-block enter-btn login-btn">Login</button>
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
$("#admin_login").validate({
  rules: {
    email: {
      required: true,
    },
    password: {
      required: true,
    },
  },
  messages: {
    email: {
      required: "Email is required.",
    },
    password: {
      required: "Password is required.",
    }
  }
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            if (type === 'text') {
                $('#togglePassword')
                .removeClass('mdi-eye-off-outline')
                .addClass('mdi-eye-outline');
            } else {
                $('#togglePassword')
                .removeClass('mdi-eye-outline')
                .addClass('mdi-eye-off-outline');
            }
        });
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleIcons = document.querySelectorAll(".toggle-password");

        toggleIcons.forEach(icon => {
            icon.addEventListener("click", function () {
                const targetId = this.getAttribute("data-target");
                const input = document.getElementById(targetId);

                if (!input) return;

                const isPassword = input.getAttribute("type") === "password";
                input.setAttribute("type", isPassword ? "text" : "password");

                // Toggle icon classes
                this.classList.toggle("mdi-eye-outline", !isPassword);
                this.classList.toggle("mdi-eye-off-outline", isPassword);
            });
        });
    });
</script>
@endsection