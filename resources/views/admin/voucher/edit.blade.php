@extends('admin.layouts.app')
@section('title', 'Edit Voucher')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Vouchers</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{route('admin.vouchers.index')}}">Vouchers</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Voucher</li>
      </ol>
    </nav>
</div>
@endsection

@section('content')
<div>
<form action="{{ route('admin.vouchers.edit', ['id' => $voucher->id]) }}" method="POST" id="edit-voucher">
    @csrf
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Voucher</h4>

                        {{-- Coupon Code --}}
                        <div class="mb-3">
                            <label for="coupon_code" class="form-label">Coupon Code</label>
                            <input type="text" 
                                   class="form-control @error('coupon_code') is-invalid @enderror" 
                                   id="coupon_code" 
                                   name="coupon_code" 
                                   value="{{ old('coupon_code', $voucher->coupon_code) }}">
                            @error('coupon_code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Coupon Name --}}
                        <div class="mb-3">
                            <label for="coupon_name" class="form-label">Coupon Name</label>
                            <input type="text" 
                                   class="form-control @error('coupon_name') is-invalid @enderror" 
                                   id="coupon_name" 
                                   name="coupon_name" 
                                   value="{{ old('coupon_name', $voucher->coupon_name) }}">
                            @error('coupon_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Type --}}
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-control @error('type') is-invalid @enderror" 
                                    id="type" name="type">
                                <option value="">-- Select Type --</option>
                                <option value="Fixed" {{ old('type', $voucher->type) == 'Fixed' ? 'selected' : '' }}>Fixed</option>
                                <option value="Percentage" {{ old('type', $voucher->type) == 'Percentage' ? 'selected' : '' }}>Percentage</option>
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Value --}}
                        <div class="mb-3">
                            <label for="value" class="form-label">Value</label>
                            <input type="number" step="0.01"
                                   class="form-control @error('value') is-invalid @enderror"
                                   id="value" name="value"
                                   value="{{ old('value', $voucher->value) }}">
                            @error('value')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Available --}}
                        <div class="mb-3">
                            <label for="available" class="form-label">Available</label>
                            <select class="form-control @error('available') is-invalid @enderror" name="available">
                                <option value="">-- Select Availability --</option>
                                <option value="once" {{ old('available', $voucher->available) == 'once' ? 'selected' : '' }}>Once</option>
                                <option value="unlimited" {{ old('available', $voucher->available) == 'unlimited' ? 'selected' : '' }}>Unlimited</option>
                            </select>
                            @error('available')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Expiration Date --}}
                        <div class="mb-3">
                            <label for="expiration_date" class="form-label">Expiration Date</label>
                            <input type="date" 
                                   class="form-control @error('expiration_date') is-invalid @enderror" 
                                   id="expiration_date" 
                                   name="expiration_date" 
                                   value="{{ old('expiration_date', $voucher->expiration_date) }}">
                            @error('expiration_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Max Uses --}}
                        <div class="mb-3">
                            <label for="max_uses" class="form-label">Max Uses</label>
                            <input type="number" 
                                   class="form-control @error('max_uses') is-invalid @enderror" 
                                   id="max_uses" 
                                   name="max_uses" 
                                   value="{{ old('max_uses', $voucher->max_uses) }}">
                            @error('max_uses')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Update Voucher</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $("#edit-voucher").validate({
        rules: {
            coupon_code: { required: true, minlength: 3, maxlength: 255 },
            coupon_name: { required: true, minlength: 3, maxlength: 255 },
            type: { required: true },
            value: { required: true, number: true, min: 0 },
            expiration_date: { date: true },
            max_uses: { number: true, min: 1 }
        },
        messages: {
            coupon_code: { required: "Coupon code is required" },
            coupon_name: { required: "Coupon name is required" },
            type: { required: "Select a type" },
            value: { required: "Value is required", number: "Enter a valid number" },
        },
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            error.insertAfter(element);
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
