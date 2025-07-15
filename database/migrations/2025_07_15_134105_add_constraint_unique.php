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
        // Add unique constraints to tb_alumni
        Schema::table('tb_alumni', function (Blueprint $table) {
            $table->unique('nik', 'alumni_nik_unique');
            $table->unique('email', 'alumni_email_unique');
        });

        // Add unique constraint to tb_company
        Schema::table('tb_company', function (Blueprint $table) {
            $table->unique('company_email', 'company_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove unique constraints from tb_alumni
        Schema::table('tb_alumni', function (Blueprint $table) {
            $table->dropUnique('alumni_nik_unique');
            $table->dropUnique('alumni_email_unique');
        });

        // Remove unique constraint from tb_company
        Schema::table('tb_company', function (Blueprint $table) {
            $table->dropUnique('company_email_unique');
        });
    }
};
