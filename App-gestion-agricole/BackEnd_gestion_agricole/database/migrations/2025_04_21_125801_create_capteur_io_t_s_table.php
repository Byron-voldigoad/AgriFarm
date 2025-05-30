<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapteurIoTSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('capteur_io_ts', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('valeur')->nullable();
            $table->date('dateMesure');
            $table->foreignId('parcelle_id')->constrained('parcelles')->onDelete('cascade');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('capteur_io_t_s');
    }
}
