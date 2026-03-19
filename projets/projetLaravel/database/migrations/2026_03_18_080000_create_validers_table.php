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
    Schema::create('valider', function (Blueprint $table) {
    $table->unsignedBigInteger('id_sessions'); // ✅
    $table->foreign('id_sessions')
          ->references('id_sessions')
          ->on('sessions_drone') // ✅ nom réel de la table
          ->onDelete('cascade');

    $table->unsignedBigInteger('id_objectifs'); // ✅
    $table->foreign('id_objectifs')
          ->references('id_objectifs')
          ->on('objectifs')
          ->onDelete('cascade');

    $table->primary(['id_sessions', 'id_objectifs']);
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
        Schema::dropIfExists('validers');
    }
};
