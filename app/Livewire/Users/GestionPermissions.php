<?php

namespace App\Livewire\Users;

use App\Models\Permission;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class GestionPermissions extends Component
{
    public User $user;
    public array $selectedPermissions = [];
    public array $defaultPermissions = [];

    public function mount(int $userId): void
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403);
        }

        $this->user = User::findOrFail($userId);
        $this->defaultPermissions = Permission::defaultsForRole($this->user->role);
        $this->selectedPermissions = $this->user->effectivePermissions();
    }

    public function save(): void
    {
        $allPermissions = Permission::all();
        $syncData = [];

        foreach ($allPermissions as $permission) {
            $isSelected = in_array($permission->name, $this->selectedPermissions);
            $isDefault  = in_array($permission->name, $this->defaultPermissions);

            // On enregistre seulement les surcharges (différences du défaut du rôle)
            if ($isSelected !== $isDefault) {
                $syncData[$permission->id] = ['granted' => $isSelected];
            }
        }

        $this->user->permissions()->sync($syncData);
        session()->flash('success', "Permissions de {$this->user->users} mises à jour.");
    }

    public function resetToDefaults(): void
    {
        $this->user->permissions()->detach();
        $this->selectedPermissions = $this->defaultPermissions;
        session()->flash('success', "Permissions réinitialisées aux valeurs par défaut du rôle.");
    }

    public function toggleModule(string $module): void
    {
        $modulePerms = Permission::where('module', $module)->pluck('name')->toArray();
        $allChecked  = count(array_intersect($modulePerms, $this->selectedPermissions)) === count($modulePerms);

        if ($allChecked) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $modulePerms));
        } else {
            $this->selectedPermissions = array_values(array_unique(array_merge($this->selectedPermissions, $modulePerms)));
        }
    }

    public function render()
    {
        $permissions = Permission::all()->groupBy('module');

        $moduleLabels = [
            'dashboard'       => '🏠 Dashboard',
            'immobilisations' => '🏗️ Immobilisations',
            'inventaires'     => '📋 Inventaires',
            'stock'           => '📦 Stock',
            'tickets'         => '🎫 Tickets',
            'utilisateurs'    => '👥 Utilisateurs',
            'parametres'      => '⚙️ Paramètres',
        ];

        return view('livewire.users.gestion-permissions', [
            'permissions'  => $permissions,
            'moduleLabels' => $moduleLabels,
        ]);
    }
}
