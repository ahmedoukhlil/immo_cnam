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
        Schema::table('gesimmo', function (Blueprint $table) {
            $table->string('code_formate', 100)->nullable()->after('NumOrdre');
        });
    }

    public function down(): void
    {
        Schema::table('gesimmo', function (Blueprint $table) {
            $table->dropColumn('code_formate');
        });
    }
};
