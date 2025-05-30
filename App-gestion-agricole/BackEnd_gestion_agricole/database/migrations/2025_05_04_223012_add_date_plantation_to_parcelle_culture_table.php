<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatePlantationToParcelleCultureTable extends Migration
{
    public function up()
    {
        Schema::table('parcelles', function (Blueprint $table) {
            $table->date('datePlantation')
                  ->default(DB::raw('CURRENT_DATE'))
                  ->after('id');
        });
    }

    public function down()
    {
        Schema::table('parcelles', function (Blueprint $table) {
            $table->dropColumn('datePlantation');
        });
    }
}