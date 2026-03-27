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
    Schema::create('sessions_drone', function (Blueprint $table) {
        $table->id('id_sessions');
        $table->dateTime('date_heure');
        $table->string('type_environnement');
        $table->string('conditions_meteo');
        $table->string('type_drone');
        $table->integer('duree_max');
        $table->string('login');
        $table->foreign('login')
              ->references('login')->on('formateurs')
              ->onDelete('restrict');
        $table->unsignedBigInteger('id_apprentis'); // ✅
$table->foreign('id_apprentis')
      ->references('id_apprentis')
      ->on('apprentis')
      ->onDelete('restrict');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
