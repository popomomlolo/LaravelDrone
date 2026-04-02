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
        $table->id('id_apprentis');
        $table->string('nom');
        $table->string('prenom');
        $table->unsignedBigInteger('id_classes'); // même type que id() de classes
        $table->foreign('id_classes')
              ->references('id_classes')
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
