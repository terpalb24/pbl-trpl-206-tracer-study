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
        Schema::create('tb_study_program', function (Blueprint $table) {
            $table->id('id_study');
            $table->string('nim');
            $table->string('study_program', 255);
            $table->timestamps();
            $table->foreign('nim')->references('nim')->on('tb_alumni')->onDelete('cascade');
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_study_program');
        //
    }
};
