<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table pivot emplacement <-> utilisateur (occupant responsable d'un emplacement)
        Schema::create('emplacement_user', function (Blueprint $table) {
            $table->id();
            $table->integer('idEmplacement'); // integer pour correspondre à la table emplacement
            $table->integer('idUser');        // integer pour correspondre à la table users (idUser INT)
            $table->timestamps();

            $table->foreign('idEmplacement')->references('idEmplacement')->on('emplacement')->onDelete('cascade');
            $table->foreign('idUser')->references('idUser')->on('users')->onDelete('cascade');
            $table->unique(['idEmplacement', 'idUser']);
        });

        // Table tickets de maintenance
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // TKT-2026-001
            $table->integer('idEmplacement');      // integer pour correspondre à emplacement.idEmplacement
            $table->integer('bien_id')->nullable(); // NumOrdre du gesimmo concerné (pas de FK car table legacy)
            $table->integer('created_by'); // occupant qui signale (INT comme users.idUser)
            $table->integer('assigned_to')->nullable(); // technicien assigné
            $table->integer('assigned_by')->nullable(); // admin qui assigne
            $table->string('titre');
            $table->text('description');
            $table->enum('priorite', ['basse', 'normale', 'haute', 'urgente'])->default('normale');
            $table->enum('statut', ['ouvert', 'assigne', 'en_cours', 'resolu', 'ferme'])->default('ouvert');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('idEmplacement')->references('idEmplacement')->on('emplacement')->onDelete('cascade');
            // bien_id référence gesimmo.NumOrdre (table legacy, pas de FK formelle)
            $table->foreign('created_by')->references('idUser')->on('users');
            $table->foreign('assigned_to')->references('idUser')->on('users')->onDelete('set null');
            $table->foreign('assigned_by')->references('idUser')->on('users')->onDelete('set null');
        });

        // Table interventions (rapport du technicien)
        Schema::create('ticket_interventions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->integer('technicien_id');
            $table->text('probleme_identifie');
            $table->text('solution_appliquee');
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('technicien_id')->references('idUser')->on('users');
        });

        // Table pièces jointes (captures d'écran des interventions)
        Schema::create('ticket_pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('intervention_id');
            $table->string('nom_fichier');
            $table->string('chemin');
            $table->string('type_mime')->nullable();
            $table->unsignedBigInteger('taille')->nullable();
            $table->timestamps();

            $table->foreign('intervention_id')->references('id')->on('ticket_interventions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_pieces_jointes');
        Schema::dropIfExists('ticket_interventions');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('emplacement_user');
    }
};
