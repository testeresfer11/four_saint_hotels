<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo text-center" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('images/logo.jpg') }}" alt="logo" />
        </a>
        <a class="sidebar-brand brand-logo-mini text-center" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('images/logo.jpg') }}" alt="logo" />
        </a>
    </div>

    <ul class="nav mx-3">
        {{-- <li class="nav-item profile">
            <div class="profile-desc">
                <a href="{{ route('admin.profile') }}">
        <div class="profile-pic">
            <div class="count-indicator">
                <img class="img-xs rounded-circle" src="{{ userImageById(authId()) }}" alt="User profile picture">
                <span class="count bg-success"></span>
            </div>
            <div class="profile-name">
                <h5 class="mb-0 font-weight-normal">{{ UserNameById(authId()) }}</h5>
            </div>
        </div>
        </a>
        </div>
        </li> --}}

        @canany(['dashboard-view'])
        <!-- Dashboard Link -->
        <li class="nav-item menu-items {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <span class="menu-icon">
                    {{-- <i class="fa-solid fa-table-cells-large"></i> --}}
                    <img src="{{ asset('images/dashboard.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        @endcanany

      

        <!-- Settings Link -->
        <li class="nav-item menu-items {{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('admin.profile', 'admin.changePassword') ? '' : 'collapsed' }}"
                data-toggle="collapse" href="#settings-menu" aria-expanded="{{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'true' : 'false' }}" aria-controls="settings-menu">
                <span class="menu-icon">
                    {{-- <i class="fa-solid fa-sliders"></i> --}}
                    <img src="{{ asset('images/setting.png') }}" alt="logout" class="img-fluid">
                </span>
                <span class="menu-title">Settings</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'show' : '' }}" id="settings-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.changePassword') ? 'active' : '' }}" href="{{ route('admin.changePassword') }}">Change Password</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Roles & Permissions -->
        @canany(['role-list', 'permission-list'])
        <li class="nav-item menu-items {{ request()->routeIs('admin.role.list') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('admin.role.list') ? '' : 'collapsed' }}" data-toggle="collapse" href="#role-permissions-menu"
                aria-expanded="{{ request()->routeIs('admin.role.list') ? 'true' : 'false' }}" aria-controls="role-permissions-menu">
                <span class="menu-icon">
                    {{-- <i class="fa-regular fa-user"></i> --}}
                    <img src="{{ asset('images/user.png') }}" alt="logout" class="img-fluid">
                </span>
                <span class="menu-title">Roles & Permissions</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.role.list') ? 'show' : '' }}" id="role-permissions-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.role.list') ? 'active' : '' }}" href="{{ route('admin.role.list') }}">Roles</a>
                    </li>
                </ul>
            </div>
        </li>
        @endcanany

        <!-- User Management -->
        @canany(['user-list','user-add','user-edit','user-delete','user-view','user-change-status','user-trashed-list','user-restore','staff-list'])
        <li class="nav-item menu-items {{ request()->routeIs('admin.customer.*','admin.driver.*','admin.staff.*','admin.trashed.list') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('admin.customer.*','admin.driver.*','admin.staff.*','admin.trashed.list') ? '' : 'collapsed' }}" data-toggle="collapse" href="#service1" aria-expanded="{{ request()->routeIs('admin.customer.*','admin.driver.*','admin.staff.*','admin.trashed.list') ? 'true' : 'false' }}" aria-controls="service1">
                <span class="menu-icon">
                    {{-- <i class="fa-solid fa-users"></i> --}}
                    <img src="{{ asset('images/people.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">User Management</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.user.*','admin.staff.*','admin.trashed.list') ? 'show' : '' }}" id="service1">
                <ul class="nav flex-column sub-menu">
                    @can('user-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}" href="{{ route('admin.user.list') }}">Users</a>
                    </li>
                    @endcan

                    @can('staff-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}" href="{{ route('admin.staff.list') }}">Staff</a>
                    </li>
                    @endcan
                    @can('trashed-user-list')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.trashed.list') ? 'active' : '' }}" href="{{ route('admin.trashed.list') }}">Trashed User</a>
                    </li>
                    @endcan
                </ul>
            </div>
        </li>
        @endcanany
        <!-- Trashed User -->


        <!-- Vouchers -->
        @canany(['vouchers-list', 'vouchers-add', 'vouchers-edit', 'vouchers-delete', 'vouchers-change-status'])
        <li class="nav-item menu-items {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.vouchers.index') }}">
                <span class="menu-icon">
                    {{-- <i class="mdi mdi-gift"></i> --}}
                    <img src="{{ asset('images/gift.png') }}" alt="logout" class="img-fluid">
                </span>
<<<<<<< HEAD
                <span class="menu-title">Coupans</span>
=======
                <span class="menu-title">Coupons</span>
>>>>>>> fd6d5498800e3253463bb27f83c0fae87c89c321
            </a>
        </li>
        @endcanany
        <li class="nav-item menu-items">
            <a class="nav-link" data-bs-toggle="collapse" href="#featureSubmenu" role="button" aria-expanded="{{ request()->routeIs('admin.category.*') || request()->routeIs('admin.sub_category.*') ? 'true' : 'false' }}" aria-controls="featureSubmenu">
                <span class="menu-icon">
                    {{-- <i class="fa-solid fa-list"></i> --}}
                    <img src="{{ asset('images/features.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Feature Management</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.category.*') || request()->routeIs('admin.sub_category.*') ? 'show' : '' }}" id="featureSubmenu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item {{ request()->routeIs('admin.category.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.category.list') }}">
                            Feature
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.sub_category.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.sub_category.list') }}">
                             Sub Feature
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.other_services.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.other_services.list') }}">
                             Other Feature
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <!-- Hotel -->
        {{-- @canany(['vouchers-list', 'vouchers-add', 'vouchers-edit', 'vouchers-delete', 'vouchers-change-status'])--}}
        <li class="nav-item menu-items {{ request()->routeIs('admin.hotel.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.hotel.list') }}">
                <span class="menu-icon">
                    {{-- <i class="fa-solid fa-hotel"></i> --}}
                    <img src="{{ asset('images/hotel.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Hotel Management</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->routeIs('admin.roomtype.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.roomtype.list') }}">
                <span class="menu-icon">
                    {{-- <i class="fa-solid fa-columns"></i> --}}
                    <img src="{{ asset('images/living-room.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Room Type Management</span>
            </a>
        </li>
        
        {{-- @endcanany--}}

        <!-- Vouchers -->
        {{-- @canany(['vouchers-list', 'vouchers-add', 'vouchers-edit', 'vouchers-delete', 'vouchers-change-status'])--}}
        <li class="nav-item menu-items {{ request()->routeIs('admin.booking.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.booking.list') }}">
                <span class="menu-icon">
                    {{-- <i class="fa-regular fa-calendar-check"></i> --}}
                    <img src="{{ asset('images/tick-mark.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Booking Management</span>
            </a>
        </li>

        <li class="nav-item menu-items {{ request()->routeIs('admin.payment') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.payment.list') }}">
                <span class="menu-icon">
                {{-- <i class="fa-solid fa-sack-dollar"></i> --}}
                <img src="{{ asset('images/payment.png') }}" alt="" class="img-fluid">

                </span>
                <span class="menu-title">Payments</span>
            </a>
        </li>
        {{-- @endcanany--}}
        <!-- Vouchers -->
        {{-- @canany(['vouchers-list', 'vouchers-add', 'vouchers-edit', 'vouchers-delete', 'vouchers-change-status'])--}}
        <!-- <li class="nav-item menu-items {{ request()->routeIs('admin.service.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.service.list') }}">
                <span class="menu-icon">
                    {{-- <i class="fa-solid fa-archive"></i> --}}
                    <img src="{{ asset('images/services.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Service Management</span>
            </a>
        </li> -->

       

        {{-- @endcanany--}}

        <!-- Notifications -->

      @canany(['notification-list', 'notification-read', 'notification-delete'])
      <li class="nav-item menu-items">
        <a class="nav-link" data-bs-toggle="collapse" href="#notificationSubmenu" role="button" aria-expanded="{{ request()->routeIs('admin.notification.*') ? 'true' : 'false' }}" aria-controls="notificationSubmenu">
            <span class="menu-icon">
                <img src="{{ asset('images/notification.png') }}" alt="" class="img-fluid">
            </span>
            <span class="menu-title">Notifications</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.notification.*') ? 'show' : '' }}" id="notificationSubmenu">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item {{ request()->routeIs('admin.notification.list') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.notification.list') }}">
                        Notification List
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.notification.send') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.pushnotification.list') }}">
                        Send Push Notification
                    </a>
                </li>
            </ul>
        </div>
    </li>

@endcanany


    {{-- <li class="nav-item menu-items {{ request()->routeIs('admin.notification.send') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.pushnotification.list') }}">
            <span class="menu-icon">
                <img src="{{ asset('images/send-notification.png') }}" alt="" class="img-fluid">
            </span>
            <span class="menu-title">Send Push Notification</span>
        </a>
    </li> --}}



        <!-- Newsletter -->
        @canany(['newsletter-index', 'newsletter-delete', 'newsletter-change-status'])
        <li class="nav-item menu-items {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.newsletter.index') }}">
                <span class="menu-icon">
                    {{-- <i class="fa-solid fa-envelope-open-text"></i> --}}
                    <img src="{{ asset('images/newsletter.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Newsletter Subscribers</span>
            </a>
        </li>
        @endcanany

        <!-- Announcements -->
        @canany(['announcements-create', 'announcements-index', 'announcements-send', 'announcements-delete', 'announcements-change-status'])
        <li class="nav-item menu-items {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.announcements.index') }}">
                <span class="menu-icon">
                    {{-- <i class="fa-solid fa-bullhorn"></i> --}}
                    <img src="{{ asset('images/announce.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Announcements</span>
            </a>
        </li>
        @endcanany

        <!-- Feedback -->
        @canany(['feedback-list', 'feedback-change-status', 'feedback-delete', 'feedback-view'])
        <li class="nav-item menu-items {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.feedback.list') }}">
                <span class="menu-icon">
                    {{-- <i class="mdi mdi-comment-text"></i> --}}
                    <img src="{{ asset('images/feedback.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Feedbacks</span>
            </a>
        </li>
        @endcanany

         <li class="nav-item menu-items {{ request()->routeIs('admin.contentPages.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.contentPages.list') }}">
                <span class="menu-icon">
                    {{-- <i class="mdi mdi-comment-text"></i> --}}
                    <img src="{{ asset('images/notes.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Content Pages</span>
            </a>
        </li>

       
        <li class="nav-item menu-items {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.chat.index') }}">
                <span class="menu-icon">
                    {{-- <i class="mdi mdi-chat"></i> --}}
                    <img src="{{ asset('images/chat.png') }}" alt="" class="img-fluid">
                </span>
                <span class="menu-title">Chat</span>
            </a>
        </li>

       

        <!-- Log Out -->
        <li class="nav-item menu-items">
            <a href="javascript:void(0);" class="nav-link" data-toggle="modal" data-target="#logoutModal">
                
            {{-- <a class="nav-link" href="#"  data-toggle="modal" data-target="#logoutModal"> --}}
                <span class="menu-icon">
                    {{-- <i class="mdi mdi-logout"></i> --}}
                    <img src="{{ asset('images/logout.png') }}" alt="logout" class="img-fluid">
                </span>
                <span class="menu-title">Log Out</span>
            </a>
            
        </li>
        
    </ul>
    
</nav>
<!-- Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content px-4 py-3">
        <div class="icon-box text-center pb-3">
          <span>
            <img src="{{ asset('images/logout-img.png') }}" alt="logout" class="img-fluid">
          </span>
        </div>
        <div class="modal-body text-center">
          <h3 class="modal-title" id="logoutModalLabel">Are you sure you want to logout?</h3>
        </div>
        <div class="text-center modal-footer-btn pt-2">
          <!-- Cancel Button -->
          <a href="javascript:void(0);" class="btn btn-secondary mr-3" data-dismiss="modal">Cancel</a>
          <!-- Logout Button -->
          <a class="btn btn-logout" href="{{ route('admin.logout') }}">Logout</a>
        </div>
      </div>
    </div>
  </div>
<!-- partial -->