@extends('layouts.guest')

@section('content')
<section class="vh-100 d-flex align-items-center" style="background-color: #1A3C65;">
  <div class="container d-flex justify-content-center align-items-center h-100">
    <div class="col col-xl-6">
      <div class="card shadow-lg" style="border-radius: 1rem;">
        <div class="card-body p-5 text-black">

          <form method="POST" action="{{ route('register') }}">
              @csrf

            <!-- Register Icon and Title -->
            <div class="text-center mb-4">
              <i class="fas fa-user-plus fa-3x" style="color: #1A3C65;"></i>
              <h1 class="h3 fw-bold mt-2">Sign Up</h1>
            </div>

            <!-- Name Field -->
            <div class="form-outline mb-4">
              <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control form-control-lg @error('name') is-invalid @enderror" autofocus placeholder="Enter your name"/>
              @error('name')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>

            <!-- Email Field -->
            <div class="form-outline mb-4">
              <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Enter your email"/>
              @error('email')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>

            <!-- S-Number Field -->
            <div class="form-outline mb-4">
              <input type="text" id="s_number" name="s_number" value="{{ old('s_number') }}" class="form-control form-control-lg @error('s_number') is-invalid @enderror" placeholder="Enter your S-number"/>
              @error('s_number')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>

            <!-- Password Field -->
            <div class="form-outline mb-4">
              <input type="password" id="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Create a password" />
              @error('password')
                  <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                  </span>
              @enderror
            </div>

            <!-- Confirm Password Field -->
            <div class="form-outline mb-4">
              <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Confirm your password"/>
            </div>

            <!-- Register Button -->
            <div class="d-grid gap-2 mb-4">
              <button class="btn btn-dark btn-lg btn-block" type="submit">Sign Up</button>
            </div>

            <!-- Login Link -->
            <div class="text-center mt-3">
              <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary">Log in here</a></p>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
@endsection
