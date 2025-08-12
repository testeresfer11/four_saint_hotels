@extends('admin.layouts.app')
@section('title', 'Add Coupon')

@section('breadcrum')
<div class="page-header">
  <h3 class="page-title">Coupons</h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.vouchers.index') }}">Coupons</a></li>
      <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
  </nav>
</div>
@endsection

@section('content')
<div>
  <div class="row justify-content-center">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Add Coupon</h4>

          <form class="forms-sample" id="add-coupon" action="{{ route('admin.vouchers.add') }}" method="POST">
            @csrf

            {{-- Hotel ID --}}
            <div class="form-group">
              <label for="hotel_id">Hotel</label>
              <select class="form-control @error('hotel_id') is-invalid @enderror" id="hotel_id" name="hotel_id">
                <option value="">Select Hotel</option>
                @foreach($hotels as $hotel)
                  <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>
                    {{ $hotel->name }}
                  </option>
                @endforeach
              </select>
              @error('hotel_id')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            {{-- Coupon Code --}}
            <div class="form-group">
              <label for="coupon_code">Coupon Code</label>
              <input type="text" class="form-control @error('coupon_code') is-invalid @enderror"
                id="coupon_code" name="coupon_code" placeholder="Enter coupon code" value="{{ old('coupon_code') }}">
              @error('coupon_code')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            {{-- Coupon Name --}}
            <div class="form-group">
              <label for="coupon_name">Coupon Name</label>
              <input type="text" class="form-control @error('coupon_name') is-invalid @enderror"
                id="coupon_name" name="coupon_name" placeholder="Enter coupon name" value="{{ old('coupon_name') }}">
              @error('coupon_name')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            {{-- Type --}}
            <div class="form-group">
              <label for="type">Type</label>
              <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                <option value="">Select Type</option>
                <option value="Fixed" {{ old('type') == 'Fixed' ? 'selected' : '' }}>Fixed</option>
                <option value="Percentage" {{ old('type') == 'Percentage' ? 'selected' : '' }}>Percentage</option>
              </select>
              @error('type')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            {{-- Value --}}
            <div class="form-group">
              <label for="value">Value</label>
              <input type="number" step="0.01" class="form-control @error('value') is-invalid @enderror"
                id="value" name="value" placeholder="Enter value" value="{{ old('value') }}">
              @error('value')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            {{-- Available --}}
           <div class="form-group">
              <label for="available">Available</label>
              <select class="form-control @error('available') is-invalid @enderror" 
                      id="available" 
                      name="available">
                  <option value="">Select Availability</option>
                  <option value="Once" {{ old('available') == 'once' ? 'selected' : '' }}>Once</option>
                  <option value="Unlimited" {{ old('available') == 'unlimited' ? 'selected' : '' }}>Unlimited</option>
              </select>
              @error('available')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
          </div>

            {{-- Expiration Date --}}
            <div class="form-group">
              <label for="expiration_date">Expiration Date</label>
              <input type="date" class="form-control @error('expiration_date') is-invalid @enderror"
                id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}">
              @error('expiration_date')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            {{-- Max Uses --}}
            <div class="form-group">
              <label for="max_uses">Max Uses</label>
              <input type="number" class="form-control @error('max_uses') is-invalid @enderror"
                id="max_uses" name="max_uses" placeholder="Max usage limit" value="{{ old('max_uses') }}">
              @error('max_uses')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group text-end">
              <button type="submit" class="btn btn-primary">Add Coupon</button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    $("#add-coupon").validate({
      rules: {
        hotel_id: { required: true },
        coupon_code: { required: true },
        coupon_name: { required: true, minlength: 3 },
        type: { required: true },
        value: { required: true, number: true, min: 0.01 },
        available:{ required: true}
        expiration_date: { date: true },
        max_uses: { number: true, min: 1 }
      },
      messages: {
        hotel_id: { required: "Please select a hotel." },
        coupon_code: { required: "Coupon code is required." },
        coupon_name: { required: "Coupon name is required.", minlength: "At least 3 characters." },
        type: { required: "Please select a type." },
        value: { required: "Value is required.", number: "Enter a valid number.", min: "Value must be positive." },
       
        expiration_date: { date: "Enter a valid date." },
        max_uses: { number: "Must be a number.", min: "At least 1." }
      },
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function(element) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function(element) {
        $(element).removeClass('is-invalid');
      }
    });
  });
</script>
@endsection
