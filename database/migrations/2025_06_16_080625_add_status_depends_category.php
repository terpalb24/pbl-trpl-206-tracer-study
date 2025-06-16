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
            $table->json('required_alumni_status')->nullable()->after('for_type');
            $table->boolean('is_status_dependent')->default(false)->after('for_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_category', function (Blueprint $table) {
            $table->dropColumn(['required_alumni_status', 'is_status_dependent']);
        });
    }
};
