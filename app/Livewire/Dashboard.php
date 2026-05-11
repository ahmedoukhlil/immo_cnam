<?php

namespace App\Livewire;

use App\Models\Gesimmo;
use App\Models\Inventaire;
use App\Models\InventaireLocalisation;
use App\Models\InventaireScan;
use App\Models\Localisation;
use App\Models\LocalisationImmo;
use App\Models\Emplacement;
use App\Models\Ticket;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    public $totalBiens = 0;
    public $totalLocalisations = 0;
    public $inventaireEnCours = null;
    public $valeurTotale = 0;
    public $dernieresActions = [];
    public $statistiquesInventaire = [];
    public $localisationsInventaire = [];
    public $repartitionStatuts = [];
    public $progressionParService = [];
    public $biensCetteAnnee = 0;
    public $nombreBatiments = 0;
    public $emplacementsInventories = [];
    public $statsTickets = [];

    public function mount()
    {
        $user = auth()->user();
        if ($user->isOccupant() || $user->isTechnicien()) {
            return; // Pas de stats pour ces rôles, la vue affichera un dashboard simplifié
        }
        $this->loadStatistics();
    }

    public function refresh()
    {
        $user = auth()->user();
        if ($user->isOccupant() || $user->isTechnicien()) return;
        $this->loadStatistics();
    }

    private function loadStatistics()
    {
        try {
            // Total immobilisations (gesimmo) - charger en premier pour éviter les erreurs
            $this->totalBiens = Gesimmo::count();
            
            // Biens créés cette année (DateAcquisition est un entier représentant l'année)
            // Inclure aussi les années récentes (2 dernières années) pour avoir une vue plus large
            $currentYear = now()->year;
            $this->biensCetteAnnee = Gesimmo::where('DateAcquisition', '>=', $currentYear - 1)
                ->where('DateAcquisition', '<=', $currentYear)
                ->count();
            
            // Total localisations - utiliser la table qui contient réellement les données
            // D'abord essayer LocalisationImmo (table principale des localisations)
            $this->totalLocalisations = LocalisationImmo::count();
            
            // Si la table localisations existe et a des données, l'utiliser aussi
            try {
                $localisationsCount = Localisation::where('actif', true)->count();
                if ($localisationsCount > 0) {
                    $this->totalLocalisations += $localisationsCount;
                }
            } catch (\Exception $e) {
                // La table localisations n'existe peut-être pas, on continue avec LocalisationImmo
            }
            
            // Nombre de bâtiments uniques - utiliser CodeLocalisation de LocalisationImmo
            $this->nombreBatiments = LocalisationImmo::whereNotNull('CodeLocalisation')
                ->where('CodeLocalisation', '!=', '')
                ->distinct('CodeLocalisation')
                ->count('CodeLocalisation');
            
            // Si la table localisations existe avec batiment, l'ajouter
            try {
                $batimentsCount = Localisation::whereNotNull('batiment')
                    ->where('batiment', '!=', '')
                    ->distinct('batiment')
                    ->count('batiment');
                if ($batimentsCount > 0) {
                    // Prendre le maximum entre les deux
                    $this->nombreBatiments = max($this->nombreBatiments, $batimentsCount);
                }
            } catch (\Exception $e) {
                // Ignorer si la colonne n'existe pas
            }
            
            // Valeur totale du parc (on ne dispose pas de cette info dans gesimmo, donc on laisse à 0)
            $this->valeurTotale = 0;
            
            // Dernier inventaire (peu importe le statut pour voir l'avancement)
            try {
                $this->inventaireEnCours = Inventaire::orderBy('annee', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->first();
            } catch (\Exception $e) {
                $this->inventaireEnCours = null;
            }
            
            // Charger les statistiques de l'inventaire en cours
            if ($this->inventaireEnCours) {
                try {
                    $this->loadInventaireStats();
                } catch (\Exception $e) {
                    \Log::warning('Erreur lors du chargement des stats d\'inventaire: ' . $e->getMessage());
                }
            }
            
            // Stats tickets
            try {
                $this->loadTicketStats();
            } catch (\Exception $e) {
                \Log::warning('Erreur lors du chargement des stats tickets: ' . $e->getMessage());
            }

            // Dernières actions
            try {
                $this->loadRecentActions();
            } catch (\Exception $e) {
                \Log::warning('Erreur lors du chargement des actions récentes: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            // En cas d'erreur critique, logger et initialiser avec des valeurs par défaut
            \Log::error('Erreur critique lors du chargement des statistiques du dashboard', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Essayer au moins de charger le total des biens
            try {
                $this->totalBiens = Gesimmo::count();
            } catch (\Exception $e2) {
                \Log::error('Impossible de charger le total des biens: ' . $e2->getMessage());
                $this->totalBiens = 0;
            }
            
            $this->totalLocalisations = 0;
            $this->valeurTotale = 0;
            $this->biensCetteAnnee = 0;
            $this->nombreBatiments = 0;
        }
    }

    private function loadInventaireStats()
    {
        if (!$this->inventaireEnCours) {
            return;
        }

        // Localisations de l'inventaire
        $this->localisationsInventaire = InventaireLocalisation::where('inventaire_id', $this->inventaireEnCours->id)
            ->with(['localisation', 'agent'])
            ->orderBy('statut', 'asc')  // en_cours d'abord, puis en_attente, puis termine
            ->orderBy('nombre_biens_attendus', 'desc')  // Puis par nombre de biens (plus important d'abord)
            ->get()
            ->map(function ($item) {
                // Gérer les différents formats de localisation
                $localisationName = 'N/A';
                if ($item->localisation) {
                    if (method_exists($item->localisation, 'getFullNameAttribute')) {
                        $localisationName = $item->localisation->full_name;
                    } elseif (isset($item->localisation->Localisation)) {
                        $codeLocalisation = $item->localisation->CodeLocalisation ?? '';
                        $localisationName = ($codeLocalisation ? $codeLocalisation . ' - ' : '') . $item->localisation->Localisation;
                    } elseif (isset($item->localisation->designation)) {
                        $localisationName = $item->localisation->designation;
                    }
                }
                
                // Gérer le nom de l'agent
                $agentName = 'Non assigné';
                if ($item->agent) {
                    if (isset($item->agent->name)) {
                        $agentName = $item->agent->name;
                    } elseif (isset($item->agent->users)) {
                        $agentName = $item->agent->users;
                    } elseif (isset($item->agent->email)) {
                        $agentName = $item->agent->email;
                    }
                }
                
                return [
                    'id' => $item->id,
                    'localisation' => $localisationName,
                    'biens_attendus' => $item->nombre_biens_attendus ?? 0,
                    'biens_scannes' => $item->nombre_biens_scannes ?? 0,
                    'progression' => $item->progression ?? 0,
                    'statut' => $item->statut ?? 'en_attente',
                    'agent' => $agentName,
                ];
            })
            ->toArray();

        // Répartition des statuts de scans
        $scans = InventaireScan::where('inventaire_id', $this->inventaireEnCours->id)->get();
        $this->repartitionStatuts = [
            'present' => $scans->where('statut_scan', 'present')->count(),
            'deplace' => $scans->where('statut_scan', 'deplace')->count(),
            'absent' => $scans->where('statut_scan', 'absent')->count(),
            'deteriore' => $scans->where('statut_scan', 'deteriore')->count(),
        ];

        // Progression par service
        $localisations = InventaireLocalisation::where('inventaire_id', $this->inventaireEnCours->id)
            ->with('localisation')
            ->get();
        
        $services = [];
        foreach ($localisations as $invLoc) {
            $service = $invLoc->localisation->service_rattache ?? 'Autre';
            if (!isset($services[$service])) {
                $services[$service] = ['total' => 0, 'termine' => 0];
            }
            $services[$service]['total']++;
            if ($invLoc->statut === 'termine') {
                $services[$service]['termine']++;
            }
        }

        $this->progressionParService = collect($services)->map(function ($data, $service) {
            return [
                'service'     => $service,
                'total'       => $data['total'],
                'termine'     => $data['termine'],
                'progression' => $data['total'] > 0 ? round(($data['termine'] / $data['total']) * 100, 1) : 0,
            ];
        })->sortByDesc('progression')->values()->toArray();

        // Statistiques globales
        try {
        $this->statistiquesInventaire = $this->inventaireEnCours->getStatistiques();
        } catch (\Exception $e) {
            $this->statistiquesInventaire = [
                'progression' => 0,
                'taux_conformite' => 0,
                'duree' => 0,
                'total_localisations' => 0,
                'localisations_terminees' => 0,
                'total_scans' => 0,
                'scans_presents' => 0,
                'scans_deplaces' => 0,
                'scans_absents' => 0,
                'scans_deteriores' => 0,
            ];
        }

        // Charger les emplacements inventoriés
        $this->loadEmplacementsInventories();
    }

    private function loadEmplacementsInventories()
    {
        if (!$this->inventaireEnCours) {
            $this->emplacementsInventories = [];
            return;
        }

        try {
            // Récupérer tous les scans de l'inventaire en cours avec leurs relations
            $scans = InventaireScan::where('inventaire_id', $this->inventaireEnCours->id)
                ->with(['gesimmo.emplacement.localisation', 'gesimmo.emplacement.affectation'])
                ->get();

            // Grouper les scans par emplacement
            $emplacementsData = [];
            
            foreach ($scans as $scan) {
                // Utiliser la relation gesimmo() qui fait le lien avec NumOrdre
                if ($scan->gesimmo && $scan->gesimmo->emplacement) {
                    $emplacement = $scan->gesimmo->emplacement;
                    $emplacementId = $emplacement->idEmplacement;
                    
                    // Initialiser l'emplacement s'il n'existe pas encore
                    if (!isset($emplacementsData[$emplacementId])) {
                        // Nom de la localisation
                        $localisationNom = 'N/A';
                        if ($emplacement->localisation) {
                            $code = $emplacement->localisation->CodeLocalisation ?? '';
                            $localisationNom = ($code ? $code . ' - ' : '') . $emplacement->localisation->Localisation;
                        }

                        // Nom de l'affectation
                        $affectationNom = $emplacement->affectation ? $emplacement->affectation->Affectation : 'N/A';

                        // Nombre total de biens dans cet emplacement
                        $totalBiens = $emplacement->immobilisations()->count();

                        $emplacementsData[$emplacementId] = [
                            'id' => $emplacement->idEmplacement,
                            'nom' => $emplacement->Emplacement,
                            'code' => $emplacement->CodeEmplacement ?? '',
                            'localisation' => $localisationNom,
                            'affectation' => $affectationNom,
                            'biens_scannes' => 0,
                            'total_biens' => $totalBiens,
                        ];
                    }
                    
                    // Incrémenter le compteur de biens scannés
                    $emplacementsData[$emplacementId]['biens_scannes']++;
                }
            }

            // Calculer la progression et formater les données
            $this->emplacementsInventories = collect($emplacementsData)
                ->map(function ($data) {
                    $data['progression'] = $data['total_biens'] > 0 
                        ? round(($data['biens_scannes'] / $data['total_biens']) * 100, 1) 
                        : 0;
                    return $data;
                })
                ->sortByDesc('biens_scannes')
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement des emplacements inventoriés: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->emplacementsInventories = [];
        }
    }

    private function loadTicketStats()
    {
        $total   = Ticket::count();
        $ouverts = Ticket::where('statut', 'ouvert')->count();
        $enCours = Ticket::whereIn('statut', ['assigne', 'en_cours'])->count();
        $resolus = Ticket::whereIn('statut', ['resolu', 'ferme'])->count();

        // Délai moyen de résolution (en heures) sur les tickets fermés ce mois
        $delaiMoyen = Ticket::where('statut', 'ferme')
            ->whereNotNull('resolved_at')
            ->whereMonth('resolved_at', now()->month)
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        // Tickets par priorité
        $parPriorite = Ticket::selectRaw('priorite, COUNT(*) as nb')
            ->whereIn('statut', ['ouvert', 'assigne', 'en_cours'])
            ->groupBy('priorite')
            ->pluck('nb', 'priorite')
            ->toArray();

        // Tickets ouverts ce mois
        $cesMois = Ticket::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Tickets urgents non traités
        $urgentsEnAttente = Ticket::where('priorite', 'urgente')
            ->whereIn('statut', ['ouvert', 'assigne', 'en_cours'])
            ->count();

        $this->statsTickets = [
            'total'            => $total,
            'ouverts'          => $ouverts,
            'en_cours'         => $enCours,
            'resolus'          => $resolus,
            'ces_mois'         => $cesMois,
            'urgents_en_attente' => $urgentsEnAttente,
            'delai_moyen_h'    => $delaiMoyen ? round($delaiMoyen, 1) : null,
            'par_priorite'     => [
                'basse'   => $parPriorite['basse']   ?? 0,
                'normale' => $parPriorite['normale']  ?? 0,
                'haute'   => $parPriorite['haute']    ?? 0,
                'urgente' => $parPriorite['urgente']  ?? 0,
            ],
        ];
    }

    private function loadRecentActions()
    {
        $actions = collect();

        try {
            // Scans récents (7 derniers jours)
            $scansRecents = InventaireScan::with(['bien', 'localisationReelle', 'agent'])
                ->where('date_scan', '>=', now()->subDays(7))
                ->orderBy('date_scan', 'desc')
                ->limit(5)
                ->get();

            foreach ($scansRecents as $scan) {
                if ($scan->bien) {
                    $agentName = $scan->agent ? ($scan->agent->users ?? 'Utilisateur') : 'Système';
                    $bienDesignation = $scan->bien->designation->designation ?? 'Immobilisation';
                    $localisationNom = $scan->localisationReelle ? ($scan->localisationReelle->Localisation ?? 'N/A') : 'N/A';
                    
                    $actions->push([
                        'type' => 'scan',
                        'icon' => 'scan',
                        'message' => "{$agentName} a scanné {$bienDesignation} dans {$localisationNom}",
                        'time' => $scan->date_scan,
                    ]);
                }
            }

            // Inventaires démarrés/clôturés récemment
            $inventairesRecents = Inventaire::with('creator')
                ->where(function ($query) {
                    $query->where('date_debut', '>=', now()->subDays(7))
                          ->orWhere('date_fin', '>=', now()->subDays(7));
                })
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get();

            foreach ($inventairesRecents as $inventaire) {
                $creatorName = $inventaire->creator ? ($inventaire->creator->users ?? 'Utilisateur') : 'Système';
                
                if ($inventaire->statut === 'en_cours' && $inventaire->date_debut) {
                    $actions->push([
                        'type' => 'inventaire_started',
                        'icon' => 'inventaire_started',
                        'message' => "{$creatorName} a démarré l'inventaire {$inventaire->annee}",
                        'time' => $inventaire->date_debut,
                    ]);
                } elseif ($inventaire->statut === 'cloture' && $inventaire->date_fin) {
                    $actions->push([
                        'type' => 'inventaire_closed',
                        'icon' => 'inventaire_closed',
                        'message' => "L'inventaire {$inventaire->annee} a été clôturé",
                        'time' => $inventaire->date_fin,
                    ]);
                }
            }
            
            // Ajouter les localisations créées récemment (si la table a des timestamps)
            try {
                $localisationsRecentes = \App\Models\Localisation::where('created_at', '>=', now()->subDays(7))
                    ->orderBy('created_at', 'desc')
                    ->limit(3)
                    ->get();
                
                foreach ($localisationsRecentes as $localisation) {
                    $actions->push([
                        'type' => 'localisation_created',
                        'icon' => 'localisation',
                        'message' => "Nouvelle localisation créée: {$localisation->code} - {$localisation->designation}",
                        'time' => $localisation->created_at,
                    ]);
                }
            } catch (\Exception $e) {
                // La table localisations n'a peut-être pas de timestamps, on ignore
            }
        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement des actions récentes: ' . $e->getMessage());
        }

        // Trier par date et prendre les 10 plus récents
        $this->dernieresActions = $actions->sortByDesc('time')
            ->take(10)
            ->values()
            ->map(function ($action) {
                $action['time_ago'] = Carbon::parse($action['time'])->diffForHumans();
                return $action;
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
