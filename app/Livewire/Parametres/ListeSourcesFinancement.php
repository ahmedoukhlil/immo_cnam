<?php

namespace App\Livewire\Parametres;

use App\Models\SourceFinancement;
use App\Livewire\Traits\ChecksPermission;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ListeSourcesFinancement extends Component
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
        $sf = SourceFinancement::findOrFail($id);
        $this->editId  = $id;
        $this->libelle = $sf->SourceFin;
        $this->code    = $sf->CodeSourceFin ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            SourceFinancement::findOrFail($this->editId)->update([
                'SourceFin'     => $this->libelle,
                'CodeSourceFin' => $this->code ?: null,
            ]);
            session()->flash('success', 'Source de financement modifiée avec succès.');
        } else {
            SourceFinancement::create([
                'SourceFin'     => $this->libelle,
                'CodeSourceFin' => $this->code ?: null,
                'actif'         => true,
            ]);
            session()->flash('success', 'Source de financement créée avec succès.');
        }

        $this->reset(['libelle', 'code', 'editId', 'showForm']);
    }

    public function toggleActif(int $id): void
    {
        $sf = SourceFinancement::findOrFail($id);
        $sf->actif = !$sf->actif;
        $sf->save();
        \Illuminate\Support\Facades\Cache::forget('sf_options_actifs');
    }

    public function delete(int $id): void
    {
        $sf = SourceFinancement::findOrFail($id);
        if ($sf->immobilisations()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer : des immobilisations utilisent cette source de financement.');
            return;
        }
        $sf->delete();
        session()->flash('success', 'Source de financement supprimée.');
    }

    public function cancel(): void
    {
        $this->reset(['libelle', 'code', 'editId', 'showForm']);
    }

    public function render()
    {
        return view('livewire.parametres.liste-sources-financement', [
            'sources' => SourceFinancement::orderBy('SourceFin')->get(),
        ]);
    }
}
