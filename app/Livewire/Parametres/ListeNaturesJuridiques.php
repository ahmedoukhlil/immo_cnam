<?php

namespace App\Livewire\Parametres;

use App\Models\NatureJuridique;
use App\Livewire\Traits\ChecksPermission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ListeNaturesJuridiques extends Component
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
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['libelle', 'code', 'editId']);
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $natJur = NatureJuridique::findOrFail($id);
        $this->editId  = $id;
        $this->libelle = $natJur->NatJur;
        $this->code    = $natJur->CodeNatJur ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            NatureJuridique::findOrFail($this->editId)->update([
                'NatJur'     => $this->libelle,
                'CodeNatJur' => $this->code ?: null,
            ]);
            session()->flash('success', 'Nature juridique modifiée avec succès.');
        } else {
            NatureJuridique::create([
                'NatJur'     => $this->libelle,
                'CodeNatJur' => $this->code ?: null,
                'actif'      => true,
            ]);
            session()->flash('success', 'Nature juridique créée avec succès.');
        }

        $this->reset(['libelle', 'code', 'editId', 'showForm']);
    }

    public function toggleActif(int $id): void
    {
        $natJur = NatureJuridique::findOrFail($id);
        $natJur->actif = !$natJur->actif;
        $natJur->save();
        \Illuminate\Support\Facades\Cache::forget('natjur_options_actifs');
    }

    public function delete(int $id): void
    {
        $natJur = NatureJuridique::findOrFail($id);
        if ($natJur->immobilisations()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer : des immobilisations utilisent cette nature juridique.');
            return;
        }
        $natJur->delete();
        session()->flash('success', 'Nature juridique supprimée.');
    }

    public function cancel(): void
    {
        $this->reset(['libelle', 'code', 'editId', 'showForm']);
    }

    public function render()
    {
        return view('livewire.parametres.liste-natures-juridiques', [
            'natures' => NatureJuridique::orderBy('NatJur')->get(),
        ]);
    }
}
