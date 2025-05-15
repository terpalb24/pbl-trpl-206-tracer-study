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
            $table->unsignedBigInteger('id_study')->after('nim')->nullable();
            $table->foreign('id_study')->references('id_study')->on('tb_study_program')->after('nim');
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_alumni', function (Blueprint $table) {
            $table->dropForeign(['id_study']);
            $table->dropColumn('id_study');
        });
        //
    }
};
