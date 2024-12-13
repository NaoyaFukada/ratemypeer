@extends('layouts.app')

<style>
    /* Table Header Styling */
    .table-responsive .table thead th {
        background-color: #1A3C65 !important; /* Theme color */
        color: white !important; /* White text for contrast */
    }

    /* Table Row Styling */
    .table-responsive .table tbody tr td {
        background-color: #F8FAFD; /* Light background for rows */
        color: #062245; /* Theme color for text */
    }

    /* Table Row Hover Effect */
    .table-responsive .table tbody td:hover {
        background-color: #DCE6F1 !important; /* Slightly darker hover color */
        color: #1A3C65; /* Keep theme color for text */
    }
</style>

@section('content')
<div class="container mt-4 mt-lg-5">
    <h2 class="mb-4" style="color: #1A3C65;">Top 10 Reviewers</h2>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Average Score (Normalized)</th>
                    <th>Average Review Rating (Normalized)</th>
                    <th>Overall Ranking</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankings as $index => $ranking)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $ranking['student']->name }}</td>
                        <td>{{ number_format($ranking['normalized_score'], 2) }}%</td>
                        <td>{{ number_format($ranking['normalized_rating'], 2) }}%</td>
                        <td>{{ number_format($ranking['ranking'], 2) }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
