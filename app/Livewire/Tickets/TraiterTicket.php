<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\TicketIntervention;
use App\Models\TicketPieceJointe;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]

class TraiterTicket extends Component
{
    use WithFileUploads;

    public Ticket $ticket;
    public string $problemeIdentifie = '';
    public string $solutionAppliquee = '';
    public string $observations = '';
    public array $photos = [];

    public function mount(Ticket $ticket): void
    {
        $this->ticket = $ticket->load(['emplacement', 'bien', 'createdBy']);

        $user = Auth::user();
        if (!$user->isTechnicien() || $ticket->assigned_to !== $user->idUser) {
            abort(403, 'Ce ticket ne vous est pas assigné.');
        }

        if (!in_array($ticket->statut, ['assigne', 'en_cours'])) {
            abort(403, 'Ce ticket ne peut plus être traité.');
        }
    }

    public function save(): void
    {
        $this->validate([
            'problemeIdentifie' => 'required|string',
            'solutionAppliquee' => 'required|string',
            'observations'      => 'nullable|string',
            'photos'            => 'nullable|array|max:10',
            'photos.*'          => 'image|max:5120', // 5 MB max
        ]);

        $user = Auth::user();

        $intervention = TicketIntervention::create([
            'ticket_id'           => $this->ticket->id,
            'technicien_id'       => $user->idUser,
            'probleme_identifie'  => $this->problemeIdentifie,
            'solution_appliquee'  => $this->solutionAppliquee,
            'observations'        => $this->observations ?: null,
        ]);

        foreach ($this->photos as $photo) {
            $chemin = $photo->store('tickets/interventions', 'public');
            TicketPieceJointe::create([
                'intervention_id' => $intervention->id,
                'nom_fichier'     => $photo->getClientOriginalName(),
                'chemin'          => $chemin,
                'type_mime'       => $photo->getMimeType(),
                'taille'          => $photo->getSize(),
            ]);
        }

        $this->ticket->update([
            'statut'      => 'resolu',
            'resolved_at' => now(),
        ]);

        session()->flash('success', 'Ticket résolu avec succès. Le rapport a été enregistré.');
        $this->redirect(route('tickets.show', $this->ticket));
    }

    public function render()
    {
        return view('livewire.tickets.traiter-ticket');
    }
}
