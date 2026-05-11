<?php

namespace App\Livewire\Users;

use App\Models\Emplacement;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class FormUser extends Component
{
    /**
     * Propriétés publiques
     */
    public $userId = null;
    public $users = '';
    public $nom = '';
    public $email = '';
    public $mdp = '';
    public $mdp_confirmation = '';
    public $role = 'agent';
    public array $emplacementIds = [];

    /**
     * Mode édition ou création
     */
    public $isEdit = false;

    /**
     * Initialisation du composant
     * 
     * @param User|int|string|null $user Instance de l'utilisateur pour l'édition, ID, ou null pour la création
     */
    public function mount($user = null): void
    {
        if ($user) {
            // Si $user est une chaîne ou un entier (ID), charger l'utilisateur
            if (is_string($user) || is_int($user)) {
                $user = User::findOrFail($user);
            }
            
            // Vérifier que $user est bien une instance de User
            if ($user instanceof User) {
                $this->isEdit        = true;
                $this->userId        = $user->idUser;
                $this->users         = $user->users;
                $this->nom           = $user->nom ?? '';
                $this->email         = $user->email ?? '';
                $this->role          = $user->role;
                $this->emplacementIds = $user->emplacements()->pluck('emplacement.idEmplacement')->map(fn($v) => (string)$v)->toArray();
            }
        }
    }

    /**
     * Options pour SearchableSelect : Emplacements
     */
    public function getEmplacementOptionsProperty()
    {
        return Emplacement::with(['localisation', 'affectation'])
            ->orderBy('Emplacement')
            ->get()
            ->map(fn($e) => [
                'value' => (string) $e->idEmplacement,
                'text'  => $e->Emplacement . ($e->affectation ? ' — ' . $e->affectation->Affectation : ''),
            ])
            ->toArray();
    }

    /**
     * Options pour SearchableSelect : Rôles
     */
    public function getRoleOptionsProperty()
    {
        return [
            ['value' => 'agent',       'text' => 'Agent'],
            ['value' => 'admin',       'text' => 'Administrateur'],
            ['value' => 'technicien',  'text' => 'Technicien'],
            ['value' => 'occupant',    'text' => 'Occupant'],
        ];
    }

    /**
     * Règles de validation
     */
    protected function rules(): array
    {
        $rules = [
            'users' => [
                'required', 'string', 'max:60',
                $this->isEdit
                    ? 'unique:users,users,' . $this->userId . ',idUser'
                    : 'unique:users,users',
            ],
            'nom'   => 'nullable|string|max:100',
            'email' => [
                'nullable', 'email', 'max:150',
                $this->isEdit
                    ? 'unique:users,email,' . $this->userId . ',idUser'
                    : 'unique:users,email',
            ],
            'role' => 'required|in:admin,agent,technicien,occupant',
        ];

        // Règles pour le mot de passe
        if ($this->isEdit) {
            // En édition, le mot de passe est optionnel
            if (!empty($this->mdp)) {
                $rules['mdp'] = ['required', 'string', 'min:1', 'max:255', 'confirmed'];
            }
        } else {
            // En création, le mot de passe est obligatoire
            $rules['mdp'] = ['required', 'string', 'min:1', 'max:255', 'confirmed'];
        }

        return $rules;
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages(): array
    {
        return [
            'users.required' => 'Le nom d\'utilisateur est obligatoire.',
            'users.max'      => 'Le nom d\'utilisateur ne peut pas dépasser 60 caractères.',
            'users.unique'   => 'Ce nom d\'utilisateur est déjà utilisé.',
            'email.email'    => 'L\'adresse email n\'est pas valide.',
            'email.unique'   => 'Cette adresse email est déjà utilisée.',
            'mdp.required' => 'Le mot de passe est obligatoire.',
            'mdp.min' => 'Le mot de passe doit contenir au moins 1 caractère.',
            'mdp.max' => 'Le mot de passe ne peut pas dépasser 255 caractères.',
            'mdp.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle sélectionné est invalide.',
        ];
    }

    /**
     * Sauvegarde l'utilisateur (création ou édition)
     */
    public function save(): void
    {
        $this->validate();

        // Vérifier si on peut changer le rôle du dernier admin
        if ($this->isEdit) {
            $user = User::findOrFail($this->userId);
            
            // Vérifier si on change le rôle d'admin vers agent
            if ($user->role === 'admin' && $this->role === 'agent') {
                $adminsCount = User::where('role', 'admin')
                    ->where('idUser', '!=', $this->userId)
                    ->count();
                
                if ($adminsCount === 0) {
                    $this->addError('role', 'Impossible de changer le rôle du dernier administrateur.');
                    return;
                }
            }
        }

        // Préparer les données
        $data = [
            'users' => $this->users,
            'nom'   => $this->nom ?: null,
            'email' => $this->email ?: null,
            'role'  => $this->role,
        ];

        // Ajouter le mot de passe seulement s'il est fourni
        if (!empty($this->mdp)) {
            $data['mdp'] = $this->mdp; // Pas de hash, stockage en clair selon la structure
        }

        // Créer ou mettre à jour
        if ($this->isEdit) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            session()->flash('success', "L'utilisateur {$user->users} a été modifié avec succès.");
        } else {
            // En création, le mot de passe est obligatoire
            if (empty($data['mdp'])) {
                $this->addError('mdp', 'Le mot de passe est obligatoire.');
                return;
            }
            $user = User::create($data);
            session()->flash('success', "L'utilisateur {$user->users} a été créé avec succès.");
        }

        // Sync emplacements (pertinent pour occupants, autorisé pour tous)
        $user->emplacements()->sync(array_map('intval', $this->emplacementIds));

        // Rediriger vers la liste
        $this->redirect(route('users.index'), navigate: true);
    }

    /**
     * Annuler et retourner à la liste
     */
    public function cancel(): void
    {
        $this->redirect(route('users.index'), navigate: true);
    }

    /**
     * Render du composant
     */
    public function render()
    {
        return view('livewire.users.form-user');
    }
}

