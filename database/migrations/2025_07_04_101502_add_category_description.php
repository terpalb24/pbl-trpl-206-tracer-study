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
        Schema::table('tb_category', function (Blueprint $table) {
            $table->string('category_name', 255)->change();
            $table->text('description')->nullable()->after('category_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_category', function (Blueprint $table) {
            $table->string('category_name', 100)->change();
            $table->dropColumn('description');
        });
    }
};