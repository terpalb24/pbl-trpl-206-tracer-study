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
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->float('salary');
            $table->timestamps();
            $table->foreign('nim')->references('nim')->on('tb_alumni')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_company')->references('id_company')->on('tb_company')->onDelete('cascade')->onUpdate('cascade');

        });
        Schema::table('tb_jobhistory', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
        });
        Schema::table('tb_jobhistory', function (Blueprint $table) {
            $table->string('duration')->nullable();
        });
        //
    }

    /**             
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_jobhistory', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('tb_jobhistory', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
        Schema::drop('tb_jobhistory');
        //
    }
};
