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
        Schema::create("tb_questions_options", function (Blueprint $table) {
            $table->integer("id_question_option",true);
            $table->integer("id_question");
            $table->string("option",255);
            $table->integer("order");
            $table->timestamps();
            $table->foreign("id_question")->references("id_question")->on("tb_questions");

        //
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('tb_questions_options');
        //
    }
};
