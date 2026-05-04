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
    Schema::create('validations', function (Blueprint $table) {
    $table->unsignedBigInteger('id_session'); 
    $table->foreign('id_session')
          ->references('id_session')
          ->on('sessions_drone') 
          ->onDelete('cascade');

    $table->unsignedBigInteger('id_objectif');
    $table->foreign('id_objectif')
          ->references('id_objectif')
          ->on('objectifs')
          ->onDelete('cascade');

    $table->primary(['id_session', 'id_objectif']);
    $table->boolean('reussi');
    $table->integer('quantite_a_atteindre');
    $table->integer('quantite_realisee');
    //$table->timestamps();
});
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};
