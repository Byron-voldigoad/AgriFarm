<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCulturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('cultures', function (Blueprint $table) {
        $table->dropColumn(['datePlantation', 'dateRecolte', 'agriculteur_id']);
        $table->text('description')->nullable()->after('nom');
    });
}

public function down()
{
    Schema::table('cultures', function (Blueprint $table) {
        $table->date('datePlantation');
        $table->date('dateRecolte');
        $table->foreignId('agriculteur_id')->constrained();
        $table->dropColumn('description');
    });
}

/**
 * Reverse the migrations.
 *
 * @return void
 */
}
