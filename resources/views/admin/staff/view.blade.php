@extends('admin.layouts.app')
@section('title', 'View Staff')
@section('breadcrum')
    <div class="page-header">
        <h3 class="page-title">Staff</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.staff.list') }}">Staff</a></li>
                <li class="breadcrumb-item active" aria-current="page">View Staff</li>
            </ol>
        </nav>
    </div>
@endsection
@section('content')
<div>
    <h4 class="user-title">View Staff</h4>
    <div class="card">
        <div class="card-body">
            <form class="forms-sample">
                <div class="form-group">
                    <div class="row ">
                        <div class="col-12 col-md-3">
                            <div class="view-user-details">
                                <div class="text-center">
                                    <img class="customer-image"
                                        @if (isset($user->userDetail) && !is_null($user->userDetail->profile)) 
                                            src="{{ asset('storage/images/' . $user->userDetail->profile) }}"
                                        @else
                                            src="{{ asset('admin/images/faces/face15.jpg') }}" 
                                        @endif
                                    onerror="this.src = '{{ asset('admin/images/faces/face15.jpg') }}'" alt="User profile picture">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="response-data ml-4">
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">User Id :</span> 
                                    <span class="text-muted" >{{ $user->uuid }}</span>
                                </h6>
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">Role :</span> 
                                    <span class="text-muted" >{{ $user->role ? ucfirst($user->role->name) : 'N/A' }}</span>
                                </h6>
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">Name :</span> 
                                    <span class="text-muted" >{{ $user->full_name }}</span>
                                </h6>
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">Email :</span> 
                                    <span class="text-muted" >{{ $user->email ?? '' }}</span>
                                </h6>
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">Gender :</span> 
                                    <span class="text-muted" >{{ $user->userDetail ? $user->userDetail->gender ?? 'N/A' : 'N/A' }}</span>
                                </h6>
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">Phone Number :</span>
                                    <span class="text-muted" >{{ $user->userDetail ? $user->userDetail->phone_number ?? 'N/A' : 'N/A' }}</span>
                                </h6>
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">Country Short Code :</span> 
                                    <span class="text-muted" >{{ $user->userDetail ? ($user->userDetail->country_short_code ? strtoupper($user->userDetail->country_short_code) : 'N/A') : 'N/A' }}</span>
                                </h6>
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">Date Of Birth :</span> 
                                    <span class="text-muted" >{{ $user->userDetail ? date('d/m/Y', strtotime($user->userDetail->dob)) ?? 'N/A' : 'N/A' }}</span>
                                </h6>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection
