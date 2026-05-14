<?php

namespace App\Livewire\Parametres;

use App\Models\Etat;
use App\Livewire\Traits\ChecksPermission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ListeEtats extends Component
{
    use ChecksPermission;

    public function mount(): void
    {
        $this->requirePermission('parametres.gerer');
    }
    public string $libelle = '';
    public string $code = '';
    public ?int $editId = null;
    public bool $showForm = false;

    protected function rules(): array
    {
        return [
            'libelle' => 'required|string|max:255',
            'code'    => 'nullable|string|max:50',
        ];
    }

    protected function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé est obligatoire.',
            'libelle.max'      => 'Le libellé ne peut pas dépasser 255 caractères.',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['libelle', 'code', 'editId']);
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $etat = Etat::findOrFail($id);
        $this->editId  = $id;
        $this->libelle = $etat->Etat;
        $this->code    = $etat->CodeEtat ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            Etat::findOrFail($this->editId)->update([
                'Etat'     => $this->libelle,
                'CodeEtat' => $this->code ?: null,
            ]);
            session()->flash('success', 'État modifié avec succès.');
        } else {
            Etat::create([
                'Etat'     => $this->libelle,
                'CodeEtat' => $this->code ?: null,
                'actif'    => true,
            ]);
            session()->flash('success', 'État créé avec succès.');
        }

        $this->reset(['libelle', 'code', 'editId', 'showForm']);
    }

    public function toggleActif(int $id): void
    {
        $etat = Etat::findOrFail($id);
        $etat->actif = !$etat->actif;
        $etat->save();
        \Illuminate\Support\Facades\Cache::forget('etat_options_actifs');
    }

    public function delete(int $id): void
    {
        $etat = Etat::findOrFail($id);
        if ($etat->immobilisations()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer : des immobilisations utilisent cet état.');
            return;
        }
        $etat->delete();
        session()->flash('success', 'État supprimé.');
    }

    public function cancel(): void
    {
        $this->reset(['libelle', 'code', 'editId', 'showForm']);
    }

    public function render()
    {
        return view('livewire.parametres.liste-etats', [
            'etats' => Etat::orderBy('Etat')->get(),
        ]);
    }
}
