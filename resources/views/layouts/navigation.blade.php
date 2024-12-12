<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #1A3C65;">
  <div class="container-fluid">
    <!-- Brand Logo -->
    <a class="navbar-brand text-white" href="{{ route('home') }}" style="font-weight: bold;">
      RateMyPeer
    </a>

    <!-- Hamburger Menu (Collapsed Navbar Button for Smaller Screens) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="border-color: rgba(255, 255, 255, 0.5);">
      <span class="navbar-toggler-icon" style="background-image: url('data:image/svg+xml;charset=utf8,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 30 30%27%3E%3Cpath stroke=%27rgba(255, 255, 255, 1)%27 stroke-width=%272%27 d=%27M4 7h22M4 15h22M4 23h22%27/%3E%3C/svg%3E');"></span>
    </button>


    <!-- Collapsible Navigation Links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Header Section for User Info (Visible in Hamburger Menu) -->
      @auth
        <div class="d-lg-none text-white mt-3 mb-2 pb-1 border-bottom">
          <i class="fas fa-user-circle" style="font-size: 1.5em;"></i>
          <span class="ms-2">{{ ucfirst(Auth::user()->name) }} ({{ ucfirst(Auth::user()->role) }})</span>
        </div>
      @endauth


      <ul class="navbar-nav me-auto">
        <!-- Home -->
        <li class="nav-item">
          <a class="nav-link text-white {{ Request::is('/') ? 'active' : '' }}" href="{{ route('home') }}">
            Home
          </a>
        </li>

        <!-- Top Reviewers Page -->
        <li class="nav-item">
          <a class="nav-link text-white {{ Request::is('students/top-reviewers') ? 'active' : '' }}" href="{{ route('students.top-reviewers') }}">
            Top Reviewers
          </a>
        </li>

        <!-- File Upload Page (Visible to Teachers Only) -->
        @if(Auth::check() && Auth::user()->role == 'teacher')
          <li class="nav-item">
            <a class="nav-link text-white {{ Request::is('courses/create') ? 'active' : '' }}" href="{{ route('courses.create') }}">
              Upload Course
            </a>
          </li>
        @endif

        <!-- Logout (For Screens Smaller than lg) -->
      @auth
        <li class="nav-item d-lg-none">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link btn btn-link text-white text-start logout-btn">
              {{ __('Log Out') }}
            </button>
          </form>
        </li>
      @endauth
      </ul>

      <!-- Right-Aligned User Info and Dropdown -->
      @auth
        <ul class="navbar-nav ms-auto d-flex align-items-center d-none d-lg-block">
          <!-- User Icon and Name with Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="opacity: 1;">
              <i class="fas fa-user-circle" style="font-size: 1.5em;"></i>
              <span class="ms-4">{{ ucfirst(Auth::user()->name) }} ({{ ucfirst(Auth::user()->role) }})</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <!-- Logout -->
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">
                    {{ __('Log Out') }}
                  </button>
                </form>
              </li>
            </ul>
          </li>
        </ul>
      @endauth
    </div>
  </div>
</nav>
