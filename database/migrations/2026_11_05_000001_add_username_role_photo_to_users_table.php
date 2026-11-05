<?php

use Illuminate\Database\Migrations\Migration;

// This migration has been superseded by 0001_01_01_000000_create_users_table.php
// which now includes username, role, and profile_photo_path columns directly.
return new class extends Migration
{
    public function up(): void
    {
        // no-op
    }

    public function down(): void
    {
        // no-op
    }
};
