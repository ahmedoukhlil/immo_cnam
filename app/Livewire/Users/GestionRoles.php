<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class GestionRoles extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRole = 'all'; // all, admin, admin_stock, agent, technicien, occupant
    public $selectedUserId = null;
    public $newRole = '';
    public $confirmingRoleChange = false;

    protected $queryString = ['search', 'filterRole'];

    /**
     * Vérification des permissions
     */
    public function mount()
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Accès non autorisé. Seuls les administrateurs peuvent gérer les rôles.');
        }
    }

    /**
     * Reset pagination lors de la recherche
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination lors du changement de filtre
     */
    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    /**
     * Confirmer le changement de rôle
     */
    public function confirmRoleChange($userId, $targetRole)
    {
        $this->selectedUserId = $userId;
        $this->newRole = $targetRole;
        $this->confirmingRoleChange = true;
    }

    /**
     * Annuler le changement de rôle
     */
    public function cancelRoleChange()
    {
        $this->confirmingRoleChange = false;
        $this->selectedUserId = null;
        $this->newRole = '';
    }

    /**
     * Changer le rôle de l'utilisateur
     */
    public function changeRole()
    {
        $user = User::find($this->selectedUserId);

        if (!$user) {
            session()->flash('error', 'Utilisateur introuvable.');
            $this->cancelRoleChange();
            return;
        }

        // Empêcher de changer son propre rôle
        if ($user->idUser === auth()->user()->idUser) {
            session()->flash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            $this->cancelRoleChange();
            return;
        }

        // Vérifier qu'il reste au moins un admin (pas admin_stock)
        if ($user->role === 'admin' && $this->newRole !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                session()->flash('error', 'Impossible de retirer le rôle admin. Il doit y avoir au moins un administrateur principal.');
                $this->cancelRoleChange();
                return;
            }
        }

        // Changer le rôle
        $oldRole = $user->role;
        $user->role = $this->newRole;
        $user->save();

        $roleName = $this->roleNames()[$this->newRole] ?? $this->newRole;
        session()->flash('success', "Le rôle de {$user->users} a été changé en {$roleName} avec succès.");

        $this->cancelRoleChange();
    }

    /**
     * Changer le rôle directement (sans confirmation)
     */
    public function toggleRole($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            session()->flash('error', 'Utilisateur introuvable.');
            return;
        }

        // Empêcher de changer son propre rôle
        if ($user->idUser === auth()->user()->idUser) {
            session()->flash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            return;
        }

        // Vérifier qu'il reste au moins un admin (pas admin_stock)
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                session()->flash('error', 'Impossible de retirer le rôle admin. Il doit y avoir au moins un administrateur principal.');
                return;
            }
        }

        $roles = ['admin', 'agent', 'technicien', 'occupant'];
        $currentIndex = array_search($user->role, $roles);
        $nextIndex = $currentIndex !== false ? ($currentIndex + 1) % count($roles) : 1;
        $user->role = $roles[$nextIndex];
        $user->save();

        $roleName = $this->roleNames()[$user->role] ?? $user->role;
        session()->flash('success', "Le rôle de {$user->users} a été changé en {$roleName} avec succès.");
    }

    protected function roleNames(): array
    {
        return [
            'admin'      => 'Administrateur',
            'agent'      => 'Agent',
            'technicien' => 'Technicien',
            'occupant'   => 'Occupant',
        ];
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where('users', 'like', '%' . $this->search . '%'))
            ->when($this->filterRole !== 'all', fn($q) => $q->where('role', $this->filterRole))
            ->orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'agent' THEN 2 WHEN 'technicien' THEN 3 WHEN 'occupant' THEN 4 ELSE 5 END")
            ->orderBy('users')
            ->paginate(20);

        $stats = [
            'total'       => User::count(),
            'admins'      => User::where('role', 'admin')->count(),
            'agents'      => User::where('role', 'agent')->count(),
            'techniciens' => User::where('role', 'technicien')->count(),
            'occupants'   => User::where('role', 'occupant')->count(),
        ];

        return view('livewire.users.gestion-roles', [
            'users' => $users,
            'stats' => $stats,
        ]);
    }
}
