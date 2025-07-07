@extends('admin.layouts.master')
@section('title', 'Help & Support')
@section('content')
<!-- <div class="custom-table mt-4"> -->
    <div class="help-info">
        <div class="help-heading mb-3 mt-4">
            <h3 class="p-h-color fs-clash fw-600">Help &amp; Support</h3>
        </div>
    </div>
    <div class="card col-lg-10">
        <div class="row gy-lg-0 gy-4">
            <div class="col-md-9">
                <div class="name-info d-flex">
                    <div class="p-name">
                        <p>Name</p>
                        <p>Email</p>
                        <p>Subject</p>
                        <p>Discription</p>
                        
                    </div>
                    <div class="p-n-details">
                        <p><span class="ps-3 pe-3">:</span>{{$ticket_detail->name}}</p>
                        <p><span class="ps-3 pe-3">:</span>{{$ticket_detail->email}}</p>
                        <p><span class="ps-3 pe-3">:</span>{{$ticket_detail->issue_subject}}</p>
                        <p class="d-flex"><span class="ps-3 pe-3">:</span>{{$ticket_detail->issue_description}}</p>
                    </div>
                </div>
                <div class="solution-text d-flex">
                    <p class="pe-3">Solution</p>
                    <p class="d-flex"><span class="ps-3 pe-3">:</span>{{$ticket_detail->solutions}}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="resolved-btn">
                    <button type="button" class="btn bg-71 color-71 pe-4 fs-14 rounded-10 fw-500">{{$ticket_detail->status}}</button>
                </div>
            </div>
        </div>
    </div>
<!-- </div> -->
@endsection

@section('scripts')

@endsection