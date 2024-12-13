@extends('layouts.app')

@section('content')
<div class="container mt-4 mt-lg-5">
    <!-- Error Message Display -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>There were some errors with your submission:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2 class="mb-4 text-center" style="color: #1A3C65;">Create a New Course</h2>

    <!-- Explanation Section -->
    <div class="alert alert-info">
        <h5 class="fw-bold">File Format for Upload</h5>
        <p>Please ensure the file you upload follows this exact format:</p>
        <pre style="background-color: #f7f9fc; padding: 15px; border-radius: 10px;">
COURSE_CODE:
CS101
COURSE_NAME:
Introduction to Computer Science
TEACHERS:
s1234567
s7654321
ASSESSMENTS:
Assignment 1|Complete the tasks given|2|100|2024-12-01|student-select
Assignment 2|Peer review assignment|3|100|2024-12-15|teacher-assign
STUDENTS:
s1230001
s1230002
        </pre>
        <p>The format must include the course code, course name, teacher IDs (s_numbers), assessments, and student IDs (s_numbers). Each section must be clearly labeled as shown above.</p>
        <ul>
            <li><strong>COURSE_CODE</strong> - The unique code for the course.</li>
            <li><strong>COURSE_NAME</strong> - The name of the course.</li>
            <li><strong>TEACHERS</strong> - The s_numbers of the teachers.</li>
            <li><strong>ASSESSMENTS</strong> - Include assessment title, instructions, number of reviews, max score, due date (Y-m-d format), and type (either student-select or teacher-assign).</li>
            <li><strong>STUDENTS</strong> - The s_numbers of students enrolled in the course.</li>
        </ul>
    </div>

    <!-- File Upload Form -->
    <div class="card shadow-sm p-4 mb-4" style="border: 2px solid #1A3C65; background-color: #F7F9FC;">
        <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="course_file" class="form-label fw-bold" style="color: #1A3C65;">Upload Course File (.txt)</label>
                <div class="custom-file">
                    <input type="file" name="course_file" class="custom-file-input @error('course_file') is-invalid @enderror" id="customFile" accept=".txt" required>
                </div>
                @error('course_file')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                <small class="form-text text-muted">Please upload a .txt file following the format mentioned above.</small>
            </div>

            <!-- Action Button -->
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg" style="background-color: #1A3C65; font-weight: bold;">
                    Create Course
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
