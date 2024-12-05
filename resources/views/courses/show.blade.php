@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Missing Users Message -->
    @if(session('missingUsers'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Some users could not be added to the course:</strong>
            <ul>
                @if(!empty(session('missingUsers')['teachers']))
                    <li><strong>Missing Teachers:</strong>
                        <ul>
                            @foreach(session('missingUsers')['teachers'] as $missingTeacher)
                                <li>{{ $missingTeacher }}</li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                @if(!empty(session('missingUsers')['students']))
                    <li><strong>Missing Students:</strong>
                        <ul>
                            @foreach(session('missingUsers')['students'] as $missingStudent)
                                <li>{{ $missingStudent }}</li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    @endif

    <!-- Course Details Section -->
    <h2 class="mb-4" style="color: #1A3C65;">{{ $course->course_name }} ({{ $course->course_code }})</h2>

    <!-- Teachers List -->
    <div class="mb-5">
        <h4 class="mb-3" style="color: #1A3C65;">Teachers:</h4>
        <ul class="list-group">
            @foreach ($teachers as $teacher)
                <li class="list-group-item mb-2 border-0 shadow-sm">
                    <i class="fas fa-user-tie mr-2" style="color: #1A3C65;"></i>{{ $teacher->name }}
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Peer Review Assessments Heading and Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 style="color: #1A3C65;">Peer Review Assessments:</h4>

        <!-- Button to add a new assessment (only for teachers) -->
        @if (Auth::user()->role === 'teacher' && $course->teachers->contains(Auth::user()))
            <a href="{{ route('assessments.create', ['course_id' => $course->id]) }}" class="btn btn-success">
                <i class="fas fa-plus-circle mr-2"></i> Add New Assessment
            </a>
        @endif
    </div>

    <!-- Assessments List -->
    <div class="mb-5">
        <ul class="list-group">
            @foreach ($assessments as $assessment)
                <li class="list-group-item d-flex justify-content-between align-items-center mb-2 border-0 shadow-sm">
                    <div>
                        <i class="fas fa-clipboard mr-2" style="color: #1A3C65;"></i>
                        <strong>{{ $assessment->title }}</strong>
                        <br>
                        <small>Due Date: {{ $assessment->due_date->format('d M Y') }}</small>
                    </div>
                    <div>
                        <a href="{{ route('assessments.show', $assessment->id) }}" class="btn btn-sm btn-primary">View</a>
                        
                        <!-- Display 'Edit' and 'Take Attendance' buttons for teachers -->
                        @if (Auth::user()->role === 'teacher' && $course->teachers->contains(Auth::user()))

                            <!-- Edit Button: Disable if reviews have been submitted -->
                            <a href="{{ route('assessments.edit', $assessment->id) }}" class="btn btn-sm btn-warning ms-2 disabled-button"
                               @if ($assessment->reviews->count() > 0) disabled @endif>
                                Edit
                            </a>

                            <!-- Take Attendance Button: Disable if reviews have been submitted and for non-'teacher-assign' types -->
                            <a href="{{ route('attendance.create', ['assessment_id' => $assessment->id]) }}" class="btn btn-sm btn-info ms-2 disabled-button"
                               @if ($assessment->reviews->count() > 0 || $assessment->type !== 'teacher-assign') disabled @endif>
                                <i class="fas fa-clipboard-list"></i> Take Attendance
                            </a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Add Students Section (Only for Teachers) -->
    @if (Auth::user()->role === 'teacher' && $course->teachers->contains(Auth::user()))
    <div class="bg-white p-4 rounded shadow mb-5">
        <h4 class="text-primary mb-4" style="color: #1A3C65;">
            <i class="fas fa-user-plus mr-2"></i>Add Students to Course
        </h4>
        <form action="{{ route('courses.addStudents', $course->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="students" class="form-label" style="color: #1A3C65; font-weight: bold;">
                    Select Students to Add:
                </label>
                <div class="input-group mb-3 shadow-sm">
                    <select id="students" name="students[]" class="form-select w-100" multiple aria-label="Select Students">
                        @foreach ($availableStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->s_number }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100" style="background-color: #1A3C65;">
                <i class="fas fa-user-check mr-2"></i>Add Selected Students
            </button>
        </form>
    </div>
@endif

</div>

<!-- Custom CSS to modify disabled button appearance -->
<style>
    .disabled-button[disabled] {
        opacity: 0.5; /* Reduce opacity */
        cursor: not-allowed; /* Change cursor to indicate it's not clickable */
        background-color: #ccc !important; /* Change background color */
        border-color: #bbb !important; /* Change border color */
    }
</style>
@endsection
