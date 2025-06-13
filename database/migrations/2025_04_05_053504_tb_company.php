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
        Schema::create('tb_company', function (Blueprint $table) {
            $table->integer('id_company', true);
            $table->integer('id_user')->nullable();
            $table->string('company_name',50);
            $table->string('company_address',255)->nullable();
            $table->string('company_email',50)->nullable();
            $table->string('company_phone_number',15)->nullable();
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade')->onUpdate('cascade');
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('tb_company');
        //
    }
};
