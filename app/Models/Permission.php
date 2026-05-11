<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'label', 'module', 'action'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions', 'permission_id', 'user_id', 'id', 'idUser')
                    ->withPivot('granted');
    }

    // Permissions par défaut selon le rôle
    public static function defaultsForRole(string $role): array
    {
        return match($role) {
            'admin' => [
                'dashboard.voir',
                'dashboard.stats_immobilisations',
                'dashboard.stats_inventaire',
                'dashboard.stats_tickets',
                'dashboard.activite_recente',
                'dashboard.actions_rapides',
                'immobilisations.voir', 'immobilisations.creer', 'immobilisations.modifier', 'immobilisations.supprimer',
                'inventaires.voir', 'inventaires.creer', 'inventaires.executer',
                'stock.voir', 'stock.gerer',
                'tickets.voir', 'tickets.creer', 'tickets.assigner', 'tickets.traiter', 'tickets.fermer',
                'utilisateurs.voir', 'utilisateurs.gerer', 'utilisateurs.roles',
                'parametres.voir', 'parametres.gerer',
            ],
            'agent' => [
                'dashboard.voir',
                'dashboard.stats_immobilisations',
                'dashboard.stats_inventaire',
                'dashboard.stats_tickets',
                'dashboard.activite_recente',
                'immobilisations.voir', 'immobilisations.creer', 'immobilisations.modifier',
                'inventaires.voir', 'inventaires.creer', 'inventaires.executer',
                'stock.voir', 'stock.gerer',
                'tickets.voir', 'tickets.creer',
                'parametres.voir',
            ],
            'technicien' => [
                'dashboard.voir',
                'tickets.voir', 'tickets.traiter',
            ],
            'occupant' => [
                'dashboard.voir',
                'tickets.voir', 'tickets.creer',
            ],
            default => [],
        };
    }
}
