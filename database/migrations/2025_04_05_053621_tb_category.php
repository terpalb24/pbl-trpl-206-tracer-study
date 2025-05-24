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
        Schema::create("tb_category", function (Blueprint $table) {
            $table->integer("id_category", true);
            $table->integer("id_periode");
            $table->string("category_name",50);
            $table->integer("order");
            $table->string('for_type',50)->default('both'); 
            $table->timestamps();
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
        Schema::drop('tb_category');
        //
    }
};
