<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsDroneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions_drone', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_apprentis');
            // ...autres colonnes...
            $table->foreign('id_apprentis')
                ->references('id_apprentis')->on('apprentis')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions_drone');
    }
}