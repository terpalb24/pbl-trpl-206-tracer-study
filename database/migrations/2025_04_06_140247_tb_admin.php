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
        Schema::create("tb_admin", function (Blueprint $table) {
            $table->integer("id_admin",true);
            $table->integer("id_user",);
            $table->string("username", 55);
            $table->string("password",255);
            $table->timestamps();
            $table->foreign("id_user")->references("id_user")->on("tb_user");




        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('tb_admin');
        //
    }
};
