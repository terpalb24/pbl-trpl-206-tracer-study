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
        Schema::table('tb_alumni', function (Blueprint $table) {
            // Add the is_First_login column with default value 1
            $table->boolean('is_First_login')->default(1)->after('status');
        });

        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_alumni', function (Blueprint $table) {
            // Drop the is_First_login column
            $table->dropColumn('is_First_login');
        });
        //
    }
};
