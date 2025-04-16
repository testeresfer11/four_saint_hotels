<!-- partial:partials/_sidebar.html -->

 <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
      <a class="sidebar-brand brand-logo" href="{{route('admin.dashboard')}}">
        <img src="{{asset('images/logo.jpg')}}" alt="logo" /> 
      </a>
      <a class="sidebar-brand brand-logo-mini" href="{{route('admin.dashboard')}}"><img src="{{asset('images/logo.jpg')}}" alt="logo" /></a>
    </div>
    <ul class="nav">
      <li class="nav-item profile">
        <div class="profile-desc">
          <a href="{{route('admin.profile')}}">
            <div class="profile-pic">
              <div class="count-indicator">
                <img class="img-xs rounded-circle" src = {{userImageById(authId())}} alt="User profile picture">
                <span class="count bg-success"></span>
              </div>
              <div class="profile-name">
                <h5 class="mb-0 font-weight-normal">{{UserNameById(authId())}}</h5>
              </div>
            </div>
          </a>
        </div>
      </li>

      <!-- Dashboard Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <span class="menu-icon">
                <i class="mdi mdi-speedometer"></i>
            </span>
            <span class="menu-title">Dashboard</span>
        </a>
      </li>

      <!-- Profile Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('admin.profile', 'admin.changePassword') ? '' : 'collapsed' }}" data-toggle="collapse" href="#ui-basic" aria-expanded="{{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'true' : 'false' }}" aria-controls="ui-basic">
            <span class="menu-icon">
                <i class="mdi mdi-settings"></i>
            </span>
            <span class="menu-title">Settings</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'show' : '' }}" id="ui-basic" data-id="{{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'true' : 'false' }}">
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

      <!-- User Management Link -->
      <li class="nav-item menu-items {{ (request()->routeIs('admin.user.*') && !request()->routeIs('admin.user.trashed.list')) ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.user.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-contacts"></i>
            </span>
            <span class="menu-title">User Management</span>
        </a>
      </li>
      
       <!-- User Management Link -->
       <li class="nav-item menu-items {{ request()->routeIs('admin.user.trashed.list') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.user.trashed.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-contacts"></i>
            </span>
            <span class="menu-title">Trashed User</span>
        </a>
      </li>
       <li class="nav-item menu-items {{ request()->routeIs('admin.transaction.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{route('admin.transaction.list')}}">
            <span class="menu-icon">
                <i class="mdi mdi-bank"></i>
            </span>
            <span class="menu-title">Transactions</span>
        </a>
      </li>

      
      <!-- Notification Management Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.notification.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{route('admin.notification.list')}}">
            <span class="menu-icon">
                <i class="mdi mdi-bell"></i>
            </span>
            <span class="menu-title">Notifications</span>
        </a>
      </li>
      
      <!-- Content Pages Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.contentPages.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('admin.contentPages.*') ? '' : 'collapsed' }}" data-toggle="collapse" href="#auth3" aria-expanded="{{ request()->routeIs('admin.card.*', 'admin.category.*') ? 'true' : 'false' }}" aria-controls="auth3">
            <span class="menu-icon">
                <i class="mdi mdi-content-save"></i>
            </span>
            <span class="menu-title">Content Pages</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.contentPages.*','admin.f-a-q.*') ? 'show' : '' }}" id="auth3">
          @php
            $currentSlug = request()->route('slug');
            $isActive = function($slug) use ($currentSlug) {
                return $currentSlug === $slug;
            };
        @endphp
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link {{ $isActive('about-us') ? 'active' : '' }}" href="{{ route('admin.contentPages.detail',['slug' => 'about-us']) }}">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $isActive('privacy-and-policy') ? 'active' : '' }}" href="{{ route('admin.contentPages.detail',['slug' => 'privacy-and-policy']) }}">Privacy And Policy</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link {{ $isActive('terms-and-conditions') ? 'active' : '' }}" href="{{ route('admin.contentPages.detail',['slug' => 'terms-and-conditions']) }}">Terms And Conditions</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link {{ $isActive('delete-account-steps') ? 'active' : '' }}" href="{{ route('admin.contentPages.detail',['slug' => 'delete-account-steps']) }}">Delete Account Steps</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link {{request()->routeIs('admin.f-a-q.*') ? 'active' : ''}}" href="{{ route('admin.f-a-q.list')}}">FAQ</a>
                </li>
            </ul>
        </div>
      </li>
      <!-- Helpdesk Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.helpDesk.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.helpDesk.list',['type' => 'open']) }}">
            <span class="menu-icon">
                <i class="mdi mdi-desktop-mac"></i>
            </span>
            <span class="menu-title">Helpdesk</span>
        </a>
      </li>
       <li class="nav-item menu-items {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.contact.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-desktop-mac"></i>
            </span>
            <span class="menu-title">Contact us</span>
        </a>
      </li>

      <!-- Config setting Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.config-setting.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('admin.config-setting.*') ? '' : 'collapsed' }}" data-toggle="collapse" href="#auth1" aria-expanded="{{ request()->routeIs('admin.config-setting.*') ? 'true' : 'false' }}" aria-controls="auth1">
            <span class="menu-icon">
                <i class="mdi mdi-settings"></i>
            </span>
            <span class="menu-title">Config Setting</span>
            <i class="menu-arrow"></i>
        </a>
         <div class="collapse {{ request()->routeIs('admin.config-setting.*') ? 'show' : '' }}" id="auth1">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.config-setting.smtp') ? 'active' : '' }}" href="{{ route('admin.config-setting.smtp') }}">SMTP Information</a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.config-setting.stripe') ? 'active' : '' }}" href="{{ route('admin.config-setting.stripe') }}">Stripe Information</a>
                </li> 
                <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('admin.config-setting.paypal') ? 'active' : '' }}" href="{{ route('admin.config-setting.paypal') }}">PayPal Information</a>
                </li>
              
            </ul>
        </div>
      </li>

      <!-- Log Out Link -->
      <li class="nav-item menu-items">
        <a class="nav-link" href="{{route('admin.logout')}}">
          <span class="menu-icon">
            <i class="mdi mdi-logout"></i>
          </span>
          <span class="menu-title">Log Out</span>
        </a>
      </li>
    </ul>
  </nav>
  <!-- partial -->