@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Assessment Details -->
    <div class="card p-4 mb-4" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <h2 class="mb-4" style="color: #1A3C65;">{{ $assessment->title }} for {{ $course->course_name }} ({{ $course->course_code }})</h2>
        <p><strong style="color: #1A3C65;">Number of Required Reviews:</strong> <span class="text-dark">{{ $assessment->num_reviews_required }}</span></p>
        <p><strong style="color: #1A3C65;">Max Score:</strong> <span class="text-dark">{{ $assessment->max_score }}</span></p>
        <p><strong style="color: #1A3C65;">Due Date:</strong> <span class="text-dark">{{ $assessment->due_date->format('d M Y') }}</span></p>
        <!-- Types are like teacher-assign, so by removing '-' with space, it would display well. -->
        <p><strong style="color: #1A3C65;">Assessment Type:</strong> <span class="text-dark">{{ ucfirst(str_replace('-', ' ', $assessment->type)) }}</span></p>
    </div>

    <!-- Search Bar -->
    <div class="mb-4">
        <form method="GET" action="">
            <!-- As this is Get method, csrf is not neccessary -->
            <div class="input-group">
                <!-- old('search') is used for POST method, more specifically validation error -->
                <input type="text" class="form-control" name="search" placeholder="Search by name or S-number" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary" style="background-color: #1A3C65;">Search</button>
            </div>
        </form>
    </div>

    <!-- Students List with Review Stats -->
    <div class="card p-4" style="border-color: #1A3C65; background-color: #F7F9FC;">
        <h4 style="color: #1A3C65;">Students and Review Stats:</h4>
        <table class="table table-hover mt-3">
            <thead class="table-dark">
                <tr>
                    <th scope="col">S Number</th>
                    <th scope="col">Student Name</th>
                    <th scope="col">Submitted Reviews</th>
                    <th scope="col">Received Reviews</th>
                    <th scope="col">Score</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($studentsWithReviewStats as $studentStat)
                <tr>
                    <td>{{ $studentStat['student']->s_number }}</td>
                    <td>{{ $studentStat['student']->name }}</td>
                    <td>{{ $studentStat['submittedReviewsCount'] }}</td>
                    <td>{{ $studentStat['receivedReviewsCount'] }}</td>
                    <td>{{ $studentStat['score'] }}</td>
                    <td>
                        <a href="{{ route('student.reviews', ['assessment' => $assessment->id, 'student' => $studentStat['student']->id]) }}" class="btn btn-primary btn-sm">View Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection