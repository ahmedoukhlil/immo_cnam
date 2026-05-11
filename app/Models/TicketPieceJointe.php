<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketPieceJointe extends Model
{
    protected $table = 'ticket_pieces_jointes';

    protected $fillable = [
        'intervention_id',
        'nom_fichier',
        'chemin',
        'type_mime',
        'taille',
    ];

    public function intervention(): BelongsTo
    {
        return $this->belongsTo(TicketIntervention::class, 'intervention_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->chemin);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->type_mime ?? '', 'image/');
    }
}
