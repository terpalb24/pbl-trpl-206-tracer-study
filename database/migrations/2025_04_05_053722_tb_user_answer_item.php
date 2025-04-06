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
        Schema::create("tb_user_answer_item", function (Blueprint $table) {
            $table->integer("id_user_answer_item", true);
            $table->integer("id_user_answer");
            $table->integer("id_question");
            $table->integer("id_question_option");
            $table->string("answer",255, null);
            $table->timestamps();
            $table->foreign("id_user_answer")->references("id_user_answer")->on("tb_user_answers");
            $table->foreign("id_question")->references("id_question")->on("tb_questions");
            $table->foreign("id_question_option")->references("id_question_option")->on("tb_questions_options");
        //
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('tb_user_answer_item');
        //
    }
};
