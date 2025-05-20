<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
   public function up()
    {
        Schema::table('tb_alumni', function (Blueprint $table) {
            $table->string('nik', 20)->change();
        });
    }

    public function down()
    {
        Schema::table('tb_alumni', function (Blueprint $table) {
            // Ubah kembali ke tipe sebelumnya, misalnya bigint atau integer
            $table->bigInteger('nik')->change();
        });
    }
};
