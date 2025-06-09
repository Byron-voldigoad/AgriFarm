<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });

        Schema::create('role_utilisateur', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('utilisateur_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->unique(['utilisateur_id', 'role_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_utilisateur');
        Schema::dropIfExists('roles');
    }
};
