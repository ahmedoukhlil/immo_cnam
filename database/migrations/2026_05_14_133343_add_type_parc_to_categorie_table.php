<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categorie', function (Blueprint $table) {
            $table->enum('type_parc', ['informatique', 'materiel'])->nullable()->after('type_cgi');
        });
    }

    public function down(): void
    {
        Schema::table('categorie', function (Blueprint $table) {
            $table->dropColumn('type_parc');
        });
    }
};
