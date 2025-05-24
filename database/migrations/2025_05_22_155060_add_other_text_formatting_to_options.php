<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tb_questions_options', function (Blueprint $table) {
            $table->string('other_before_text')->nullable()->after('is_other_option');
            $table->string('other_after_text')->nullable()->after('other_before_text');
        });
    }

    public function down()
    {
        Schema::table('tb_questions_options', function (Blueprint $table) {
            $table->dropColumn(['other_before_text', 'other_after_text']);
        });
    }
};