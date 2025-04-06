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
        Schema::create("tb_questions", function (Blueprint $table) {
            $table->integer("id_question", true);
            $table->integer("id_category");
            $table->string("question",255);
            $table->string("type",50);
            $table->integer('order');
            $table->timestamps();
            $table->foreign('id_category')->references('id_category')->on('tb_category');
        //
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('tb_questions');
        //
    }
};
