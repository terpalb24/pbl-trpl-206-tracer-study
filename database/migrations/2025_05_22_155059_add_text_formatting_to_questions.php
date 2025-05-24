<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tb_questions', function (Blueprint $table) {
            $table->string('before_text')->nullable()->after('type');
            $table->string('after_text')->nullable()->after('before_text');
        });
    }

    public function down()
    {
        Schema::table('tb_questions', function (Blueprint $table) {
            $table->dropColumn(['before_text', 'after_text']);
        });
    }
};