@extends('admin.layouts.app')
@section('title', 'View Feedback')
@section('breadcrum')
    <div class="page-header">
        <h3 class="page-title">Users</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.user.list') }}">Feedbacks</a></li>
                <li class="breadcrumb-item active" aria-current="page">View Feedback</li>
            </ol>
        </nav>
    </div>
@endsection
@section('content')
    <div>
        <h4 class="user-title">View Feedback</h4>
        <div class="card">
            <div class="card-body">
                <form class="forms-sample">
                    <div class="form-group">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-3">
                                <div class="view-user-details">
                                    {{-- <h5 class="text-center mb-2">Profile Image</h5> --}}
                                    <div class="text-center">
                                        <img class="user-image"
                                            @if (isset($feedback->user->userDetail) && !is_null($feedback->user->userDetail->profile)) src="{{ asset('storage/images/' . $feedback->user->userDetail->profile) }}"
                                @else
                                    src="{{ asset('admin/images/faces/face15.jpg') }}" @endif
                                            onerror="this.src = '{{ asset('admin/images/faces/face15.jpg') }}'"
                                            alt="User profile picture">
                                    </div>
                                </div>

                            </div>
                            <div class="col-12 col-md-8">
                              
                            
                                <div class="response-data ml-4">
                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Name :</span> 
                                      <span class="text-muted">{{ $feedback->user->full_name }}</span>
                                    </h6>

                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Email :</span> 
                                      <span class="text-muted">{{ $feedback->user->email ?? '' }}</span>
                                    </h6>

                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Message :</span> 
                                      <span class="text-muted">{{ $feedback->message ? $feedback->message ?? 'N/A' : 'N/A' }}</span>
                                    </h6>

                                  

                                   <h6 class="f-14 mb-1">
                                        <span class="semi-bold qury">Rating :</span>
                                        @php
                                            $rating = $feedback->rating ?? 0;
                                            $maxStars = 5;
                                        @endphp
                                        @for ($i = 1; $i <= $maxStars; $i++)
                                            @if ($i <= $rating)
                                                <i class="mdi mdi-star text-warning"></i>
                                            @elseif ($i - 0.5 == $rating)
                                                <i class="mdi mdi-star-half-full text-warning"></i>
                                            @else
                                                <i class="mdi mdi-star-outline text-warning"></i>
                                            @endif
                                        @endfor
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
