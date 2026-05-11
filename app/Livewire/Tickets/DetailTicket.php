<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]

class DetailTicket extends Component
{
    public Ticket $ticket;
    public ?int $technicienId = null;

    public $techniciens = [];

    public function mount(Ticket $ticket): void
    {
        $this->ticket = $ticket->load(['emplacement', 'bien', 'createdBy', 'assignedTo', 'assignedBy', 'intervention.technicien', 'intervention.piecesJointes']);
        $this->techniciens = User::where('role', 'technicien')->get();
        $this->technicienId = $ticket->assigned_to;
    }

    public function assigner(): void
    {
        $this->validate(['technicienId' => 'required|exists:users,idUser']);

        $user = Auth::user();
        if (!$user->isAdmin()) {
            session()->flash('error', 'Seul un administrateur peut assigner un ticket.');
            return;
        }

        $this->ticket->update([
            'assigned_to'  => $this->technicienId,
            'assigned_by'  => $user->idUser,
            'statut'       => 'assigne',
            'assigned_at'  => now(),
        ]);

        $this->ticket->refresh();
        session()->flash('success', 'Ticket assigné au technicien.');
    }

    public function demarrerIntervention(): void
    {
        $user = Auth::user();
        if (!$user->isTechnicien() || $this->ticket->assigned_to !== $user->idUser) {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        $this->ticket->update(['statut' => 'en_cours']);
        $this->ticket->refresh();
        session()->flash('success', 'Intervention démarrée.');
    }

    public function render()
    {
        return view('livewire.tickets.detail-ticket');
    }
}
