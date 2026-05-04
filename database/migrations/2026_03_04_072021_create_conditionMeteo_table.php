<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table meteo
     *
     * condition_meteo : true = bonne météo, false = mauvaise météo
     * vent_x, vent_y, vent_z : direction du vent (vecteur 3D)
     * vent_norme : intensité du vent en m/s
     */
    public function up(): void
    {
        Schema::create('condition_meteo', function (Blueprint $table) {
            $table->id('id_meteo');

            // Condition générale : true = favorable, false = défavorable
            $table->boolean('condition_meteo');

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
        Schema::dropIfExists('condition_meteo');
    }
};