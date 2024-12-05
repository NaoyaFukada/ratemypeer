@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Assessment Title -->
    <div class="card p-4 mb-4" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <h2 class="mb-4" style="color: #1A3C65;">{{ $assessment->title }} for {{ $course->course_name }} ({{ $course->course_code }})</h2>
        <!-- Instructions for the assessment -->
        <div class="mb-5">
            <h4 class="mb-3" style="color: #1A3C65;">Instructions:</h4>
            <p>{{ $assessment->instruction }}</p>
            <p><strong style="color: #1A3C65;">Number of Required Reviews:</strong> <span class="text-dark">{{ $assessment->num_reviews_required }}</span></p>
            <p><strong style="color: #1A3C65;">Max Score:</strong> <span class="text-dark">{{ $assessment->max_score }}</span></p>
            <p><strong style="color: #1A3C65;">Due Date:</strong> <span class="text-dark">{{ $assessment->due_date->format('d M Y') }}</span></p>
            <p><strong style="color: #1A3C65;">Assessment Type:</strong> <span class="text-dark">{{ ucfirst(str_replace('-', ' ', $assessment->type)) }}</span></p>
        </div>
    </div>


    <!-- Review Submission Section -->
    <div class="card p-4 mb-4" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <h4 style="color: #1A3C65; margin-bottom: 25px;">Submit Your Peer Reviews:</h4>

        <!-- Informational Message -->
        @if (session('info'))
            <div class="alert alert-info">
                {{ session('info') }}
            </div>
        @endif

        <!-- Personalized Message based on Ranking -->
        @if (isset($studentRank) && $studentRank !== null)
            <div class="alert 
                @if ($percentile <= 10)
                    alert-success  <!-- Green for top 10% -->
                @elseif ($percentile <= 50)
                    alert-warning  <!-- Yellow for top 50% -->
                @else
                    alert-danger   <!-- Red for bottom 50% -->
                @endif
            ">
                <strong>Position: </strong> You are ranked {{ $studentRank }} out of {{ $totalStudents }} students.
                <br>
                @if ($percentile <= 10)
                    <strong>Great job!</strong> You are in the top 10%. Keep up the excellent work!
                @elseif ($percentile <= 50)
                    <strong>Good effort!</strong> You are in the top 50%. Keep striving for the top!
                @else
                    <strong>Keep going!</strong> You are in the bottom half, but with more effort, you can rise higher!
                @endif
            </div>
        @else
            <div class="alert alert-info">
                <strong>Ranking Information:</strong> No ranking information is available for you at the moment. 
                You may need to submit more reviews or wait until you receive scores to see your rank.
            </div>
        @endif

        <!-- Check if the student has already submitted the required number of reviews -->
        <form method="POST" action="{{ route('reviews.store', $assessment->id) }}">
            @csrf
            <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">

            <!-- Reviewee selection -->
            <div class="mb-3">
                <label for="reviewee" class="form-label">Select Reviewee</label>
                <select id="reviewee" name="reviewee_id" class="form-select @error('reviewee_id') is-invalid @enderror">
                    <option value="">-- Select a student --</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" {{ old('reviewee_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                    @endforeach
                </select>
                @error('reviewee_id')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Review Text -->
            <div class="mb-3">
                <label for="review_text" class="form-label">Review Text</label>
                <textarea id="review_text" name="review_text" class="form-control @error('review_text') is-invalid @enderror" rows="4" placeholder="Write your review...">{{ old('review_text') }}</textarea>
                @error('review_text')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary" style="background-color: #1A3C65;">Submit Review</button>
        </form>
    </div>

    <!-- Submitted Reviews -->
    <div class="card p-4 mb-4" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <h4 style="color: #1A3C65; margin-bottom: 25px;">Your Submitted Reviews:</h4>
        @if ($submittedReviews->isEmpty())
            <div class="alert alert-info">
                You haven't submitted any reviews yet.
            </div>
        @else
            <ul class="list-group">
                @foreach ($submittedReviews as $review)
                    <li class="list-group-item mb-3" style="background-color: #E6F0FA; border: 1px solid #1A3C65; border-radius: 8px;">
                        <strong style="color: #1A3C65;">Reviewee: {{ $review->reviewee->name }}</strong>
                        <p class="mt-2" style="color: #333;">{{ $review->review_text }}</p>
                    </li>
                @endforeach
            </ul>
            
        @endif
    </div>

    <!-- Received Reviews -->
    <div class="card p-4 mb-4" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <h4 style="color: #1A3C65; margin-bottom: 25px;">Peer Reviews You Received:</h4>
        @if ($receivedReviews->isEmpty())
            <div class="alert alert-info">
                You haven't received any reviews yet.
            </div>
        @else
            <ul class="list-group">
                @foreach ($receivedReviews as $review)
                    <li class="list-group-item mb-3" style="background-color: #E6F0FA; border: 1px solid #1A3C65; border-radius: 8px;">
                        <strong style="color: #1A3C65;">Reviewed by: {{ $review->reviewer->name }}</strong>
                        <p class="mt-2" style="color: #333;">{{ $review->review_text }}</p>

                        <!-- Display current rating if available -->
                        <p><strong>Current Rating:</strong> {{ $review->rating ?? 'Not rated yet' }}</p>

                        <!-- Rating Form -->
                        <form method="POST" action="{{ route('reviews.rate', $review->id) }}">
                            @csrf
                            @method('PUT')
                                <div class="d-flex align-items-center mt-2">
                                    <label for="rating-{{ $review->id }}" class="mr-3">Rate this review:</label>
                                    <input 
                                        type="range" 
                                        name="rating" 
                                        id="rating-{{ $review->id }}" 
                                        min="1" 
                                        max="5" 
                                        value="{{ $review->rating ?? 3 }}" 
                                        class="form-range mr-3" 
                                        style="width: 150px;"
                                    >
                                    <span id="rating-display-{{ $review->id }}" class="mr-3">{{ $review->rating ?? 3 }}</span>
                                    <button type="submit" class="btn btn-primary" style="background-color: #1A3C65; padding: 8px 20px; font-size: 14px; border-radius: 5px;">
                                        <i class="fas fa-paper-plane"></i> Submit Rating
                                    </button>
                                </div>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>
@foreach ($receivedReviews as $review)
<script>
    // Update the displayed value when the range input changes
    const ratingInput = document.getElementById('rating-{{ $review->id }}');
    const ratingDisplay = document.getElementById('rating-display-{{ $review->id }}');
    
    ratingInput.addEventListener('input', function() {
        ratingDisplay.textContent = this.value;
    });
</script>
@endforeach
@endsection