<?php

namespace App\Livewire\Biens;

use App\Models\Gesimmo;
use App\Models\Designation;
use App\Models\Categorie;
use App\Models\Etat;
use App\Models\Emplacement;
use App\Models\LocalisationImmo;
use App\Models\Affectation;
use App\Models\NatureJuridique;
use App\Models\SourceFinancement;
use App\Models\ParametreChamp;
use App\Livewire\Traits\WithCachedOptions;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FormBien extends Component
{
    use WithCachedOptions;
    /**
     * Instance de l'immobilisation (null si création)
     */
    public $bien = null;

    /**
     * ID de l'immobilisation pour l'édition
     */
    public $bienId = null;

    /**
     * Propriétés du formulaire
     */
    public $idDesignation = '';
    public $idCategorie = '';
    public $idEtat = '';
    public $idLocalisation = ''; // Pour le filtrage hiérarchique
    public $idAffectation = ''; // Pour le filtrage hiérarchique
    public $idEmplacement = '';
    public $idNatJur = '';
    public $idSF = '';
    public $DateAcquisition = '';
    public $valeur_acquisition = '';
    public $date_mise_en_service = '';
    public $quantite = 1;
    public $code_formate = '';
    public $codeFormateSuggere = '';

    /**
     * Mise à jour automatique de la catégorie lorsque la désignation change
     */
    public function updatedIdDesignation($value)
    {
        if (!empty($value)) {
            $designation = Designation::with('categorie')->find($value);
            if ($designation && $designation->categorie) {
                $this->idCategorie = $designation->categorie->idCategorie;
            } else {
                $this->idCategorie = '';
            }
        } else {
            $this->idCategorie = '';
        }
    }

    /**
     * Réagit au changement de localisation
     * Réinitialise l'affectation et l'emplacement
     */
    public function updatedIdLocalisation($value)
    {
        // Vérifier si l'affectation actuelle appartient toujours à la nouvelle localisation
        if (!empty($this->idAffectation)) {
            $affectation = Affectation::find($this->idAffectation);
            if (!$affectation || $affectation->idLocalisation != $value) {
                $this->idAffectation = '';
                $this->idEmplacement = '';
            }
        } else {
            $this->idEmplacement = '';
        }
    }

    /**
     * Réagit au changement d'affectation
     * Réinitialise l'emplacement
     */
    public function updatedIdAffectation($value)
    {
        if (!empty($this->idEmplacement)) {
            $emplacement = Emplacement::find($this->idEmplacement);
            if (!$emplacement || $emplacement->idAffectation != $value) {
                $this->idEmplacement = '';
            }
        }
        $this->recalculerCodeSuggere();
    }

    public function updatedIdEmplacement($value)
    {
        $this->recalculerCodeSuggere();
    }

    public function updatedDateAcquisition($value)
    {
        $this->recalculerCodeSuggere();
    }

    private function recalculerCodeSuggere(): void
    {
        if (empty($this->idEmplacement) || empty($this->DateAcquisition)) {
            $this->codeFormateSuggere = '';
            return;
        }

        $emplacement = Emplacement::with(['localisation', 'affectation'])->find($this->idEmplacement);
        if (!$emplacement) {
            $this->codeFormateSuggere = '';
            return;
        }

        $annee           = (int) $this->DateAcquisition;
        $codeLocalisation = $emplacement->localisation->CodeLocalisation ?? '';
        $codeAffectation  = $emplacement->affectation->CodeAffectation   ?? '';
        $codeEmplacement  = $emplacement->CodeEmplacement                 ?? '';

        // NumOrdre = prochain auto-increment (estimation)
        $nextNum = (\App\Models\Gesimmo::max('NumOrdre') ?? 0) + 1;

        $this->codeFormateSuggere = \App\Models\Gesimmo::genererCodeSuggere(
            $nextNum,
            $annee,
            $codeLocalisation,
            $codeAffectation,
            $codeEmplacement
        );
    }

    /**
     * Initialisation du composant
     * 
     * @param Gesimmo|null $bien Instance de l'immobilisation pour l'édition, null pour la création
     */
    public function mount($bien = null): void
    {
        if ($bien) {
            $this->bien = $bien;
            $this->bienId = $bien->NumOrdre;
            $this->idDesignation = (string) ($bien->idDesignation ?? '');
            $this->idCategorie = (string) ($bien->idCategorie ?? '');
            $this->idEtat = (string) ($bien->idEtat ?? '');
            $this->idEmplacement = (string) ($bien->idEmplacement ?? '');
            $this->idNatJur = (string) ($bien->idNatJur ?? '');
            $this->idSF = (string) ($bien->idSF ?? '');
            $this->DateAcquisition = $bien->DateAcquisition ?? '';
            $this->valeur_acquisition = $bien->valeur_acquisition ?? '';
            $this->date_mise_en_service = $bien->date_mise_en_service ? $bien->date_mise_en_service->format('Y-m-d') : '';
            $this->code_formate = $bien->attributes['code_formate'] ?? '';

            if ($bien->idEmplacement) {
                $emplacement = Emplacement::find($bien->idEmplacement);
                if ($emplacement) {
                    $this->idLocalisation = (string) ($emplacement->idLocalisation ?? '');
                    $this->idAffectation = (string) ($emplacement->idAffectation ?? '');
                }
            }
        } else {
            $this->DateAcquisition = now()->year;
            $this->quantite = 1;
        }
    }

    /**
     * Propriété calculée : Retourne toutes les désignations
     */
    public function getDesignationsProperty()
    {
        return Designation::with('categorie')
            ->orderBy('designation')
            ->get();
    }

    /**
     * Options pour SearchableSelect : Désignations
     */
    public function getDesignationOptionsProperty()
    {
        return Designation::with('categorie')
            ->orderBy('designation')
            ->get()
            ->map(function ($designation) {
                return [
                    'value' => (string)$designation->id,
                    'text' => $designation->designation . ($designation->categorie ? ' (' . $designation->categorie->Categorie . ')' : ''),
                ];
            })
            ->toArray();
    }

    /**
     * Propriété calculée : Retourne toutes les catégories
     */
    public function getCategoriesProperty()
    {
        return Categorie::orderBy('Categorie')->get();
    }

    /**
     * Options pour SearchableSelect : Catégories
     */
    public function getCategorieOptionsProperty()
    {
        return Categorie::orderBy('Categorie')
            ->get()
            ->map(function ($categorie) {
                return [
                    'value' => (string)$categorie->idCategorie,
                    'text' => $categorie->Categorie,
                ];
            })
            ->toArray();
    }

    /**
     * Propriété calculée : Retourne tous les états
     */
    public function getEtatsProperty()
    {
        return Etat::orderBy('Etat')->get();
    }

    /**
     * Options pour SearchableSelect : États
     */
    public function getEtatOptionsProperty()
    {
        return Etat::actif()->orderBy('Etat')
            ->get()
            ->map(function ($etat) {
                return [
                    'value' => (string)$etat->idEtat,
                    'text' => $etat->Etat,
                ];
            })
            ->toArray();
    }

    /**
     * Options pour SearchableSelect : Localisations
     * Optimisé avec cache pour des performances ultra-rapides
     */
    public function getLocalisationOptionsProperty()
    {
        return $this->getCachedLocalisationOptions();
    }

    /**
     * Options pour SearchableSelect : Affectations
     * Filtrées selon la localisation sélectionnée
     * Optimisé avec cache pour des performances ultra-rapides
     */
    public function getAffectationOptionsProperty()
    {
        return $this->getCachedAffectationOptions($this->idLocalisation);
    }

    /**
     * Propriété calculée : Retourne tous les emplacements avec leurs relations
     * Groupés par localisation pour faciliter la sélection
     */
    public function getEmplacementsProperty()
    {
        return Emplacement::with(['localisation', 'affectation'])
            ->orderBy('Emplacement')
            ->get()
            ->map(function ($emplacement) {
                // Ajouter un attribut calculé pour l'affichage
                $emplacement->display_name = $this->getEmplacementDisplayName($emplacement);
                return $emplacement;
            });
    }

    /**
     * Options pour SearchableSelect : Emplacements
     * Filtrés selon la localisation et l'affectation sélectionnées
     * Optimisé pour des performances instantanées
     */
    public function getEmplacementOptionsProperty()
    {
        $query = Emplacement::select(
            'idEmplacement',
            'Emplacement',
            'CodeEmplacement',
            'idLocalisation',
            'idAffectation'
        )->orderBy('Emplacement');
        
        // Filtrer par localisation si sélectionnée
        if (!empty($this->idLocalisation)) {
            $query->where('idLocalisation', $this->idLocalisation);
        }
        
        // Filtrer par affectation si sélectionnée
        if (!empty($this->idAffectation)) {
            $query->where('idAffectation', $this->idAffectation);
        }
        
        $emplacements = $query->get();
        
        // Charger les relations en une seule requête si nécessaire
        if ($emplacements->isNotEmpty()) {
            $localisationIds = $emplacements->pluck('idLocalisation')->unique()->filter();
            $affectationIds = $emplacements->pluck('idAffectation')->unique()->filter();
            
            $localisations = collect();
            $affectations = collect();
            
            if ($localisationIds->isNotEmpty()) {
                $localisations = LocalisationImmo::select('idLocalisation', 'Localisation', 'CodeLocalisation')
                    ->whereIn('idLocalisation', $localisationIds)
                    ->get()
                    ->keyBy('idLocalisation');
            }
            
            if ($affectationIds->isNotEmpty()) {
                $affectations = Affectation::select('idAffectation', 'Affectation', 'CodeAffectation')
                    ->whereIn('idAffectation', $affectationIds)
                    ->get()
                    ->keyBy('idAffectation');
            }
            
            // Ajouter les relations
            $emplacements->each(function ($emplacement) use ($localisations, $affectations) {
                $emplacement->localisation = $localisations->get($emplacement->idLocalisation);
                $emplacement->affectation = $affectations->get($emplacement->idAffectation);
            });
        }
        
        return $emplacements
            ->map(function ($emplacement) {
                return [
                    'value' => (string)$emplacement->idEmplacement,
                    'text' => ($emplacement->CodeEmplacement ? $emplacement->CodeEmplacement . ' - ' : '') . $emplacement->Emplacement,
                ];
            })
            ->toArray();
    }
    
    /**
     * Génère le nom d'affichage d'un emplacement avec ses relations
     */
    private function getEmplacementDisplayName($emplacement): string
    {
        $parts = [];
        
        // Localisation
        if ($emplacement->localisation) {
            $parts[] = $emplacement->localisation->Localisation ?? '';
            if ($emplacement->localisation->CodeLocalisation) {
                $parts[] = '(' . $emplacement->localisation->CodeLocalisation . ')';
            }
        }
        
        // Affectation
        if ($emplacement->affectation) {
            $parts[] = '- ' . ($emplacement->affectation->Affectation ?? '');
        }
        
        // Emplacement
        $parts[] = '- ' . ($emplacement->Emplacement ?? '');
        
        return implode(' ', array_filter($parts));
    }

    /**
     * Propriété calculée : Retourne toutes les natures juridiques
     */
    public function getNatureJuridiquesProperty()
    {
        return NatureJuridique::orderBy('NatJur')->get();
    }

    /**
     * Options pour SearchableSelect : Natures juridiques
     */
    public function getNatureJuridiqueOptionsProperty()
    {
        return NatureJuridique::actif()->orderBy('NatJur')
            ->get()
            ->map(function ($natJur) {
                return [
                    'value' => (string)$natJur->idNatJur,
                    'text' => $natJur->NatJur,
                ];
            })
            ->toArray();
    }

    /**
     * Propriété calculée : Retourne toutes les sources de financement
     */
    public function getSourceFinancementsProperty()
    {
        return SourceFinancement::orderBy('SourceFin')->get();
    }

    /**
     * Options pour SearchableSelect : Sources de financement
     */
    public function getSourceFinancementOptionsProperty()
    {
        return SourceFinancement::actif()->orderBy('SourceFin')
            ->get()
            ->map(function ($sourceFin) {
                return [
                    'value' => (string)$sourceFin->idSF,
                    'text' => $sourceFin->SourceFin,
                ];
            })
            ->toArray();
    }

    /**
     * Propriété calculée : Vérifie si on est en mode édition
     */
    public function getIsEditProperty(): bool
    {
        return $this->bien !== null;
    }

    /**
     * Règles de validation — dépend des paramètres activés
     */
    protected function rules(): array
    {
        $etatActif   = ParametreChamp::isActif('etat');
        $natJurActif = ParametreChamp::isActif('nature_juridique');
        $sfActif     = ParametreChamp::isActif('source_financement');

        $rules = [
            'idDesignation' => 'required|exists:designation,id',
            'idCategorie'   => 'required|exists:categorie,idCategorie',
            'idEtat'        => $etatActif   ? 'required|exists:etat,idEtat'                     : 'nullable|exists:etat,idEtat',
            'idEmplacement' => 'required|exists:emplacement,idEmplacement',
            'idNatJur'      => $natJurActif ? 'required|exists:naturejurdique,idNatJur'          : 'nullable|exists:naturejurdique,idNatJur',
            'idSF'          => $sfActif     ? 'required|exists:sourcefinancement,idSF'           : 'nullable|exists:sourcefinancement,idSF',
            'DateAcquisition'     => 'nullable|integer|min:1900|max:' . (now()->year + 1),
            'valeur_acquisition'  => 'nullable|numeric|min:0',
            'date_mise_en_service'=> 'nullable|date',
            'code_formate'        => 'nullable|string|max:100',
        ];

        if (!$this->isEdit) {
            $rules['quantite'] = 'required|integer|min:1|max:1000';
        }

        return $rules;
    }

    public function getEtatActifProperty(): bool
    {
        return ParametreChamp::isActif('etat');
    }

    public function getNatJurActifProperty(): bool
    {
        return ParametreChamp::isActif('nature_juridique');
    }

    public function getSfActifProperty(): bool
    {
        return ParametreChamp::isActif('source_financement');
    }

    /**
     * Messages de validation personnalisés
     */
    protected function messages(): array
    {
        return [
            'idDesignation.required' => 'La désignation est obligatoire.',
            'idDesignation.exists' => 'La désignation sélectionnée n\'existe pas.',
            'idCategorie.required' => 'La catégorie est obligatoire.',
            'idCategorie.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'idEtat.required' => 'L\'état est obligatoire.',
            'idEtat.exists' => 'L\'état sélectionné n\'existe pas.',
            'idEmplacement.required' => 'L\'emplacement est obligatoire.',
            'idEmplacement.exists' => 'L\'emplacement sélectionné n\'existe pas.',
            'idNatJur.required' => 'La nature juridique est obligatoire.',
            'idNatJur.exists' => 'La nature juridique sélectionnée n\'existe pas.',
            'idSF.required' => 'La source de financement est obligatoire.',
            'idSF.exists' => 'La source de financement sélectionnée n\'existe pas.',
            'DateAcquisition.integer' => 'L\'année d\'acquisition doit être un nombre.',
            'DateAcquisition.min' => 'L\'année d\'acquisition doit être supérieure ou égale à 1900.',
            'DateAcquisition.max' => 'L\'année d\'acquisition ne peut pas être dans le futur.',
            'valeur_acquisition.numeric' => 'La valeur d\'acquisition doit être un nombre.',
            'valeur_acquisition.min' => 'La valeur d\'acquisition ne peut pas être négative.',
            'date_mise_en_service.date' => 'La date de mise en service n\'est pas valide.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité doit être au moins de 1.',
            'quantite.max' => 'La quantité ne peut pas dépasser 1000.',
        ];
    }

    /**
     * Sauvegarde l'immobilisation (création ou édition)
     */
    public function save()
    {
        // Valider les données
        $validated = $this->validate();

        try {
            if ($this->isEdit) {
                // Mode édition : mettre à jour l'immobilisation existante
                $this->bien->update([
                    'idDesignation'       => $validated['idDesignation'],
                    'idCategorie'         => $validated['idCategorie'],
                    'idEtat'              => $validated['idEtat'],
                    'idEmplacement'       => $validated['idEmplacement'],
                    'idNatJur'            => $validated['idNatJur'],
                    'idSF'                => $validated['idSF'],
                    'DateAcquisition'     => !empty($validated['DateAcquisition']) ? (int)$validated['DateAcquisition'] : null,
                    'valeur_acquisition'  => !empty($validated['valeur_acquisition']) ? $validated['valeur_acquisition'] : null,
                    'date_mise_en_service'=> !empty($validated['date_mise_en_service']) ? $validated['date_mise_en_service'] : null,
                    'code_formate'        => $validated['code_formate'] ?? null,
                ]);

                $bien = $this->bien->fresh();
                $message = 'Immobilisation modifiée avec succès';
            } else {
                // Mode création : créer une ou plusieurs immobilisations selon la quantité
                $quantite = (int)($validated['quantite'] ?? 1);
                $biensCrees = [];
                
                // Données communes pour toutes les immobilisations
                $donneesCommunes = [
                    'idDesignation'       => $validated['idDesignation'],
                    'idCategorie'         => $validated['idCategorie'],
                    'idEtat'              => $validated['idEtat'],
                    'idEmplacement'       => $validated['idEmplacement'],
                    'idNatJur'            => $validated['idNatJur'],
                    'idSF'                => $validated['idSF'],
                    'DateAcquisition'     => !empty($validated['DateAcquisition']) ? (int)$validated['DateAcquisition'] : null,
                    'valeur_acquisition'  => !empty($validated['valeur_acquisition']) ? $validated['valeur_acquisition'] : null,
                    'date_mise_en_service'=> !empty($validated['date_mise_en_service']) ? $validated['date_mise_en_service'] : null,
                    'code_formate'        => $validated['code_formate'] ?? null,
                ];
                
                // Créer les immobilisations
                for ($i = 0; $i < $quantite; $i++) {
                    // Pour plusieurs biens, on ne duplique pas le code saisi — laisser vide sauf le premier
                    if ($i > 0) {
                        $donneesCommunes['code_formate'] = null;
                    }
                    $bien = Gesimmo::create($donneesCommunes);
                    
                    // Charger les relations nécessaires pour le code formaté et l'affichage
                    $bien->load([
                        'designation',
                        'categorie',
                        'natureJuridique',
                        'sourceFinancement',
                        'emplacement.localisation',
                        'emplacement.affectation'
                    ]);
                    
                    $biensCrees[] = $bien;
                }
                
                // Utiliser le dernier bien créé pour la redirection
                $bien = end($biensCrees);
                
                if ($quantite > 1) {
                    $message = $quantite . ' immobilisations créées avec succès';
                } else {
                    $message = 'Immobilisation créée avec succès';
                }
            }

            session()->flash('success', $message);

            // Rediriger vers la page de détail
            return redirect()->route('biens.show', $bien);
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    /**
     * Annule et redirige vers la liste
     */
    public function cancel()
    {
        return redirect()->route('biens.index');
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.biens.form-bien');
    }
}

