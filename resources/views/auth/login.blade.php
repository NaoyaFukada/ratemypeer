@extends('layouts.guest')

@section('content')
<section class="vh-100 d-flex align-items-center" style="background-color: #1A3C65;">
  <div class="container d-flex justify-content-center align-items-center h-100">
    <div class="col col-xl-6">
      <div class="card shadow-lg" style="border-radius: 1rem;">
        <div class="card-body p-5 text-black">

          <form method="POST" action="{{ route('login') }}">
              @csrf

            <!-- Login Icon and Title -->
            <div class="text-center mb-lg-5 mb-4">
              <i class="fas fa-user-circle fa-4x" style="color: #1A3C65;"></i>
              <h1 class="h2 fw-bold mt-3">Login</h1>
            </div>

            <!-- S-Number Field -->
            <div class="form-outline mb-4">
              <input type="text" id="s_number" name="s_number" value="{{ old('s_number') }}" class="form-control form-control-lg @error('s_number') is-invalid @enderror" autofocus placeholder="Enter your S-number"/>
              @error('s_number')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>

            <!-- Password Field -->
            <div class="form-outline mb-4">
              <input type="password" id="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Enter your password" />
              @error('password')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>

            <!-- Remember Me Checkbox -->
            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" name="remember" id="remember">
              <label class="form-check-label" for="remember">
                  Remember me
              </label>
            </div>

            <!-- Login Button -->
            <div class="d-grid gap-2 mb-4">
              <button class="btn btn-dark btn-lg btn-block" type="submit">Login</button>
            </div>
            
            <!-- Sign up Link -->
            <div class="text-center mt-3">
              <p class="mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-primary">Sign up here</a></p>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
@endsection
