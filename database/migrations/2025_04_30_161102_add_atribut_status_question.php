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
        Schema::table('tb_questions', function (Blueprint $table) {
            // Add the new columns
            $table->string('status')->default('visible')->after('order');
        });
        
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('tb_questions', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn('status');
        });
        //
    }
};
