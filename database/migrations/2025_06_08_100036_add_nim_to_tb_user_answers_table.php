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

            //
         Schema::table('tb_user_answers', function (Blueprint $table) {
            $table->string('nim')->nullable()->after('id_periode');

            // Tambahkan foreign key ke tb_alumni.nim
            $table->foreign('nim')
                  ->references('nim')
                  ->on('tb_alumni')
                  ->onUpdate('cascade')
                  ->onDelete('set null'); // Jika alumni dihapus, isian tetap ada tapi nim jadi null
        });
     
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('tb_user_answers', function (Blueprint $table) {
            $table->dropForeign(['nim']);
            $table->dropColumn('nim');
        });
    }
};
