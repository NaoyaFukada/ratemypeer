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
        Schema::table('users', function (Blueprint $table) { 
            $table->string('s_number')->unique()->after('email');  // Add student number after email
            $table->enum('role', ['student', 'teacher'])->after('password');  // Add role column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('s_number');
            $table->dropColumn('role');
        });
    }
};
