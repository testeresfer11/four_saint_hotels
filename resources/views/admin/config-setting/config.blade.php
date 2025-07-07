@extends('admin.layouts.app')
@section('title', 'Config')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Config Setting</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">General Information</li>
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
            <h4 class="card-title">General Information</h4>
             
            <form class="forms-sample" id="config-information" action="{{route('admin.config-setting.config')}}" method="POST">
              @csrf
              <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="exampleInputCardLimit">Card Limit</label>
                        <input type="number" class="form-control @error('CARD_LIMIT') is-invalid @enderror" id="exampleInputCardLimit" min=0 max=99 placeholder="Card Limit" name = "CARD_LIMIT" value="{{$configDetail['CARD_LIMIT'] ?? ''}}">
                        @error('CARD_LIMIT')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputQuestionLimit">Question Limit</label>
                        <input type="number" class="form-control @error('QUESTION_LIMIT') is-invalid @enderror" id="exampleInputQuestionLimit" placeholder="Question Limit" name = "QUESTION_LIMIT" min=0 max=99 value="{{$configDetail['QUESTION_LIMIT'] ?? ''}}">
                        @error('QUESTION_LIMIT')
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
                    <label for="exampleInputPriceCategorized">Price ( Categorized Board)</label>
                    <input type="number" class="form-control @error('PRICE_CATEGORIZED') is-invalid @enderror" min=0 max=9999 id="exampleInputPriceCategorized" placeholder="Price" name = "PRICE_CATEGORIZED" value="{{$configDetail['PRICE_CATEGORIZED'] ?? ''}}">
                    @error('PRICE_CATEGORIZED')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                  <div class="col-6">
                    <label for="exampleInputPricePersonalized">Price ( Personalized Board)</label>
                    <input type="number" class="form-control @error('PRICE_PERSONALIZED') is-invalid @enderror" id="exampleInputPricePersonalized" min=0 max=9999 placeholder="Price" name = "PRICE_PERSONALIZED" value="{{$configDetail['PRICE_PERSONALIZED'] ?? ''}}">
                    @error('PRICE_PERSONALIZED')
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
                    <label for="exampleInputBoardExpiry">Board Expiry Time ( in days )</label>
                    <input type="number" class="form-control @error('BOARD_EXPIRY') is-invalid @enderror" id="exampleInputBoardExpiry" min=0 max=9999 placeholder="Price" name = "BOARD_EXPIRY" value="{{$configDetail['BOARD_EXPIRY'] ?? ''}}">
                    @error('BOARD_EXPIRY')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary mr-2">Update</button>
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
    $("#config-information").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            CARD_LIMIT: {
                required: true,
                number: true,
                maxlength:2
            },
            QUESTION_LIMIT: {
                required: true,
                number: true,
                maxlength:2
            },
            PRICE_CATEGORIZED:{
              required: true,
              number: true,
              maxlength:4
            },
            PRICE_PERSONALIZED:{
              required: true,
              number: true,
              maxlength:4
            },
            BOARD_EXPIRY:{
              required: true,
              number: true,
              maxlength:3
            }
        },
        messages: {
            CARD_LIMIT: {
              required: "Card limit is required",
              number: "Only numeric value is acceptable",
              maxlength: "Card limit must not be greater than 2 digits"
            },
            QUESTION_LIMIT: {
              required: "Question limit is required",
              number: "Only numeric value is acceptable",
              maxlength: "Question limit must not be greater than 2 digits"
            },
            PRICE_CATEGORIZED: {
              required: "Price is required",
              number: "Only numeric value is acceptable",
              maxlength: "Price must not be greater than 4 digits"
            },
            PRICE_PERSONALIZED: {
              required: "Price is required",
              number: "Only numeric value is acceptable",
              maxlength: "Price must not be greater than 4 digits"
            },
            BOARD_EXPIRY: {
              required: "Board expiry is required",
              number: "Only numeric value is acceptable",
              maxlength: "Board expirymust not be greater than 3 digits"
            },
        },
        submitHandler: function(form) {
            form.submit();
        }

    });
  });
  </script>
@stop