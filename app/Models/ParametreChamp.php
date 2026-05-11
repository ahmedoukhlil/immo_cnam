<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ParametreChamp extends Model
{
    protected $table = 'parametres_champs';

    protected $fillable = ['champ', 'label', 'actif'];

    protected $casts = ['actif' => 'boolean'];

    public static function isActif(string $champ): bool
    {
        return Cache::remember('parametre_champ_' . $champ, 300, function () use ($champ) {
            $p = static::where('champ', $champ)->first();
            return $p ? (bool) $p->actif : true;
        });
    }

    public static function clearCache(string $champ): void
    {
        Cache::forget('parametre_champ_' . $champ);
    }
}
