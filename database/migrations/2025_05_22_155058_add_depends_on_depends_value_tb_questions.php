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
        Schema::table('tb_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('depends_on')->after('type')->nullable();
            $table->string('depends_value')->after('depends_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_questions', function (Blueprint $table) {
            $table->dropColumn(['depends_on', 'depends_value']);
        });
    }
};
