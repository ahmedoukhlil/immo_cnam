<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParametreChamp;

class ParametresChampsSeeder extends Seeder
{
    public function run(): void
    {
        $champs = [
            ['champ' => 'etat',               'label' => 'État',                'actif' => true],
            ['champ' => 'nature_juridique',   'label' => 'Nature Juridique',    'actif' => true],
            ['champ' => 'source_financement', 'label' => 'Source de Financement', 'actif' => true],
        ];

        foreach ($champs as $data) {
            ParametreChamp::updateOrCreate(['champ' => $data['champ']], $data);
        }
    }
}
