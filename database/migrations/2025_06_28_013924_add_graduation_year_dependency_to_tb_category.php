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
        Schema::table('tb_category', function (Blueprint $table) {
            $table->boolean('is_graduation_year_dependent')->default(false)->after('is_status_dependent');
            $table->json('required_graduation_years')->nullable()->after('required_alumni_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_category', function (Blueprint $table) {
            $table->dropColumn(['is_graduation_year_dependent', 'required_graduation_years']);
        });
    }
};
