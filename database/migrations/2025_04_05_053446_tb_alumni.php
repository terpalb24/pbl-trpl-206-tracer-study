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
        Schema::create('tb_alumni', function(Blueprint $table){
            $table->integer('nim');
            $table->integer('id_user');
            $table->string('name',50);
            $table->string('password',255);
            $table->integer('nik');
            $table->string('gender',10);
            $table->date('date_of_birth');
            $table->string('phone_number',15);
            $table->string('email',50);
            $table->string('status',50);
            $table->string('study_program',255);
            $table->integer('graduation_year');
            $table->float('ipk');
            $table->integer('batch');
            $table->string('address',255);
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')->on('tb_user');
            $table->primary('nim');
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        schema::drop('tb_alumni');
        //
    }
};
