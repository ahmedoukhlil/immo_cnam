<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\User;
use App\Livewire\Traits\ChecksPermission;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class GestionTickets extends Component
{
    use WithPagination, ChecksPermission;

    public function mount(): void
    {
        $this->requirePermission('tickets.assigner');
    }

    public string $search       = '';
    public string $filtreStatut = '';
    public string $filtrePriorite = '';
    public string $filtreTechnicien = '';

    // Assignement inline
    public ?int $assignTicketId   = null;
    public ?int $technicienAssignId = null;

    // Clôture inline
    public ?int $closeTicketId = null;

    protected $queryString = ['search', 'filtreStatut', 'filtrePriorite', 'filtreTechnicien'];

    public function updatingSearch(): void      { $this->resetPage(); }
    public function updatingFiltreStatut(): void { $this->resetPage(); }
    public function updatingFiltrePriorite(): void { $this->resetPage(); }
    public function updatingFiltreTechnicien(): void { $this->resetPage(); }

    // Ouvrir le modal d'assignement
    public function openAssign(int $ticketId): void
    {
        $this->assignTicketId    = $ticketId;
        $this->technicienAssignId = Ticket::find($ticketId)?->assigned_to;
    }

    public function closeAssign(): void
    {
        $this->assignTicketId     = null;
        $this->technicienAssignId = null;
    }

    public function assigner(): void
    {
        $this->validate(['technicienAssignId' => 'required|exists:users,idUser']);

        $ticket = Ticket::findOrFail($this->assignTicketId);
        $ticket->update([
            'assigned_to' => $this->technicienAssignId,
            'assigned_by' => Auth::id(),
            'statut'      => 'assigne',
            'assigned_at' => now(),
        ]);

        $this->closeAssign();
        $this->dispatch('notify', 'Ticket assigné avec succès.');
    }

    public function confirmerCloture(int $ticketId): void
    {
        $this->closeTicketId = $ticketId;
    }

    public function annulerCloture(): void
    {
        $this->closeTicketId = null;
    }

    public function cloturer(): void
    {
        $ticket = Ticket::findOrFail($this->closeTicketId);
        $ticket->update([
            'statut'      => 'ferme',
            'resolved_at' => $ticket->resolved_at ?? now(),
        ]);

        $this->closeTicketId = null;
        $this->dispatch('notify', 'Ticket clôturé.');
    }

    public function reouvrirTicket(int $ticketId): void
    {
        Ticket::findOrFail($ticketId)->update(['statut' => 'ouvert', 'assigned_to' => null, 'assigned_by' => null, 'assigned_at' => null]);
        $this->dispatch('notify', 'Ticket réouvert.');
    }

    public function render()
    {
        $techniciens = User::where('role', 'technicien')->orderBy('users')->get();

        $query = Ticket::with(['emplacement.affectation', 'bien.designation', 'createdBy', 'assignedTo', 'intervention'])
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('reference', 'like', '%'.$this->search.'%')
                  ->orWhere('titre', 'like', '%'.$this->search.'%');
            }))
            ->when($this->filtreStatut,     fn($q) => $q->where('statut',    $this->filtreStatut))
            ->when($this->filtrePriorite,   fn($q) => $q->where('priorite',  $this->filtrePriorite))
            ->when($this->filtreTechnicien, fn($q) => $q->where('assigned_to', $this->filtreTechnicien));

        // Restriction parc : admin avec périmètre restreint ne voit que les tickets de son parc
        $user = Auth::user();
        $typesParcs = $user->typesParcsAccessibles();
        if (!empty($typesParcs)) {
            $query->whereHas('bien.categorie', fn($q) => $q->whereIn('type_parc', $typesParcs));
        }

        $tickets = $query
            ->orderByRaw("FIELD(statut, 'ouvert', 'assigne', 'en_cours', 'resolu', 'ferme')")
            ->orderByRaw("FIELD(priorite, 'urgente', 'haute', 'normale', 'basse')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // KPIs
        $kpi = [
            'total'         => Ticket::count(),
            'ouverts'       => Ticket::where('statut', 'ouvert')->count(),
            'assigne'       => Ticket::where('statut', 'assigne')->count(),
            'en_cours'      => Ticket::where('statut', 'en_cours')->count(),
            'resolus'       => Ticket::whereIn('statut', ['resolu', 'ferme'])->count(),
            'urgents'       => Ticket::where('priorite', 'urgente')->whereNotIn('statut', ['ferme', 'resolu'])->count(),
        ];

        // Charge par technicien
        $chargeTechniciens = User::where('role', 'technicien')
            ->withCount([
                'ticketsAssignes as tickets_actifs' => fn($q) => $q->whereIn('statut', ['assigne', 'en_cours']),
                'ticketsAssignes as tickets_resolus' => fn($q) => $q->whereIn('statut', ['resolu', 'ferme']),
                'ticketsAssignes as tickets_total',
            ])
            ->orderByDesc('tickets_actifs')
            ->get();

        return view('livewire.tickets.gestion-tickets', compact('tickets', 'techniciens', 'kpi', 'chargeTechniciens'));
    }
}
