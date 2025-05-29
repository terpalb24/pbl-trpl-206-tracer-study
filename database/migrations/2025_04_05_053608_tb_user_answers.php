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
        Schema::create("tb_user_answers", function (Blueprint $table) {
            $table->integer("id_user_answer",true);
            $table->integer('id_user');
            $table->string('status',50);
            $table->integer("id_periode");
            $table->timestamps();
            $table->foreign('id_user')->references('id_user')->on('tb_user')
            ->onDelete('cascade')
            ->onUpdate('cascade'); 
            $table->foreign('id_periode')->references('id_periode')->on('tb_periode')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            //
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void

    {
        Schema::drop('tb_user_answers');
        //
    }
};
