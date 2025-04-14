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
            <div class="card-body">
                <div class="text-center">
                    <img src="{{asset('images/logo.png')}}" alt="" class="img-fluid logo">
                </div>
            <h3 class="card-title text-center mb-3">Reset Password</h3>
            <p class="card-sub-title text-center">The Password must be different than before</p>
            <form action="{{route('admin-reset-password')}}" method="post" id="admin_reset_password">
                @csrf
                <input type="hidden" value="{{$token}}" name="token"> 
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
                <div class="form-group">
                    <label class="title-label">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control p_input @error('password_confirmation') is-invalid @enderror">
                    <span class="mdi mdi-eye-off-outline eye-icon absolute" id="toggleConfirmPassword"></span>
                    @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{$message}}
                        </div>
                    @enderror
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-block enter-btn">Submit</button>
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
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>


<script>
$(document).ready(function () {
    // Custom validation method for strong passwords
    jQuery.validator.addMethod("strongPassword", function(value, element) {
        return this.optional(element) || 
               /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(value);
    }, "Password must have at least 8 characters, including one uppercase, one lowercase, one number, and one special character.");

    $("#admin_reset_password").validate({
        rules: {
            password: {
                required: true,
                minlength: 8,
                strongPassword: true
            },
            password_confirmation: {
                required: true,
                equalTo: "#password" // Ensures it matches password
            }
        },
        messages: {
            password: {
                required: "Password is required.",
                minlength: "Password must be at least 8 characters long."
            },
            password_confirmation: {
                required: "Password confirmation is required.",
                equalTo: "Passwords do not match."
            }
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

    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');
   
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


    document.addEventListener('DOMContentLoaded', function () {
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const passwordField = document.querySelector('#password_confirmation');
   
        toggleConfirmPassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            if (type === 'text') {
                $('#toggleConfirmPassword')
                .removeClass('mdi-eye-off-outline')
                .addClass('mdi-eye-outline');
            } else {
                $('#toggleConfirmPassword')
                .removeClass('mdi-eye-outline')
                .addClass('mdi-eye-off-outline');
            }
        });
    });
</script>


@endsection