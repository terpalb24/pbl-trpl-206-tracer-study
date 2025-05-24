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
            $table->integer("id_questions_options")->nullable(); // Make nullable

            $table->string("answer", 255)->nullable(); // Make nullable
            $table->string("other_answer", 255)->nullable(); // Make nullable
            $table->timestamps();
            
            // Foreign keys
            $table->foreign("id_user_answer")->references("id_user_answer")->on("tb_user_answers")
                 ->onDelete('cascade')
                 ->onUpdate('cascade');
            
            $table->foreign("id_questions_options")->references("id_questions_options")->on("tb_questions_options")
                 ->onDelete('set null') // Safe deletion
                 ->onUpdate('cascade');
            
            $table->foreign("id_question")->references("id_question")->on("tb_questions")
                 ->onDelete('cascade')
                 ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('tb_user_answer_item');
    }
};
