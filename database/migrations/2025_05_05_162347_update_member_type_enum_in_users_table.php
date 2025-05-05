<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to modify the column definition directly with raw SQL
        DB::statement("ALTER TABLE users MODIFY member_type ENUM('basic', 'standard', 'premium', 'regular', 'vip') DEFAULT 'basic'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original values
        DB::statement("ALTER TABLE users MODIFY member_type ENUM('basic', 'standard', 'premium') DEFAULT 'basic'");
    }
};
