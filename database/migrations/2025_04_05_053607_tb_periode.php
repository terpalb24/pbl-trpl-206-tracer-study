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
        Schema::create("tb_periode", function (Blueprint $table) {
            $table->integer("id_periode",true);
            $table->string('periode_name', 50);
            $table->date('start_date');
            $table->string('status', 50);
            $table->timestamps();
        //
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('tb_periode');
        //
    }
};
