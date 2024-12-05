<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Assessment;
use Illuminate\Support\Facades\Auth;


class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        // https://laravel.com/docs/11.x/validation#using-closures
        $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'reviewee_id' => 'required|exists:users,id|different:reviewer_id',  // Ensure the reviewee is different from the reviewer
            // Custom validation rule for checking at least 5 words in review_text
            'review_text' => ['required', 'string', function ($attribute, $value, $fail) {
                if (str_word_count($value) < 5) {
                    $fail('The ' . $attribute . ' must contain at least 5 words.');
                }
            }],
        ]);

        // Ensure the student has not already submitted the required number of reviews
        $user = Auth::user();
        $assessment = Assessment::findOrFail($request->assessment_id);
        $existingReviewsCount = Review::where('assessment_id', $assessment->id)
            ->where('reviewer_id', $user->id)
            ->count();

        // Check if the reviewer is trying to submit a review for themselves
        if ($user->id == $request->reviewee_id) {
            return redirect()->back()->withErrors('You cannot submit a review for yourself.');
        }

        // Check if the reviewer has already submitted a review for the selected reviewee in this assessment
        $existingReviewForReviewee = Review::where('assessment_id', $assessment->id)
            ->where('reviewer_id', $user->id)
            ->where('reviewee_id', $request->reviewee_id)
            ->first();

        if ($existingReviewForReviewee) {
            return redirect()->back()->withErrors('You have already submitted a review for this student.');
        }

        // Create the review
        Review::create([
            'assessment_id' => $assessment->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $request->reviewee_id,
            'review_text' => $request->review_text,
        ]);

        return redirect()->route('assessments.show', $assessment->id)
            ->with('success', 'Review submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Update the rating for a review.
     */
    public function rate(Request $request, $reviewId)
    {
        // Validate that the rating is between 1 and 5
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // Find the review by ID
        $review = Review::findOrFail($reviewId);

        // Ensure that the user receiving the review can rate it
        if (Auth::id() !== $review->reviewee_id) {
            return redirect()->back()->withErrors('You are not authorized to rate this review.');
        }

        // Update the rating for the review
        $review->update([
            'rating' => $request->rating,
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Rating submitted successfully.');
    }
}
