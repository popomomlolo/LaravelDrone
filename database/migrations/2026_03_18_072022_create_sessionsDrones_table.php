<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Crée la table sessions_drone
     * Dépend de : meteo, formateurs, apprentis
     * Cette migration doit être exécutée APRÈS ces 3 tables
     */
    public function up(): void
    {
        Schema::create('sessions_drone', function (Blueprint $table) {
            $table->id('id_session');
            $table->dateTime('date_heure')->default(DB::raw('CURRENT_TIMESTAMP'));

            // true = extérieur, false = intérieur
            $table->boolean('type_environnement');

            $table->boolean('type_drone');
            $table->integer('duree_max');

            // Clé étrangère vers conditions_meteo
            $table->unsignedBigInteger('id_meteo');
            $table->foreign('id_meteo')
                ->references('id_meteo')
                ->on('conditions_meteo')
                ->onDelete('restrict');

            // Clé étrangère vers formateurs
            $table->unsignedBigInteger('id_formateur');
            $table->foreign('id_formateur')
                ->references('id_formateur')
                ->on('formateurs')
                ->onDelete('restrict');

            // Clé étrangère vers apprentis
            $table->unsignedBigInteger('id_apprenti');
            $table->foreign('id_apprenti')
                ->references('id_apprenti')
                ->on('apprentis')
                ->onDelete('restrict');

            //$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions_drone');
    }
};
