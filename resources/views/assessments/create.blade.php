@extends('layouts.app')

@section('content')
<div class="container mt-4 mt-lg-5">
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h2 class="mb-4" style="color: #1A3C65;">Create New Peer Review Assessment for {{ $course->course_name }} ({{ $course->course_code }})</h2>

    <div class="card p-4 mb-4 shadow-sm" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <form action="{{ route('assessments.store') }}" method="POST">
            @csrf
            <!-- Pass the course_id as a hidden field -->
            <input type="hidden" name="course_id" value="{{ $course->id }}">

            <!-- Assessment Title -->
            <div class="mb-3">
                <label for="title" class="form-label">Assessment Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title') }}">
                <small class="form-text text-muted">Maximum 20 characters.</small>
                @error('title')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Instructions -->
            <div class="mb-3">
                <label for="instruction" class="form-label">Instruction</label>
                <textarea 
                    id="instruction" 
                    name="instruction" 
                    class="form-control @error('instruction') is-invalid @enderror" 
                    rows="4" >{{ old('instruction') }}</textarea>
                @error('instruction')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Number of Reviews Required -->
            <div class="mb-3">
                <label for="num_reviews_required" class="form-label">Number of Reviews Required</label>
                <input 
                    type="number" 
                    id="num_reviews_required" 
                    name="num_reviews_required" 
                    class="form-control @error('num_reviews_required') is-invalid @enderror" 
                    value="{{ old('num_reviews_required') }}">
                @error('num_reviews_required')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Maximum Score -->
            <div class="mb-3">
                <label for="max_score" class="form-label">Maximum Score</label>
                <input 
                    type="number" 
                    id="max_score" 
                    name="max_score" 
                    class="form-control @error('max_score') is-invalid @enderror" 
                    value="{{ old('max_score') }}">
                @error('max_score')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Due Date -->
            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input 
                    type="date" 
                    id="due_date" 
                    name="due_date" 
                    class="form-control @error('due_date') is-invalid @enderror" 
                    value="{{ old('due_date') }}">
                @error('due_date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Assessment Type -->
            <div class="mb-3">
                <label for="type" class="form-label">Assessment Type</label>
                <select 
                    id="type" 
                    name="type" 
                    class="form-select @error('type') is-invalid @enderror">
                    <!-- <option value="" disabled selected>Choose assessment type</option> -->
                    <option value="student-select" {{ old('type') == 'student-select' ? 'selected' : '' }}>Student Select</option>
                    <option value="teacher-assign" {{ old('type') == 'teacher-assign' ? 'selected' : '' }}>Teacher Assign</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            <!-- Submit Button -->
            <div class="d-flex justify-content-center mt-3 mt-lg-5">
                <button type="submit" class="btn btn-primary" style="background-color: #1A3C65;">Create Assessment</button>
            </div>
        </form>
    </div>
</div>
@endsection
