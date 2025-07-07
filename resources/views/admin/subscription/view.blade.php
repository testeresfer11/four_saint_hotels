@extends('admin.layouts.master')
@section('title', 'subscription')
@section('content')
<div class="content-wrapper user-manage-box">
    <div class="admin-help-pending-content-box f8-bg rounded-20  border-e7">
        <div class="row gy-lg-0 gy-4">
            <div class="col-12">
                <h4 class="f-20 bold title-main">View  Subscription</h4>
                <div class="card">
                    <div class="card-body">
                        
                        <form id="edit-subscription" method="post" action="{{route('update-subscription', $subscription_detail->id)}}">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name</label>
                               <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$subscription_detail->name }}">
                                @error('name')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="android_sku_code">Android Sku Code</label>
                                <input type="text" class="form-control @error('android_sku_code') is-invalid @enderror" name="android_sku_code" id="android_sku_code" value="{{$subscription_detail->android_sku_code }}">
                                @error('android_sku_code')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="ios_sku_code">IOS Sku Code</label>
                                <input type="text" class="form-control @error('ios_sku_code') is-invalid @enderror" name="ios_sku_code" id="ios_sku_code" value="{{$subscription_detail->ios_sku_code}}">
                                @error('ios_sku_code')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description"  name="description" class="form-control text-black rounded-10  @error('description') is-invalid @enderror"  style="height: 100px"> {{$subscription_detail->description}}</textarea>       
                                @error('description')
                                    <div class="invalid-feedback">{{$message}}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary mr-2" id="submitButton">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<!-- Include jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script>
    $(document).ready(function() {
        $("textarea#description").val($.trim($("textarea#description").val()));
    });
    
    $(document).ready(function () {
        $("#add-subscription").validate({
            rules: {
                name: {
                    required: true, 
                }
            },
            messages: {
                name: {
                    required: "Name is required.",
                }
            },
            errorPlacement: function (error, element) {
                if (element.prop("tagName").toLowerCase() === "textarea") {
                    error.insertAfter(element); // Ensures error is displayed right after the textarea
                } else {
                    error.insertAfter(element);
                }
            }
        });
    });
</script>


@endsection