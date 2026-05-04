<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table sessions_drone
     *
     * conditions_meteo est remplacé par une clé étrangère id_meteo
     * qui pointe vers la table meteo
     */
    public function up(): void
    {
        Schema::create('sessions_drone', function (Blueprint $table) {
            $table->id('id_sessions');
            $table->dateTime('date_heure');

            // true = extérieur, false = intérieur
            $table->boolean('type_environnement');

            $table->string('type_drone');
            $table->integer('duree_max');

            // Clé étrangère vers la table meteo
            $table->unsignedBigInteger('id_meteo');
            $table->foreign('id_meteo')
                ->references('id_meteo')
                ->on('meteo')
                ->onDelete('restrict');

            // Clé étrangère vers formateurs
            $table->string('login');
            $table->foreign('login')
                ->references('login')
                ->on('formateurs')
                ->onDelete('restrict');

            // Clé étrangère vers apprentis
            $table->unsignedBigInteger('id_apprentis');
            $table->foreign('id_apprentis')
                ->references('id_apprentis')
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