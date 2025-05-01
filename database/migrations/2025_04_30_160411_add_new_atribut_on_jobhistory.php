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
        Schema::table('tb_jobhistory', function (Blueprint $table) {
            // Add the is_First_login column with default value 1
            $table->string('status')->default('active')->after('end_date');
            $table->string('company_email') ->nullable()->after('salary');
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_jobhistory', function (Blueprint $table) {
            // Add the is_First_login column with default value 1
            $table->dropColumn('status');
            $table->dropColumn('company_email') ;
        });
        //
    }
};
