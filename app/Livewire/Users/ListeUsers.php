<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class ListeUsers extends Component
{
    use WithPagination;

    /**
     * Propriétés publiques pour les filtres et la recherche
     */
    public $search = '';
    public $filterRole = 'all'; // all, admin, agent, superuser, immobilisation, stock
    public $sortField = 'users';
    public $sortDirection = 'asc';
    public $perPage = 20;
    public $selectedUsers = [];

    /**
     * Initialisation du composant
     */
    public function mount(): void
    {
        $this->resetPage();
    }

    /**
     * Réinitialiser les filtres
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterRole = 'all';
        $this->selectedUsers = [];
        $this->resetPage();
    }

    /**
     * Trier par champ
     */
    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    /**
     * Toggle sélection d'un utilisateur
     */
    public function toggleSelect($userId): void
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
        } else {
            $this->selectedUsers[] = $userId;
        }
    }

    /**
     * Sélectionner/désélectionner tous les utilisateurs
     */
    public function toggleSelectAll(): void
    {
        if (count($this->selectedUsers) === $this->getUsersQuery()->count()) {
            $this->selectedUsers = [];
        } else {
            $this->selectedUsers = $this->getUsersQuery()->pluck('idUser')->toArray();
        }
    }

    // Note: La table users n'a pas de colonne 'actif', cette fonctionnalité n'est pas disponible

    /**
     * Supprimer un utilisateur
     */
    public function delete($userId): void
    {
        $user = User::findOrFail($userId);

        if ($user->idUser === auth()->user()->idUser) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return;
        }

        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->where('idUser', '!=', $userId)->count();
            if ($adminCount === 0) {
                session()->flash('error', 'Impossible de supprimer le dernier administrateur.');
                return;
            }
        }

        $userName = $user->users;

        // Vérifier les dépendances non-nullables qui bloqueraient la suppression
        $stockEntrees  = \DB::table('stock_entrees')->where('created_by', $user->idUser)->count();
        $stockSorties  = \DB::table('stock_sorties')->where('created_by', $user->idUser)->count();
        $transferts    = \DB::table('historique_transferts')->where('transfert_par', $user->idUser)->count();
        $ticketsCreés  = \DB::table('tickets')->where('created_by', $user->idUser)->count();
        $interventions = \DB::table('ticket_interventions')->where('technicien_id', $user->idUser)->count();

        $total = $stockEntrees + $stockSorties + $transferts + $ticketsCreés + $interventions;
        if ($total > 0) {
            $details = collect([
                $stockEntrees  ? "{$stockEntrees} entrée(s) de stock"      : null,
                $stockSorties  ? "{$stockSorties} sortie(s) de stock"      : null,
                $transferts    ? "{$transferts} transfert(s)"               : null,
                $ticketsCreés  ? "{$ticketsCreés} ticket(s) créé(s)"        : null,
                $interventions ? "{$interventions} intervention(s)"         : null,
            ])->filter()->implode(', ');
            session()->flash('error', "Impossible de supprimer {$userName} : il est lié à {$details}. Réassignez ces données avant de supprimer.");
            return;
        }

        // Détacher les relations pivot
        $user->emplacements()->detach();
        $user->permissions()->detach();

        // Nullifier les FK nullable
        \DB::table('tickets')->where('assigned_by', $user->idUser)->update(['assigned_by' => null]);
        \DB::table('tickets')->where('assigned_to', $user->idUser)->update(['assigned_to' => null]);

        $user->delete();

        session()->flash('success', "L'utilisateur {$userName} a été supprimé avec succès.");
        $this->resetPage();
    }

    /**
     * Requête de base pour les utilisateurs
     */
    protected function getUsersQuery()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('users', 'like', '%' . $this->search . '%')
                  ->orWhere('nom',   'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Filtre par rôle
        if ($this->filterRole !== 'all') {
            $query->where('role', $this->filterRole);
        }

        // Note: La table users n'a pas de colonne 'actif', ce filtre a été supprimé

        // Tri
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    /**
     * Propriété calculée : Liste paginée des utilisateurs
     */
    public function getUsersProperty()
    {
        return $this->getUsersQuery()->paginate($this->perPage);
    }

    /**
     * Propriété calculée : Statistiques
     */
    public function getStatsProperty()
    {
        return [
            'total'        => User::count(),
            'admins'       => User::where('role', 'admin')->count(),
            'agents'       => User::where('role', 'agent')->count(),
            'techniciens'  => User::where('role', 'technicien')->count(),
            'occupants'    => User::where('role', 'occupant')->count(),
        ];
    }

    /**
     * Render du composant
     */
    public function render()
    {
        return view('livewire.users.liste-users', [
            'users' => $this->users,
            'stats' => $this->stats,
        ]);
    }
}

