<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parcelle_culture', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('culture_id');
            $table->unsignedBigInteger('parcelle_id');
            $table->timestamps();

            $table->foreign('culture_id')->references('id')->on('cultures')->onDelete('cascade');
            $table->foreign('parcelle_id')->references('id')->on('parcelles')->onDelete('cascade');

            $table->unique(['culture_id', 'parcelle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcelle_culture');
    }
};