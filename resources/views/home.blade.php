@extends('layouts.app')

@section('content')
<div class="container mt-4 mt-lg-5">
    @if ($user->role === 'student')
        <h1 class="mb-3 mb-lg-5" style="color: #1A3C65;">Courses you're enrolled in:</h1>
    @elseif ($user->role === 'teacher')
        <h1 class="mb-3 mb-lg-5" style="color: #1A3C65;">Courses you're teaching:</h1>
    @endif

    @if ($courses->isEmpty())
        <div class="alert alert-info">
            You are not enrolled in or teaching any courses.
        </div>
    @else
        <!-- Table for modern layout -->
        <div class="row">
            @foreach ($courses as $course)
            <div class="col-lg-4 mb-4">
                <a href="{{ route('courses.show', $course->id) }}" class="text-decoration-none text-dark">
                    <div class="card shadow-sm h-100" style="border-left: 4px solid #1A3C65;">
                        <div class="card-body">
                            <h5 class="card-title fw-bold" style="color: #1A3C65">{{ $course->course_code }}</h5>
                            <p class="card-text">
                                <strong>Course Name: </strong> {{ $course->course_name }} <br>
                                <strong>Instructor(s): </strong> 
                                <!-- @if(!$loop->last), @endif: If loop is not at the end, it would add "," -->
                                @foreach ($course->teachers as $instructor)
                                    {{ $instructor->name }}@if(!$loop->last), @endif
                                @endforeach
                            </p>
                            <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-primary btn-sm mt-3 ">View course profile</a>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
