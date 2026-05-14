<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            [
                'name'   => 'immobilisations.parc_informatique',
                'label'  => 'Gérer le parc informatique',
                'module' => 'immobilisations',
                'action' => 'parc_informatique',
            ],
            [
                'name'   => 'immobilisations.parc_materiel',
                'label'  => 'Gérer le parc matériel',
                'module' => 'immobilisations',
                'action' => 'parc_materiel',
            ],
        ];

        foreach ($permissions as $perm) {
            DB::table('permissions')->insertOrIgnore($perm);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'immobilisations.parc_informatique',
            'immobilisations.parc_materiel',
        ])->delete();
    }
};
