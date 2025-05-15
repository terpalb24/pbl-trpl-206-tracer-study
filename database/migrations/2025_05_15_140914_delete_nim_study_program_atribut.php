<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tb_study_program', function (Blueprint $table) {
            // Drop foreign key dulu
            $table->dropForeign(['nim']); // atau $table->dropForeign('tb_study_program_nim_foreign');
            
            // Baru drop kolom
            $table->dropColumn('nim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tb_study_program', function (Blueprint $table) {
            $table->string('nim')->nullable();
    
            $table->foreign('nim')
                  ->references('nim')
                  ->on('tb_alumni')
                  ->onDelete('cascade');
        });
    }
};    
