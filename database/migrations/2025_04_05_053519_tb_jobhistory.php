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
        Schema::create('tb_jobhistory',function (Blueprint $table){
            $table->integer('id_jobhistory', true);
            $table->string('nim');
            $table->integer('id_company');
            $table->string('position', 50);
            $table->date('start_date');
            $table->date('end_date');
            $table->float('salary');
            $table->timestamps();
            $table->foreign('nim')->references('nim')->on('tb_alumni');
            $table->foreign('id_company')->references('id_company')->on('tb_company');

        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('tb_jobhistory');
        //
    }
};
