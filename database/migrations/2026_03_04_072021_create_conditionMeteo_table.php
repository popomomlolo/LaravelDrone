<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table meteo
     *
     * jour : true = jour, false = nuit
     * ciel : code du type de ciel (0=dégagé, 1=nuageux, 2=couvert, 3=pluvieux, 4=orageux)
     * vent_x, vent_y, vent_z : direction du vent (vecteur 3D)
     * vent_norme : intensité du vent en m/s
     */
    public function up(): void
    {
        Schema::create('conditions_meteo', function (Blueprint $table) {
            $table->id('id_meteo');

            // true = jour, false = nuit
            $table->boolean('jour');
            
            // Type de ciel (0=dégagé, 1=nuageux, 2=couvert, 3=pluvieux, 4=orageux)
            $table->integer('ciel');

            // Direction du vent sur les 3 axes (vecteur)
            $table->float('vent_x');
            $table->float('vent_y');
            $table->float('vent_z');

            // Intensité du vent (norme du vecteur, en m/s)
            $table->float('vent_norme');

            //$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conditions_meteo');
    }
};
