@extends('admin.layouts.app')
@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.6.1/build/css/intlTelInput.css">
@endsection
@section('title', 'Add User')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Users</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{route('admin.user.list')}}">Users</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add User</li>
      </ol>
    </nav>
</div>
@endsection
@section('content')
<div>
    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title ">Add User</h4>
             
            <form class="forms-sample add-form" id="add-user" action="{{route('admin.user.add')}}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="exampleInputFirstName">First Name</label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="exampleInputFirstName" placeholder="First Name" name="first_name">
                        @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputLastName">Last Name</label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="exampleInputLastName" placeholder="Last Name" name="last_name">
                        @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="exampleInputEmail">Email address</label>
                        <input type="email" class="form-control  @error('email') is-invalid @enderror" id="exampleInputEmail" placeholder="Email" name="email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="col-6">
                        <label for="exampleInputGender">Gender</label>
                        <select name="gender" id="exampleInputGender" class="form-control  @error('gender') is-invalid @enderror" >
                            <option value="" disabled selected hidden>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('gender')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                </div>
              </div>
              <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="dob">Date Of Birth</label>
                        <input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob"  name ="dob" max="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}">
                        @error('dob')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6 select_country_code">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone" placeholder="Phone Number" name="phone_number" value="">
                        <input type="hidden" name="country_code" value="">
                        <input type="hidden" name="country_short_code" value="us">
                        @error('phone_number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                </div> 
              </div>

              <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="address">Address</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" placeholder="Address" name = "address">
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputPinCode">Pin Code</label>
                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="exampleInputPinCode" placeholder="Pin Code" name="zip_code">
                        @error('zip_code')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                    </div>  
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="password">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password" name = "password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col-6">
                        <label>Profile upload</label>
                          <input type="file" name="profile" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*">
                    </div>
                </div>
              </div>
              
<div class="text-end">
              <button type="submit" class="btn btn-primary mr-2">Add</button>
              {{-- <button class="btn btn-dark">Cancel</button> --}}
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>    
@endsection
@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZ09dtOd8YHF_ZCbfbaaMHJKiOr26noY8&libraries=places" ></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.6.1/build/js/intlTelInput.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector("#phone");
        const iti = window.intlTelInput(input, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.6.1/build/js/utils.js",
            initialCountry: "us",
            formatOnDisplay: false,  
            nationalMode: false,    
        });

        
        document.querySelector("#phone").addEventListener("change", function(event) {
            // Get the selected country data
            const countryData = iti.getSelectedCountryData();
            
            // Get the raw phone number
            let phoneNumber = iti.getNumber("e164");
            phoneNumber = phoneNumber.replace(/\D/g, '');
           
            // Set hidden fields with country code and short code
            document.querySelector("input[name='country_code']").value = countryData.dialCode;
            document.querySelector("input[name='country_short_code']").value = countryData.iso2;

            input.value = phoneNumber;
        });
    });
</script>

<script>
  $(document).ready(function() {

    var err = false;
    var autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('address'), {
            types: ['geocode']
        }
    );
    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        var postalCode = '';
        if (place.address_components) {
            for (var i = 0; i < place.address_components.length; i++) {
                var component = place.address_components[i];
                if (component.types.includes('postal_code')) {
                    postalCode = component.long_name;
                    break;
                }
            }
            $('#exampleInputPinCode').val(postalCode);
        }
    });

    $("#add-user").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            first_name: {
                required: true,
                noSpace: true,
                minlength: 3,
                maxlength:25,
            },
            last_name: {
                required: true,
                noSpace: true,
                minlength: 3,
                maxlength:25
            },
            email: {
                required: true,
                email: true,
                noSpace: true,
            },
            phone_number: {
                number: true,
                // minlength:10,
                maxlength: 12,
            },
            gender: {
                required: true,
            },
            password: {
                minlength: 6,
            },
        },
        messages: {
            first_name: {
                required: "First name is required",
                minlength: "First name must consist of at least 3 characters",
                maxlength: "First name must not contains more then 25 characters."
            },
            last_name: {
                required: "Last name is required",
                minlength: "Last name must consist of at least 3 characters",
                maxlength: "Last name must not contains more then 25 characters."
            },
            email: {
                required: 'Email is required.',
                email: "Please enter a valid email address"
            },
            phone_number: {
                number: 'Only numeric value is acceptable',
                minlength:  'Phone number must be 10 digits',
                maxlength:  'Phone number must be 10 digits'
            },
            gender: {
                required: 'Please select gender.',
            },
            password: {
                minlength: "Password must consist of at least 6 characters"
            },
        },
        submitHandler: function(form) {
            form.submit();
        }

    });
  });
  </script>
@stop