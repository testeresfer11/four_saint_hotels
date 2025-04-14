<nav class="navbar p-0 fixed-top d-flex flex-row">
    <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
    <a class="navbar-brand brand-logo-mini" href="index.html"><img src="{{asset('assets/images/logo-mini.svg')}}" alt="logo"></a>
    </div>
    <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
        <span class="mdi mdi-chevron-left side-left-icon"></span>
    </button>
    @php
        $user = Auth::guard('admin')->user();

        $notification_count = DB::table('notifications')
                ->where('read_status', false)
                ->where('type', 'customer')
                ->count();

        $notifications = DB::table('notifications')
            ->where('read_status', false)
            ->where('type', 'customer')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();
    @endphp
    <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item ">
            {{-- <div class="input-group search-bar">
                <input type="text" class="form-control" placeholder="Search for anything" aria-label="Search for anything" aria-describedby="basic-addon1">
                <span class="input-group-text search-icon" id="basic-addon1"><i class="fa-solid fa-magnifying-glass"></i></span>
                
              </div> --}}
        </li>
       
        <li class="nav-item dropdown  a-notify-icon">
            <a class="nav-link count-indicator notifi-icon dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                <span class="mdi mdi-bell-outline d-flex align-items-center"></span>
                @if($notification_count > 0)
                    <span class="badge bg-danger notifi-count">{{ $notification_count }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown" id="header-notification-list">
                @if($notifications->isNotEmpty())
                    @foreach($notifications as $notification)
                        @php
                            if (isset($notification->title) && $notification->title == 'New Ticket') {
                                $route = route('tickets');
                            }else{
                                $route = route('user-list');
                            }
                        @endphp
                        <a class="dropdown-item preview-item notifi-dropdown-list" href="{{$route}}">
                            <div class="preview-thumbnail">
                                <div class="preview-icon bg-grey rounded-circle">
                                <i class="mdi mdi-calendar dropdown-notifi-icon"></i>
                                </div>
                            </div>
                            <div class="preview-item-content">
                                <p class="preview-subject mb-1">{{$notification->title}}</p>
                                <p class="text-muted ellipsis mb-0"> {{$notification->message}} </p>
                            </div>
                        </a>
                    @endforeach
                    {{-- <div class="dropdown-divider"></div> --}}
                    <a href="{{route('notification-list')}}" class="dropdown-item text-link-btn preview-item justify-content-center text-center" id="view-all-notifications-btn">
                        <p class="p-0 mb-0 text-link text-center">See all notifications</p>
                    </a>
                @else
                    <a class="dropdown-item preview-item notifi-dropdown-list" id="no-notifications-message">
                        <span class="text-black">No new notifications</span>
                    </a>
                @endif

            </div>
        </li>
        <li class="nav-item dropdown">
        <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
            <div class="navbar-profile">

            @if($user->profile_pic)
                <img class="img-xs rounded-circle" src="{{asset('storage/profile')}}/{{$user->profile_pic}}" alt="user-profile">
            @else
                <img class="img-xs rounded-circle" src="{{asset('assets/icons/user-default.jpg')}}" alt="user-profile">
            @endif
            <p class="mb-0 d-none d-sm-block navbar-profile-name">{{$user->name}}</p>
            <i class="mdi mdi-menu-down d-none d-sm-block"></i>
            </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown profile-dropdown preview-list" aria-labelledby="profileDropdown">
            <h6 class="p-2 mb-0">Profile</h6>
            {{-- <div class="dropdown-divider"></div> --}}
            <a class="dropdown-item preview-item notifi-dropdown-list py-2" href="{{route('profile')}}">
                <div class="preview-thumbnail">
                    <div class="bg-info-icon rounded">
                    <i class="mdi mdi-settings info-icon"></i>
                    </div>
                </div>
                <div class="preview-item-content">
                    <p class="preview-subject mb-1">Settings</p>
                </div>
            </a>
            {{-- <div class="dropdown-divider"></div> --}}
            <a class="dropdown-item preview-item notifi-dropdown-list py-2" href="{{route('change-password')}}">
                <div class="preview-thumbnail">
                    <div class="bg-info-icon rounded">
                        <i class="fa-solid fa-unlock info-icon"></i>
                    </div>
                </div>
                <div class="preview-item-content">
                    <p class="preview-subject mb-1">Change Password</p>
                </div>
            </a>
            {{-- <div class="dropdown-divider"></div> --}}
            <a class="dropdown-item preview-item notifi-dropdown-list py-2" href="{{route('admin-logout')}}">
                <div class="preview-thumbnail">
                    <div class="bg-info-icon rounded">
                    <i class="mdi mdi-logout text-danger"></i>
                    </div>
                </div>
                <div class="preview-item-content">
                    <p class="preview-subject mb-1">Log out</p>
                </div>
            </a>
        </div>
        </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
        <span class="mdi mdi-format-line-spacing"></span>
    </button>
    </div>
</nav>