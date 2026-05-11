<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]

class ListeTickets extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filtreStatut = '';
    public string $filtrePriorite = '';

    protected $queryString = ['search', 'filtreStatut', 'filtrePriorite'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        $query = Ticket::with(['emplacement', 'bien', 'createdBy', 'assignedTo'])
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('titre', 'like', '%' . $this->search . '%');
            }))
            ->when($this->filtreStatut, fn($q) => $q->where('statut', $this->filtreStatut))
            ->when($this->filtrePriorite, fn($q) => $q->where('priorite', $this->filtrePriorite));

        // Un occupant ne voit que ses propres tickets
        if ($user->isOccupant()) {
            $query->where('created_by', $user->idUser);
        }

        // Un technicien voit ses tickets assignés + tous les résolus/fermés
        if ($user->isTechnicien()) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->idUser)
                  ->orWhereIn('statut', ['resolu', 'ferme']);
            });
        }

        $tickets = $query->orderByRaw("FIELD(statut, 'ouvert', 'assigne', 'en_cours', 'resolu', 'ferme')")
                         ->orderByRaw("FIELD(priorite, 'urgente', 'haute', 'normale', 'basse')")
                         ->orderBy('created_at', 'desc')
                         ->paginate(15);

        return view('livewire.tickets.liste-tickets', [
            'tickets' => $tickets,
        ]);
    }
}
