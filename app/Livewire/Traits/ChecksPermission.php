<?php

namespace App\Livewire\Traits;

use Illuminate\Support\Facades\Auth;

trait ChecksPermission
{
    /**
     * Vérifie une permission et redirige vers 403 si non autorisé.
     */
    protected function requirePermission(string $permission): void
    {
        if (!Auth::check() || !Auth::user()->hasPermission($permission)) {
            abort(403, 'Accès non autorisé.');
        }
    }
}
