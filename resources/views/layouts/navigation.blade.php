<nav class="navbar navbar-expand-lg" style="background-color: #1A3C65;">
  <div class="container-fluid">
    <!-- Brand Logo -->
    <a class="navbar-brand text-white" href="{{ route('home') }}" style="font-weight: bold;">
      RateMyPeer
    </a>

    <!-- Navigation Links -->
    <div class="navbar-collapse" id="navbarNav">
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
      </ul>
    </div>

    <!-- Right-Aligned User Info and Dropdown -->
    @auth
      <ul class="navbar-nav ms-auto d-flex align-items-center">
        <!-- User Icon and Name with Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="opacity: 1;">
            <i class="fas fa-user-circle" style="font-size: 1.5em;"></i>
            <!-- ucfirst(): used to capitalize the first letter -->
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
</nav>
