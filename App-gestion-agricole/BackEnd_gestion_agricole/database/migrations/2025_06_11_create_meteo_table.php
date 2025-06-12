<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeteoTable extends Migration
{
    public function up()
    {
        Schema::create('meteo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcelle_id')->constrained('parcelles')->onDelete('cascade');
            $table->decimal('temperature', 5, 2);
            $table->decimal('humidite', 5, 2);
            $table->decimal('pluie', 5, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meteo');
    }
}
