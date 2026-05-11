<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres_champs', function (Blueprint $table) {
            $table->id();
            $table->string('champ')->unique(); // etat, nature_juridique, source_financement
            $table->string('label');           // libellé affiché
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres_champs');
    }
};
