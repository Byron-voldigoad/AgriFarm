<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateParcellesAndCulturesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Supprimer stadeCroissance de cultures
        Schema::table('cultures', function (Blueprint $table) {
            $table->dropColumn('stadeCroissance');
        });

        // Ajouter culture_id et stadeCroissance à parcelles
        Schema::table('parcelles', function (Blueprint $table) {
            $table->unsignedBigInteger('culture_id')->nullable()->after('agriculteur_id');
            $table->string('stadeCroissance')->nullable()->after('culture_id');
            
            $table->foreign('culture_id')->references('id')->on('cultures')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Annuler les modifications si nécessaire
        Schema::table('parcelles', function (Blueprint $table) {
            $table->dropForeign(['culture_id']);
            $table->dropColumn(['culture_id', 'stadeCroissance']);
        });

        Schema::table('cultures', function (Blueprint $table) {
            $table->string('stadeCroissance')->nullable();
        });
    }
}