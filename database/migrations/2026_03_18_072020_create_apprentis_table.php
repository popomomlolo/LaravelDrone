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
    Schema::create('apprentis', function (Blueprint $table) {
        $table->id('id_apprenti');
        $table->string('nom');
        $table->string('prenom');
        $table->unsignedBigInteger('id_classe');
        $table->foreign('id_classe')
              ->references('id_classe')
              ->on('classes')
              ->onDelete('restrict');
        //$table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apprentis');
    }

    
};
