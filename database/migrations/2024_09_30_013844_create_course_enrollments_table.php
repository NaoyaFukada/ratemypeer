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
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // foreignID(): It automatically creates the column course_id as an unsignedBigInteger. (Shorthand for $table->unsignedBigInteger())
            // constrained(): connect to courses table (Laravel automatically assumes it's referring to id unless it's specified otherwise)
            // onDelete('cascade'): When this row was deleted, all of related rows in other table would be deleted as well.
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->primary(['user_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
