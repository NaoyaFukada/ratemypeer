@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Top 10 Reviewers</h2>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
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
@endsection
