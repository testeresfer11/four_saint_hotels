@extends('admin.layouts.app')
@section('title', 'Edit User')
@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.6.1/build/css/intlTelInput.css">
@endsection
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Users</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>  
        <li class="breadcrumb-item"><a href="{{route('admin.user.list')}}">Users</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
            <h4 class="card-title">Edit User</h4>
             
            <form class="forms-sample" id="Edit-User" action="{{route('admin.user.edit',['id' => $user->id])}}" method="POST" enctype="multipart/form-data">
              @csrf
              
              <div class="form-group">
                <div class="row">
                    
                </div>
                <div class="row">
                    <div class="col-6">
                        <label for="exampleInputFirstName">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="exampleInputFirstName" placeholder="Full Name" name="full_name" value="{{$user->full_name ?? ''}}">
                        @error('full_name')
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
                        <label for="exampleInputEmail">Email address<span class="text-danger">*</span></label>
                        <input type="email" class="form-control  @error('email') is-invalid @enderror" id="exampleInputEmail" placeholder="Email" name="email" value="{{$user->email ?? ''}}" readonly>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputGender">Gender <span class="text-danger">*</span></label>
                        <select name="gender" id="exampleInputGender" class="form-control  @error('gender') is-invalid @enderror" >
                            <option value="">Select Gender</option>
                            <option value="Male" {{$user->userDetail ? (($user->userDetail->gender == 'Male' ) ? 'selected': '') : ''}}>Male</option>
                            <option value="Female" {{$user->userDetail ? (($user->userDetail->gender == 'Female' ) ? 'selected': '') : ''}}>Female</option>
                            <option value="Other" {{$user->userDetail ? (($user->userDetail->gender == 'Other' ) ? 'selected': '') : ''}}>Other</option>
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
                        <input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob"  name = "dob" value = "{{$user->userDetail ? ($user->userDetail->dob ? ($user->userDetail->dob) : '') : ''}}" max="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}">
                        @error('dob')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6 select_country_code">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone" placeholder="Phone Number" name="phone_number" value="{{$user->userDetail ? ($user->userDetail->phone_number ?? '') : ''}}">
                        <input type="hidden" name="country_code" value="">
                        <input type="hidden" name="country_short_code" value="{{$user->userDetail ? ($user->userDetail->country_short_code ?? 'us') : 'us'}}">
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
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" placeholder="Address" name = "address" value = {{$user->userDetail ? ($user->userDetail->address ?? '') : ''}}>
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputPinCode">Pin Code</label>
                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="exampleInputPinCode" placeholder="Pin Code" name="zip_code" value = {{$user->userDetail ?($user->userDetail->zip_code ?? '') : ''}}>
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
            <div class="col-12">
                <label for="profile">Profile upload</label>
                <input type="file" name="profile" class="form-control file-upload-info"
                       accept="image/*" onchange="previewImage(event)">
                
                <!-- Preview Section -->
                <div class="img-preview mt-3">
                    <img id="preview" 
                        src="@if(isset($user->userDetail) && $user->userDetail->profile) 
                                {{$user->userDetail->profile }} 
                             @else 
                                {{ asset('admin/images/faces/face15.jpg') }} 
                             @endif" 
                        alt="Profile Preview" 
                        
                        style="max-width:150px; height:150px; object-fit:cover;" 
                        onerror="this.src='{{ asset('admin/images/faces/face15.jpg') }}'">
                </div>
            </div>
        </div>

        <script>
        function previewImage(event) {
            let reader = new FileReader();
            reader.onload = function(){
                let output = document.getElementById('preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
        </script>


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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZ09dtOd8YHF_ZCbfbaaMHJKiOr26noY8&libraries=places" ></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.6.1/build/js/intlTelInput.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector("#phone");

    // Get initial country code from hidden input or default
    const initialCountry = document.querySelector("input[name='country_short_code']").value || "us";

    const iti = window.intlTelInput(input, {
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.6.1/build/js/utils.js",
        initialCountry: initialCountry,
        formatOnDisplay: false,
        nationalMode: false,
    });

    // On load, set hidden fields if there is a prefilled number
    if (input.value) {
        const countryData = iti.getSelectedCountryData();
        document.querySelector("input[name='country_code']").value = countryData.dialCode;
        document.querySelector("input[name='country_short_code']").value = countryData.iso2;
    }

    // Update hidden fields whenever the input loses focus
    input.addEventListener("blur", function() {
        const countryData = iti.getSelectedCountryData();
        document.querySelector("input[name='country_code']").value = countryData.dialCode;
        document.querySelector("input[name='country_short_code']").value = countryData.iso2;
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

    $("#Edit-User").submit(function(e){
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
                maxlength:25,
            },
            email: {
                required: true,
                email: true
            },
            phone_number: {
                number: true,
                // minlength:10,
                maxlength: 12,
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
                email: "Please enter a valid email address"
            },
            phone_number: {
                number: 'Only numeric value is acceptable',
                minlength:  'Phone number must be 10 digits',
                maxlength:  'Phone number must be 10 digits'
            },
        },
        submitHandler: function(form) {
          form.submit();
      }

    });
  });
  </script>
@stop