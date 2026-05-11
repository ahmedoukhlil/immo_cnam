<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Ticket extends Model
{
    protected $fillable = [
        'reference',
        'idEmplacement',
        'bien_id',
        'created_by',
        'assigned_to',
        'assigned_by',
        'titre',
        'description',
        'priorite',
        'statut',
        'assigned_at',
        'resolved_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function emplacement(): BelongsTo
    {
        return $this->belongsTo(Emplacement::class, 'idEmplacement', 'idEmplacement');
    }

    public function bien(): BelongsTo
    {
        // bien_id stocke le NumOrdre de la table gesimmo
        return $this->belongsTo(Gesimmo::class, 'bien_id', 'NumOrdre');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'idUser');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to', 'idUser');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by', 'idUser');
    }

    public function intervention(): HasOne
    {
        return $this->hasOne(TicketIntervention::class);
    }

    public function scopeOuverts(Builder $query): Builder
    {
        return $query->where('statut', 'ouvert');
    }

    public function scopeAssignes(Builder $query): Builder
    {
        return $query->whereIn('statut', ['assigne', 'en_cours']);
    }

    public function scopeResolus(Builder $query): Builder
    {
        return $query->whereIn('statut', ['resolu', 'ferme']);
    }

    public function scopePourTechnicien(Builder $query, int $userId): Builder
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopePourOccupant(Builder $query, int $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'ouvert'   => 'Ouvert',
            'assigne'  => 'Assigné',
            'en_cours' => 'En cours',
            'resolu'   => 'Résolu',
            'ferme'    => 'Fermé',
            default    => $this->statut,
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match($this->statut) {
            'ouvert'   => 'yellow',
            'assigne'  => 'blue',
            'en_cours' => 'indigo',
            'resolu'   => 'green',
            'ferme'    => 'gray',
            default    => 'gray',
        };
    }

    public function getPrioriteColorAttribute(): string
    {
        return match($this->priorite) {
            'basse'   => 'green',
            'normale' => 'blue',
            'haute'   => 'orange',
            'urgente' => 'red',
            default   => 'gray',
        };
    }

    public static function generateReference(): string
    {
        $annee = now()->year;
        $last = self::where('reference', 'like', "TKT-{$annee}-%")
            ->orderBy('reference', 'desc')
            ->first();

        $next = $last ? ((int) substr($last->reference, -4)) + 1 : 1;

        return sprintf('TKT-%d-%04d', $annee, $next);
    }
}
