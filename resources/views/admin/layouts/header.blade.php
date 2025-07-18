<nav class="navbar p-0 fixed-top d-flex flex-row">
  <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
    <a class="navbar-brand brand-logo-mini" href="index.html">
      <img src="{{ asset('images/logo.jpg') }}" alt="logo" />
    </a>
  </div>

  <div class="navbar-menu-wrapper flex-grow d-flex align-items-stretch">
    <ul class="navbar-nav navbar-nav-right">

      @php
    $selectedHotelId = session('selected_hotel_id');

   
    @endphp

  <form method="POST" action="{{ route('admin.hotel.select') }}">
      @csrf
      <select name="hotel_id" class="form-control hotel_status" onchange="this.form.submit()">
          <option value="">Select Hotel</option>
          @foreach($hotels as $hotel)
              <option value="{{ $hotel->hotel_id }}" {{ $selectedHotelId == $hotel->hotel_id ? 'selected' : '' }}>
                  {{ $hotel->name }}
              </option>
          @endforeach
      </select>
  </form>







      @php
      $notification_count = auth()->user()->unreadNotifications()->count();
      @endphp

      <!-- Notifications -->
      <li class="nav-item dropdown">
  <a class="nav-link count-indicator dropdown-toggle notifi read-notification" id="notificationDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
    <i class="fa-regular fa-bell"></i>
    @if($notification_count)
      <span class="noti-count">{{ $notification_count }}</span>
      <span class="count bg-danger"></span>
    @endif
  </a>

  @if($notification_count)
  <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
    <h6 class="p-3 mb-0">Notifications</h6>

    @foreach (auth()->user()->unreadNotifications()->take(5)->get() as $notification)
      <div class="dropdown-divider m-0"></div>
      <a class="dropdown-item preview-item" href="{{ route('admin.notification.list') }}">
        <div class="preview-item-content">
          <p class="preview-subject mb-0">{{ ($notification->data)['description'] }}</p>
        </div>
      </a>
    @endforeach

    @if(auth()->user()->unreadNotifications()->count() > 5)
      <div class="dropdown-divider"></div>
      <a class="dropdown-item text-center" href="{{ route('admin.notification.list') }}">
        See all notifications
      </a>
    @endif
  </div>
  @endif
</li>


      <!-- Profile -->
      <li class="nav-item dropdown">
        <a class="nav-link" id="profileDropdown" href="#" data-toggle="dropdown">
          <div class="navbar-profile">
            <img class="img-xs rounded-circle" src="{{ userImageById(authId()) }}" alt="User profile picture">
            <p class="mb-0 d-none d-sm-block navbar-profile-name text-capitalize">{{ UserNameById(authId()) }}</p>
            <i class="mdi mdi-menu-down d-none d-sm-block"></i>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown profile-dropdown preview-list" aria-labelledby="profileDropdown">
          {{-- <div class="dropdown-divider"></div> --}}
          <a class="dropdown-item preview-item" href="{{ route('admin.profile') }}">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-dark rounded-circle">
                <i class="fa-regular fa-user"></i>
              </div>
            </div>
            <div class="preview-item-content">
              <p class="preview-subject mb-1">Profile</p>
            </div>
          </a>
          {{-- <div class="dropdown-divider"></div> --}}
          <a class="dropdown-item preview-item" href="javascript:void(0);" data-toggle="modal" data-target="#logoutModal1">
            <div class="preview-thumbnail">
              <div class="preview-icon bg-dark rounded-circle">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
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
<div class="modal fade" id="logoutModal1" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
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