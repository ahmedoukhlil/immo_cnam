<?php

namespace App\Livewire\Parametres;

use App\Models\ParametreChamp;
use App\Livewire\Traits\ChecksPermission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ParametresChamps extends Component
{
    use ChecksPermission;
    public array $champs = [];

    public function mount(): void
    {
        $this->requirePermission('parametres.gerer');
        $this->charger();
    }

    private function charger(): void
    {
        $this->champs = ParametreChamp::all()->keyBy('champ')->map(fn($p) => [
            'id'    => $p->id,
            'label' => $p->label,
            'actif' => (bool) $p->actif,
        ])->toArray();
    }

    public function toggle(string $champ): void
    {
        $p = ParametreChamp::where('champ', $champ)->firstOrFail();
        $p->actif = !$p->actif;
        $p->save();

        ParametreChamp::clearCache($champ);

        $this->charger();

        session()->flash('success', "Paramètre \"{$p->label}\" " . ($p->actif ? 'activé' : 'désactivé') . " avec succès.");
    }

    public function render()
    {
        return view('livewire.parametres.parametres-champs');
    }
}
