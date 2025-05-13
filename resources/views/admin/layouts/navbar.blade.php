<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('images/logo.jpg') }}" alt="logo" />
        </a>
        <a class="sidebar-brand brand-logo-mini" href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('images/logo.jpg') }}" alt="logo" />
        </a>
    </div>

    <ul class="nav ml-3">
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
                    <i class="fa-solid fa-table-cells-large"></i>
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
                    <i class="mdi mdi-settings"></i>
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
                    <i class="mdi mdi-account"></i>
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
                <i class="mdi mdi-settings"></i>
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
            <a class="nav-link" href="{{ route('admin.vouchers.list') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-gift"></i>
                </span>
                <span class="menu-title">Vouchers</span>
            </a>
        </li>
        @endcanany
        <!-- Hotel -->
        {{-- @canany(['vouchers-list', 'vouchers-add', 'vouchers-edit', 'vouchers-delete', 'vouchers-change-status'])--}}
        <li class="nav-item menu-items {{ request()->routeIs('admin.hotel.*') ? 'active' : '' }}">
            <a class="nav-link" href="#">
                <span class="menu-icon">
                    <i class="mdi mdi-gift"></i>
                </span>
                <span class="menu-title">Hotel Mangemanet</span>
            </a>
        </li>
       {{-- @endcanany--}}

         <!-- Vouchers -->
        {{-- @canany(['vouchers-list', 'vouchers-add', 'vouchers-edit', 'vouchers-delete', 'vouchers-change-status'])--}}
        <li class="nav-item menu-items {{ request()->routeIs('admin.booking.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.booking.list') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-gift"></i>
                </span>
                <span class="menu-title">Booking Mangemanet</span>
            </a>
        </li>
       {{-- @endcanany--}}
        <!-- Vouchers -->
        {{-- @canany(['vouchers-list', 'vouchers-add', 'vouchers-edit', 'vouchers-delete', 'vouchers-change-status'])--}}
        <li class="nav-item menu-items {{ request()->routeIs('admin.service.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.service.list') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-gift"></i>
                </span>
                <span class="menu-title">Service Mangemanet</span>
            </a>
        </li>
       {{-- @endcanany--}}

        <!-- Notifications -->
        @canany(['notification-list', 'notification-read', 'notification-delete'])
        <li class="nav-item menu-items {{ request()->routeIs('admin.notification.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.notification.list') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-bell"></i>
                </span>
                <span class="menu-title">Notifications</span>
            </a>
        </li>
        @endcanany

        <!-- Newsletter -->
        @canany(['newsletter-index', 'newsletter-delete', 'newsletter-change-status'])
        <li class="nav-item menu-items {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.newsletter.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-contacts"></i>
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
                    <i class="mdi mdi-contacts"></i>
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
                    <i class="mdi mdi-comment-text"></i>
                </span>
                <span class="menu-title">Feedbacks</span>
            </a>
        </li>
        @endcanany

        <!-- Content Pages -->
        <li class="nav-item menu-items {{ request()->routeIs('admin.contentPages.*') ? 'active' : '' }}">
            <a class="nav-link {{ request()->routeIs('admin.contentPages.*') ? '' : 'collapsed' }}" data-toggle="collapse" href="#contentPages" aria-expanded="{{ request()->routeIs('admin.contentPages.*') ? 'true' : 'false' }}" aria-controls="contentPages">
                <span class="menu-icon">
                    <i class="mdi mdi-content-save"></i>
                </span>
                <span class="menu-title">Content Pages</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.contentPages.*') ? 'show' : '' }}" id="contentPages">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.contentPages.detail') ? 'active' : '' }}" href="{{ route('admin.contentPages.detail', ['slug' => 'about-us']) }}">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.contentPages.detail') ? 'active' : '' }}" href="{{ route('admin.contentPages.detail', ['slug' => 'privacy-and-policy']) }}">Privacy And Policy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.contentPages.detail') ? 'active' : '' }}" href="{{ route('admin.contentPages.detail', ['slug' => 'terms-and-conditions']) }}">Terms And Conditions</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Helpdesk -->
        @can('helpdesk-list')
        <li class="nav-item menu-items {{ request()->routeIs('admin.helpDesk.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.helpDesk.list', ['type' => 'open']) }}">
                <span class="menu-icon">
                    <i class="mdi mdi-desktop-mac"></i>
                </span>
                <span class="menu-title">Helpdesk</span>
            </a>
        </li>
        @endcanany

        <!-- Log Out -->
        <li class="nav-item menu-items">
            <a class="nav-link" href="{{ route('admin.logout') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-logout"></i>
                </span>
                <span class="menu-title">Log Out</span>
            </a>
        </li>
    </ul>
</nav>
<!-- partial -->
