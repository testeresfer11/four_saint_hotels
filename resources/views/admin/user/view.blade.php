@extends('admin.layouts.app')
@section('title', 'View User')
@section('breadcrum')
    <div class="page-header">
        <h3 class="page-title">Users</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.user.list') }}">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">View User</li>
            </ol>
        </nav>
    </div>
@endsection
@section('content')
    <div>
        <h4 class="user-title">View User</h4>
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
                                            @if (isset($user->userDetail) && !is_null($user->userDetail->profile)) src="{{ asset('storage/images/' . $user->userDetail->profile) }}"
                                @else
                                    src="{{ asset('admin/images/faces/face15.jpg') }}" @endif
                                            onerror="this.src = '{{ asset('admin/images/faces/face15.jpg') }}'"
                                            alt="User profile picture">
                                    </div>
                                </div>

                            </div>
                            <div class="col-12 col-md-8">
                              <div class="align-items-right" style="text-align: right;">
                                @if($user->plan_type == "premium")
                                  <label class="badge badge-success">{{ ucfirst($user->plan_type) }}</label>
                                @else
                                  <label class="badge badge-danger">{{ ucfirst($user->plan_type) }}</label>
                                @endif
                            </div>
                            
                                <div class="response-data ml-4">
                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Name :</span> 
                                      <span class="text-muted">{{ $user->full_name }}</span>
                                    </h6>

                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Email :</span> 
                                      <span class="text-muted">{{ $user->email ?? '' }}</span>
                                    </h6>

                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Gender :</span> 
                                      <span class="text-muted">{{ $user->userDetail ? $user->userDetail->gender ?? 'N/A' : 'N/A' }}</span>
                                    </h6>

                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Date Of Birth:</span> 
                                      <span class="text-muted">{{ $user->userDetail ? $user->userDetail->dob ? strtoupper($user->userDetail->dob): 'N/A' : 'N/A' }}</span>
                                    </h6>

                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Phone Number :</span> 
                                      <span class="text-muted"  class="userPhone">{{ $user->userDetail ? $user->userDetail->phone_number ?? 'N/A' : 'N/A' }}</span>
                                    </h6>
                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Country Short Code :</span> 
                                      <span class="text-muted">{{ $user->userDetail ? $user->userDetail->country_short_code ? strtoupper($user->userDetail->country_short_code): 'N/A' : 'N/A' }}</span>
                                    </h6>

                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Address :</span> 
                                      <span class="text-muted">{{ $user->userDetail ? $user->userDetail->address ?? 'N/A' : 'N/A' }}</span>
                                    </h6>

                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Pin Code :</span>
                                       <span class="text-muted" >{{ $user->userDetail ? $user->userDetail->zip_code ?? 'N/A' : 'N/A  ' }}</span>
                                    </h6>

                                    <h6 class="f-14 mb-1">
                                      <span class="semi-bold qury">Date &amp; time :</span> 
                                      <span class="text-muted" id="userDateTime">{{ convertDate($user->created_at) }}</span>
                                    </h6>
                                    
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-5 pb-3">
          <h4 class="user-title ">Customized Boards</h4>
          @if($user->categoriesOrder()->count() > 4)
            <a href="{{route('admin.order.list',['user_id' => $user->id])}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">View All</span></button></a>
            @endif
        </div>

        <div class="row scratch-rw">
          @forelse ($user->categoriesOrder()->latest()->take(4)->get() as $data)
            <div class="col-12 col-md-3">
              <a href="{{route('admin.order.view',['id' => $data->id])}}">
                <div class="scratch-card text-center first">
                  <h6> 
                    <span class="semi-bold qury">Order Id : {{$data->uuid}}</span> <br>
                    <span class="semi-bold qury">Transaction Id :{{$data->paymentDetail ? $data->paymentDetail->payment_id : 'N/A'}}</span> <br>
                    <span class="semi-bold qury">Transaction Date : {{ convertDate($data->created_at,'d M,Y') }}</span> <br>
                    <span class="semi-bold qury">Scratched  / Total Cards : {{ $data->orderCard()->where('is_scratched',1)->count() .'/'. $data->orderCard()->count()}}</span> 
                  </h6>
                  <h3>${{$data->paymentDetail ? $data->paymentDetail->amount : 'N/A'}}.00</h3>
                </div>
              </a>
            </div>
          @empty
            <div class="col-12 col-md-12">
              <div class="text-center">
                No customized board purchased 
              </div>
            </div>
          @endforelse
        </div>

        <div class="d-flex justify-content-between mt-5 pb-3">
          <h4 class="user-title">Personalized Boards</h4>
          @if($user->personalizedOrder()->count() > 4)
            <a href="{{route('admin.order.list',['user_id' => $user->id])}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">View All</span></button></a>
            @endif
        </div>

        <div class="row personal-rw">
          @forelse ($user->personalizedOrder()->latest()->take(4)->get() as $data)
          <div class="col-12 col-md-3">
            <a href="{{route('admin.order.view',['id' => $data->id])}}">
              <div class="personal-card text-center first">
                <h6> 
                  <span class="semi-bold qury">Order Id : {{$data->uuid}}</span> <br>
                  <span class="semi-bold qury">Transaction Id :{{$data->paymentDetail ? $data->paymentDetail->payment_id : 'N/A'}}</span> <br>
                  <span class="semi-bold qury">Transaction Date : {{ convertDate($data->created_at,'d M,Y') }}</span> <br>
                  <span class="semi-bold qury">Scratched  / Total Cards : {{ $data->orderCard()->where('is_scratched',1)->count() .'/'. $data->orderCard()->count()}}</span> 
                </h6>
                <h3>${{$data->paymentDetail ? $data->paymentDetail->amount : 'N/A'}}.00</h3>
              </div>
            </a>
            </div>
          @empty
            <div class="col-12 col-md-12">
              <div class="text-center">
                No personalized board purchased 
              </div>
            </div>
          @endforelse
        </div>
    </div>
@endsection
