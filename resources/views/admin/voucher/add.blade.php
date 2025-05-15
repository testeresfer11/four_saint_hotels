@extends('admin.layouts.app')
@section('title', 'Add Vouchers')
@section('breadcrum')
<div class="page-header">
  <h3 class="page-title">Vouchers</h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item "><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{route('admin.vouchers.list')}}">Vouchers</a></li>
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
          <h4 class="card-title">Add Vouchers</h4>

          <form class="forms-sample" id="add-voucher" action="{{ route('admin.vouchers.add') }}" method="POST">
            @csrf

            <div class="form-group">
              <label for="title">Title</label>
              <input type="text" class="form-control @error('title') is-invalid @enderror"
                id="title" name="title" placeholder="Title" value="{{ old('title') }}">
              @error('title')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <label for="amount">Amount</label>
              <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror"
                id="amount" name="amount" placeholder="Enter amount" value="{{ old('amount') }}">
              @error('amount')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <label for="expiry_date">Expiry Date</label>
              <input type="date" class="form-control @error('expiry_date') is-invalid @enderror"
                id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}">
              @error('expiry_date')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror"
                id="description" name="description" placeholder="Description">{{ old('description') }}</textarea>
              @error('description')
              <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary">Add</button>
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
    $("#add-voucher").validate({
      rules: {
        title: {
          required: true,
          minlength: 3
        },
        amount: {
          required: true,
          number: true,
          min: 1
        },
        expiry_date: {
          required: true,
          date: true
        }
      },
      messages: {
        title: {
          required: "Title is required.",
          minlength: "Title must be at least 3 characters."
        },
        amount: {
          required: "Amount is required.",
          number: "Enter a valid amount.",
          min: "Amount must be at least 1."
        },
        expiry_date: {
          required: "Expiry date is required.",
          date: "Enter a valid date."
        }
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