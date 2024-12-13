@extends('layouts.app')

@section('content')
<div class="container mt-4 mt-lg-5">
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Validation Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Page Header -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-2 mb-lg-4">
        <h2 class="mb-3 mb-lg-0 text-center text-lg-start" style="color: #1A3C65;">
            Attendance for {{ $assessment->title }} ({{ $assessment->course->course_code }})
        </h2>
    </div>

    <!-- Attendance Form -->
    <form method="POST" action="{{ route('attendance.store', ['assessment_id' => $assessment->id]) }}">
        @csrf

        <!-- Card for Attendance -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4">
                    <h4 class="mb-3 mb-lg-0 text-center text-lg-start" style="color: #1A3C65;">Mark Attendance</h4>

                    <!-- Toggle All Attendance Buttons -->
                    <div class="d-flex gap-2 justify-content-center justify-content-lg-end">
                        <button type="button" class="btn btn-success btn-sm" id="mark-all-present">Mark All Present</button>
                        <button type="button" class="btn btn-danger btn-sm" id="mark-all-absent">Mark All Absent</button>
                    </div>
                </div>

                <!-- Responsive Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Student Name</th>
                                <th scope="col">S Number</th>
                                <th scope="col">Attended</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr class="attendance-row" data-id="{{ $student->id }}">
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->s_number }}</td>
                                    <td class="text-center">
                                        <!-- Hidden input for default attendance -->
                                        <input type="hidden" name="attendance[{{ $student->id }}]" value="0">
                                        
                                        <!-- Checkbox for attendance -->
                                        <input type="checkbox" name="attendance[{{ $student->id }}]" value="1" class="attendance-checkbox"
                                            {{ $attendanceRecords->where('user_id', $student->id)->first()?->attended ? 'checked' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Save Attendance Button -->
        <div class="d-flex justify-content-center mb-2">
            <button type="submit" class="btn btn-primary btn-lg w-100 w-md-50" style="background-color: #1A3C65;">
                Save Attendance
            </button>
        </div>
    </form>
</div>

<script>
    // JavaScript to toggle all checkboxes
    document.getElementById('mark-all-present').addEventListener('click', function() {
        document.querySelectorAll('.attendance-checkbox').forEach(function(checkbox) {
            checkbox.checked = true;
        });
    });

    document.getElementById('mark-all-absent').addEventListener('click', function() {
        document.querySelectorAll('.attendance-checkbox').forEach(function(checkbox) {
            checkbox.checked = false;
        });
    });

    // JavaScript to toggle checkbox when clicking on the row
    document.querySelectorAll('.attendance-row').forEach(function(row) {
        row.addEventListener('click', function(event) {
            if (event.target.tagName !== 'INPUT') { // Prevents the checkbox from being triggered twice
                const checkbox = this.querySelector('.attendance-checkbox');
                checkbox.checked = !checkbox.checked;
            }
        });
    });
</script>
@endsection
