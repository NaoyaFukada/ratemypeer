<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('title', 20);  // Title max length 20 characters
            $table->text('instruction');
            // unsingned() makes sure that the number is not negative number (Although 0 is inclusive)
            $table->integer('num_reviews_required')->unsigned(); // Should be positive number
            $table->integer('max_score')->unsigned();  // Score between 1 and 100
            $table->date('due_date');
            // enum allows me to define fixed set of possible values
            $table->enum('type', ['student-select', 'teacher-assign']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
