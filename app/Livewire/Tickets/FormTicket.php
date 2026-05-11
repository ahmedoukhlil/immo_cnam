<?php

namespace App\Livewire\Tickets;

use App\Models\Gesimmo;
use App\Models\Emplacement;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]

class FormTicket extends Component
{
    public ?int $idEmplacement = null;
    public ?int $bien_id = null;
    public string $titre = '';
    public string $description = '';
    public string $priorite = 'normale';

    public $emplacements = [];
    public $biens = [];

    public function mount(): void
    {
        $user = Auth::user();

        // Occupant : uniquement ses emplacements assignés
        if ($user->isOccupant()) {
            $this->emplacements = $user->emplacements()->get();
            // Pré-sélectionner automatiquement si un seul emplacement
            if ($this->emplacements->count() === 1) {
                $this->idEmplacement = $this->emplacements->first()->idEmplacement;
                $this->updatedIdEmplacement();
            }
        } else {
            $this->emplacements = Emplacement::orderBy('Emplacement')->get();
        }
    }

    public function updatedIdEmplacement(): void
    {
        $this->bien_id = null;
        $this->biens = $this->idEmplacement
            ? Gesimmo::with('designation')
                ->where('idEmplacement', $this->idEmplacement)
                ->orderBy('NumOrdre')
                ->get()
            : collect();
    }

    public function save(): void
    {
        $this->validate([
            'idEmplacement' => 'required|exists:emplacement,idEmplacement',
            'bien_id'       => 'nullable|exists:gesimmo,NumOrdre',
            'titre'         => 'required|string|max:255',
            'description'   => 'required|string',
            'priorite'      => 'required|in:basse,normale,haute,urgente',
        ]);

        $user = Auth::user();

        // Vérifier que l'occupant est bien assigné à cet emplacement
        if ($user->isOccupant()) {
            $assigned = $user->emplacements()->where('emplacement.idEmplacement', $this->idEmplacement)->exists();
            if (!$assigned) {
                $this->addError('idEmplacement', 'Vous n\'êtes pas responsable de cet emplacement.');
                return;
            }
        }

        Ticket::create([
            'reference'     => Ticket::generateReference(),
            'idEmplacement' => $this->idEmplacement,
            'bien_id'       => $this->bien_id ?: null,
            'created_by'    => $user->idUser,
            'titre'         => $this->titre,
            'description'   => $this->description,
            'priorite'      => $this->priorite,
            'statut'        => 'ouvert',
        ]);

        session()->flash('success', 'Ticket créé avec succès.');
        $this->redirect(route('tickets.index'));
    }

    public function render()
    {
        return view('livewire.tickets.form-ticket');
    }
}
