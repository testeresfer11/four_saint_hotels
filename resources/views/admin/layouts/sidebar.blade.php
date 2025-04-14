<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none  d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo text-center" href="{{route('dashboard')}}"><img src="{{asset('images/logo.png')}}" alt="logo"></a>
        <a class="sidebar-brand brand-logo-mini" href="{{route('dashboard')}}"><img src="{{asset('images/logo.png')}}" alt="logo"></a>
    </div>
    @php
        $user = Auth::guard('admin')->user();
    @endphp
    <ul class="nav">
        {{-- <li class="nav-item profile pt-3">
        <div class="profile-desc">
            <div class="profile-pic">
            <div class="count-indicator">
                <!-- <img class="img-xs rounded-circle " src="{{asset('assets/images/faces/face15.jpg')}}" alt=""> -->
                <!-- <span class="count bg-success"></span> -->
                @if($user->profile_pic)
                    <img class="img-xs rounded-circle" src="{{asset('storage/profile')}}/{{$user->profile_pic}}" alt="user-profile">
                @else
                    <img class="img-xs rounded-circle" src="{{asset('assets/icons/user-default.jpg')}}" alt="user-profile">
                @endif
            </div>
       
            <div class="profile-name">
                <h5 class="mb-0 font-weight-normal"> {{$user->name}}</h5>
                <!-- <span>Gold Member</span> -->
            </div>
            </div>
            <a href="#" id="profile-dropdown" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
            <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list" aria-labelledby="profile-dropdown">
            <a href="{{route('profile')}}" class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-settings text-primary side-icn"></i>
                </div>
                </div>
                <div class="preview-item-content">
                <p class="preview-subject ellipsis mb-1 text-small">Account settings</p>
                </div>
            </a>
            <div class="dropdown-divider"></div>
            <a href="{{route('change-password')}}" class="dropdown-item preview-item">
                <div class="preview-thumbnail">
                <div class="preview-icon bg-dark rounded-circle">
                    <i class="mdi mdi-onepassword  text-info side-icn"></i>
                </div>
                </div>
                <div class="preview-item-content">
                <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                </div>
            </a>
        </div>
        </div>
        </li> --}}
        {{-- <li class="nav-item nav-category">
        <span class="nav-link">Navigation</span>
        </li> --}}
        <li class="nav-item menu-items pt-3">
        <a class="nav-link" href="{{route('dashboard')}}">
            <span class="menu-icon">
            <span class="mdi mdi-view-dashboard side-icn"></span>
            </span>
            <span class="menu-title  text-truncate">Dashboard</span>
        </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{route('user-list')}}">
                <span class="menu-icon">
                    <i class="fa-solid fa-user side-icn"></i>
                </span>
                <span class="menu-title  text-truncate">Users</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{route('page-list')}}">
                <span class="menu-icon">
                    <i class="fa-solid fa-toolbox side-icn"></i>
                </span>
                <span class="menu-title  text-truncate">Content Management</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{route('subscription-list')}}">
                <span class="menu-icon">
                    <i class="fa-solid fa-circle-dollar-to-slot side-icn"></i>
                </span>
                <span class="menu-title  text-truncate">Subscriptions</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('faq-list')}}">
                <span class="menu-icon">
                    <i class="fa-solid fa-clipboard-question side-icn"></i>
                </span>
                <span class="menu-title  text-truncate">FAQs</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{route('tickets')}}">
                <span class="menu-icon">
                    <i class="fa-solid fa-headset side-icn"></i>
                </span>
                <span class="menu-title  text-truncate">Help & Support</span>
            </a>
        </li>
    </ul>
</nav>