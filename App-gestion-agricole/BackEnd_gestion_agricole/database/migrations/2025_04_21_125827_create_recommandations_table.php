<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecommandationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recommandations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parcelle_id')->constrained('parcelles')->onDelete('cascade');
            $table->string('titre');
            $table->text('contenu');
            $table->string('type')->comment('ex: CULTURE, ENTRETIEN, FERTILISATION');
            $table->enum('priorite', ['haute', 'moyenne', 'basse'])->default('moyenne');
            $table->enum('status', ['proposée', 'lue', 'acceptée'])->default('proposée');
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
        Schema::dropIfExists('recommandations');
    }
}
