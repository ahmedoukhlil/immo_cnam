<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketIntervention extends Model
{
    protected $table = 'ticket_interventions';

    protected $fillable = [
        'ticket_id',
        'technicien_id',
        'probleme_identifie',
        'solution_appliquee',
        'observations',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function technicien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technicien_id', 'idUser');
    }

    public function piecesJointes(): HasMany
    {
        return $this->hasMany(TicketPieceJointe::class, 'intervention_id');
    }
}
