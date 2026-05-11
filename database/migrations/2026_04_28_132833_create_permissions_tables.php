<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('module');
            $table->string('action');
            $table->timestamps();
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->integer('user_id');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->boolean('granted')->default(true);
            $table->primary(['user_id', 'permission_id']);
            $table->foreign('user_id')->references('idUser')->on('users')->onDelete('cascade');
        });

        $now = now();
        $permissions = [
            ['module' => 'immobilisations', 'action' => 'voir',      'label' => 'Voir les immobilisations'],
            ['module' => 'immobilisations', 'action' => 'creer',     'label' => 'Créer des immobilisations'],
            ['module' => 'immobilisations', 'action' => 'modifier',  'label' => 'Modifier des immobilisations'],
            ['module' => 'immobilisations', 'action' => 'supprimer', 'label' => 'Supprimer des immobilisations'],
            ['module' => 'inventaires',     'action' => 'voir',      'label' => 'Voir les inventaires'],
            ['module' => 'inventaires',     'action' => 'creer',     'label' => 'Créer des inventaires'],
            ['module' => 'inventaires',     'action' => 'executer',  'label' => 'Exécuter des inventaires'],
            ['module' => 'stock',           'action' => 'voir',      'label' => 'Voir le stock'],
            ['module' => 'stock',           'action' => 'gerer',     'label' => 'Gérer le stock'],
            ['module' => 'tickets',         'action' => 'voir',      'label' => 'Voir les tickets'],
            ['module' => 'tickets',         'action' => 'creer',     'label' => 'Créer des tickets'],
            ['module' => 'tickets',         'action' => 'assigner',  'label' => 'Assigner des tickets'],
            ['module' => 'tickets',         'action' => 'traiter',   'label' => 'Traiter des tickets'],
            ['module' => 'tickets',         'action' => 'fermer',    'label' => 'Fermer des tickets'],
            ['module' => 'utilisateurs',    'action' => 'voir',      'label' => 'Voir les utilisateurs'],
            ['module' => 'utilisateurs',    'action' => 'gerer',     'label' => 'Gérer les utilisateurs'],
            ['module' => 'utilisateurs',    'action' => 'roles',     'label' => 'Gérer les rôles RBAC'],
            ['module' => 'parametres',      'action' => 'voir',      'label' => 'Voir les paramètres'],
            ['module' => 'parametres',      'action' => 'gerer',     'label' => 'Gérer les paramètres'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->insert([
                'name'       => $p['module'] . '.' . $p['action'],
                'label'      => $p['label'],
                'module'     => $p['module'],
                'action'     => $p['action'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('permissions');
    }
};
