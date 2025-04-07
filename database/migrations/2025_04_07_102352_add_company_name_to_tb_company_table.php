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
        Schema::table('tb_company', function (Blueprint $table) {
            $table->string('company_name', 50)->after('id_user');
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_company', function (Blueprint $table) {
            $table->dropColumn('company_name');
            //
        });
    }
};
