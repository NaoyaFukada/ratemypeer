@extends('layouts.app')

@section('content')
<div class="container mt-5">
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: #1A3C65;">Attendance for {{ $assessment->title }} ({{ $assessment->course->course_code }})</h2>
    </div>

    <!-- Attendance Table -->
    <form method="POST" action="{{ route('attendance.store', ['assessment_id' => $assessment->id]) }}">
        @csrf
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0" style="color: #1A3C65;">Mark Attendance</h4>

                    <!-- Toggle All Attendance Buttons -->
                    <div>
                        <button type="button" class="btn btn-success btn-sm mr-2" id="mark-all-present">Mark All Present</button>
                        <button type="button" class="btn btn-danger btn-sm" id="mark-all-absent">Mark All Absent</button>
                    </div>
                </div>

                <table class="table table-hover">
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
                                <td>
                                    <!-- Hidden input for students' attendance marked as false -->
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

        <button type="submit" class="btn btn-primary mb-5" style="background-color: #1A3C65;">Save Attendance</button>
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
