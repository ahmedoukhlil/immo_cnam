<?php

namespace App\Livewire\Parametres;

use App\Models\Categorie;
use App\Livewire\Traits\ChecksPermission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ListeCategories extends Component
{
    use ChecksPermission;

    public function mount(): void
    {
        $this->requirePermission('parametres.gerer');
    }
    public ?int $editId = null;
    public string $typeParc = '';

    public function setTypeParc(int $id, string $value): void
    {
        $cat = Categorie::findOrFail($id);
        $cat->type_parc = $value ?: null;
        $cat->save();
        \Illuminate\Support\Facades\Cache::forget('liste_biens_categories');
    }

    public function render()
    {
        return view('livewire.parametres.liste-categories', [
            'categories' => Categorie::orderBy('Categorie')->get(),
        ]);
    }
}
