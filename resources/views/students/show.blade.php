@extends('layouts.app')

@section('content')
<div class="container mt-4 mt-lg-5">
    <!-- Assessment and Student Details -->
    <div class="card p-4 mb-4" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <h2 class="mb-4" style="color: #1A3C65;">Reviews for {{ $student->name }} in {{ $assessment->title }} ({{ $assessment->course->course_code }})</h2>
        <p><strong style="color: #1A3C65;">Number of Required Reviews:</strong> <span class="text-dark">{{ $assessment->num_reviews_required }}</span></p>
        <p><strong style="color: #1A3C65;">Max Score:</strong> <span class="text-dark">{{ $assessment->max_score }}</span></p>
    </div>

    <!-- Error Messages Section -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Error:</strong> {{ $errors->first() }}
        </div>
    @endif

    <!-- Submitted Reviews Section -->
    <div class="card p-4 mb-4" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <h4 style="color: #1A3C65; margin-bottom: 25px;">Reviews Submitted:</h4>
        @if ($submittedReviews->isEmpty())
            <div class="alert alert-info">This student hasn't submitted any reviews yet.</div>
        @else
            <ul class="list-group">
                @foreach ($submittedReviews as $review)
                    <li class="list-group-item mb-3" style="background-color: #E6F0FA; border: 1px solid #1A3C65;">
                        <strong style="color: #1A3C65;">Reviewee: {{ $review->reviewee->name }}</strong>
                        <p class="mt-2" style="color: #333;">{{ $review->review_text }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Received Reviews Section -->
    <div class="card p-4 mb-4" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <h4 style="color: #1A3C65; margin-bottom: 25px;">Reviews Received:</h4>
        @if ($receivedReviews->isEmpty())
            <div class="alert alert-info">{{ $student->name }} hasn't received any reviews yet.</div>
        @else
            <ul class="list-group">
                @foreach ($receivedReviews as $review)
                    <li class="list-group-item mb-3" style="background-color: #E6F0FA; border: 1px solid #1A3C65;">
                        <strong style="color: #1A3C65;">Reviewed by: {{ $review->reviewer->name }}</strong>
                        <p class="mt-2" style="color: #333;">{{ $review->review_text }}</p>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Score Assignment Section -->
    <div class="card p-4 mb-4" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <h4 style="color: #1A3C65; margin-bottom: 25px;">Assign Score for {{ $student->name }}:</h4>
        <form method="POST" action="{{ route('assessments.assign-score', ['assessment' => $assessment->id, 'student' => $student->id]) }}">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <div class="mb-3">
                <label for="score" class="form-label">Score (Max: {{ $assessment->max_score }})</label>
                <input type="number" id="score" name="score" class="form-control @error('score') is-invalid @enderror" value="{{ old('score', $score) }}">
                @error('score')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary" style="background-color: #1A3C65;">Assign Score</button>
        </form>
    </div>
</div>
@endsection
