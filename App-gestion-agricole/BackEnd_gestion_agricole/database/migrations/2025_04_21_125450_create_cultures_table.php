<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCulturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cultures', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->date('datePlantation')->nullable()->change();
            $table->date('dateRecolte')->nullable()->change();
            $table->foreignId('agriculteur_id')->nullable()->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('parcelle_id')->nullable()->constrained('parcelles')->onDelete('cascade');
            $table->string('description')->nullable();
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
        Schema::dropIfExists('cultures');
    }
}
