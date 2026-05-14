<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Gesimmo extends Model
{
    use HasFactory;

    protected $table = 'gesimmo';
    protected $primaryKey = 'NumOrdre';
    public $timestamps = false;

    protected $fillable = [
        'idDesignation', 'idCategorie', 'idEtat',
        'idEmplacement', 'idNatJur', 'idSF',
        'DateAcquisition', 'Observations',
        'valeur_acquisition', 'date_mise_en_service',
    ];

    protected $casts = [
        'DateAcquisition' => 'integer',
        'valeur_acquisition' => 'decimal:2',
        'date_mise_en_service' => 'date',
    ];

    /**
     * Get the route key for the model.
     * Permet d'utiliser NumOrdre dans les routes au lieu de 'id'
     */
    public function getRouteKeyName()
    {
        return 'NumOrdre';
    }

    /**
     * RELATIONS
     */

    /**
     * Relation avec la désignation
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'idDesignation', 'id');
    }

    /**
     * Relation avec la catégorie
     */
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'idCategorie', 'idCategorie');
    }

    /**
     * Relation avec l'état
     */
    public function etat(): BelongsTo
    {
        return $this->belongsTo(Etat::class, 'idEtat', 'idEtat');
    }

    /**
     * Relation avec l'emplacement
     * L'emplacement est la table centrale qui lie LocalisationImmo et Affectation
     */
    public function emplacement(): BelongsTo
    {
        return $this->belongsTo(Emplacement::class, 'idEmplacement', 'idEmplacement');
    }

    /**
     * Relation avec la nature juridique
     */
    public function natureJuridique(): BelongsTo
    {
        return $this->belongsTo(NatureJuridique::class, 'idNatJur', 'idNatJur');
    }

    /**
     * Relation avec la source de financement
     */
    public function sourceFinancement(): BelongsTo
    {
        return $this->belongsTo(SourceFinancement::class, 'idSF', 'idSF');
    }

    /**
     * Relation avec le code-barres
     */
    public function code(): HasOne
    {
        return $this->hasOne(Code::class, 'idGesimmo', 'NumOrdre');
    }

    /**
     * Tickets de maintenance liés à ce bien
     */
    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class, 'bien_id', 'NumOrdre');
    }

    /**
     * ACCESSORS
     */

    /**
     * Génère le code d'immobilisation au format :
     * CHAIB/MMOB/AA/CodeLocalisation/CodeAffectation/CodeEmplacement/NumOrdre
     * AA = 2 derniers chiffres de l'année d'acquisition (ex: 2026 → 26)
     */
    public function getCodeFormateAttribute(): string
    {
        if (!$this->relationLoaded('emplacement')) {
            $this->load('emplacement.localisation', 'emplacement.affectation');
        } elseif ($this->emplacement) {
            if (!$this->emplacement->relationLoaded('localisation')) {
                $this->emplacement->load('localisation');
            }
            if (!$this->emplacement->relationLoaded('affectation')) {
                $this->emplacement->load('affectation');
            }
        }

        $annee            = ($this->DateAcquisition > 1970) ? substr((string) $this->DateAcquisition, -2) : '';
        $codeLocalisation = $this->emplacement->localisation->CodeLocalisation ?? '';
        $codeAffectation  = $this->emplacement->affectation->CodeAffectation   ?? '';
        $codeEmplacement  = $this->emplacement->CodeEmplacement                ?? '';

        return sprintf(
            'CHAIB/MMOB/%s/%s/%s/%s/%d',
            $annee,
            strtoupper($codeLocalisation),
            strtoupper($codeAffectation),
            strtoupper($codeEmplacement),
            $this->NumOrdre
        );
    }

    /**
     * Génère et sauvegarde le code-barres Code 128 pour cette immobilisation
     * 
     * @return string Le code-barres en SVG (base64 ou SVG direct)
     */
    public function generateBarcode(): string
    {
        $barcodeService = app(\App\Services\BarcodeService::class);
        return $barcodeService->generateForGesimmo($this);
    }
}
