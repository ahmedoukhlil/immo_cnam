<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etat', function (Blueprint $table) {
            $table->boolean('actif')->default(true)->after('CodeEtat');
        });

        Schema::table('naturejurdique', function (Blueprint $table) {
            $table->boolean('actif')->default(true)->after('CodeNatJur');
        });

        Schema::table('sourcefinancement', function (Blueprint $table) {
            $table->boolean('actif')->default(true)->after('CodeSourceFin');
        });
    }

    public function down(): void
    {
        Schema::table('etat', function (Blueprint $table) {
            $table->dropColumn('actif');
        });
        Schema::table('naturejurdique', function (Blueprint $table) {
            $table->dropColumn('actif');
        });
        Schema::table('sourcefinancement', function (Blueprint $table) {
            $table->dropColumn('actif');
        });
    }
};
