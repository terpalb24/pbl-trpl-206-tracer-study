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
        Schema::table('tb_periode', function (Blueprint $table) {
            $table->json('target_graduation_years')->nullable()->after('status');
            $table->boolean('all_alumni')->default(true)->after('target_graduation_years');
            $table->string('target_type', 20)->default('all')->after('all_alumni'); // 'all', 'specific_years', 'years_ago'
            $table->json('years_ago_list')->nullable()->after('target_type'); // [1, 3, 5] untuk 1, 3, 5 tahun lalu
            $table->text('target_description')->nullable()->after('years_ago_list');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_periode', function (Blueprint $table) {
            $table->dropColumn([
                'target_graduation_years', 
                'all_alumni', 
                'target_type', 
                'years_ago_list', 
                'target_description'
            ]);
        });
    }
};
