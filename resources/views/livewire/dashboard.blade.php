<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- En-tête --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
        <p class="text-sm text-gray-500 mt-1">
            @if(auth()->user()->isOccupant())
                Bienvenue, <span class="font-medium text-gray-700">{{ auth()->user()->nom ?: auth()->user()->users }}</span>. Gérez vos tickets de maintenance.
            @elseif(auth()->user()->isTechnicien())
                Bienvenue, <span class="font-medium text-gray-700">{{ auth()->user()->nom ?: auth()->user()->users }}</span>. Consultez vos tickets assignés.
            @else
                Vue d'ensemble de votre gestion d'inventaire
            @endif
        </p>
    </div>

    {{-- ============================================================ --}}
    {{-- DASHBOARD OCCUPANT / TECHNICIEN                              --}}
    {{-- ============================================================ --}}
    @if(auth()->user()->isOccupant() || auth()->user()->isTechnicien())
        @php
            $mesTickets = auth()->user()->isOccupant()
                ? \App\Models\Ticket::where('created_by', auth()->id())->latest()->take(5)->get()
                : \App\Models\Ticket::where('assigned_to', auth()->id())->whereIn('statut', ['assigne','en_cours'])->latest()->take(5)->get();
            $nbOuverts = auth()->user()->isOccupant()
                ? \App\Models\Ticket::where('created_by', auth()->id())->whereIn('statut', ['ouvert','assigne','en_cours'])->count()
                : \App\Models\Ticket::where('assigned_to', auth()->id())->whereIn('statut', ['assigne','en_cours'])->count();
            $nbFermes = auth()->user()->isOccupant()
                ? \App\Models\Ticket::where('created_by', auth()->id())->where('statut', 'ferme')->count()
                : \App\Models\Ticket::where('assigned_to', auth()->id())->where('statut', 'ferme')->count();
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
            {{-- KPI en cours --}}
            <div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-orange-50 rounded-bl-full"></div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tickets en cours</p>
                <p class="text-4xl font-extrabold text-orange-500">{{ $nbOuverts }}</p>
                <div class="mt-3 flex items-center text-xs text-orange-500 font-medium">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path stroke-linecap="round" stroke-width="2" d="M12 8v4l2 2"/></svg>
                    Actifs
                </div>
            </div>

            {{-- KPI résolus --}}
            <div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-green-50 rounded-bl-full"></div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Tickets résolus</p>
                <p class="text-4xl font-extrabold text-green-500">{{ $nbFermes }}</p>
                <div class="mt-3 flex items-center text-xs text-green-500 font-medium">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Fermés
                </div>
            </div>

            {{-- Action --}}
            <div class="flex items-center justify-center bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl shadow-sm p-6">
                @if(auth()->user()->isOccupant())
                    <a href="{{ route('tickets.create') }}" wire:navigate
                       class="flex items-center gap-2 text-white font-semibold text-sm hover:opacity-90 transition">
                        <span class="flex items-center justify-center w-8 h-8 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </span>
                        Créer un ticket
                    </a>
                @else
                    <a href="{{ route('tickets.index') }}" wire:navigate
                       class="flex items-center gap-2 text-white font-semibold text-sm hover:opacity-90 transition">
                        Voir mes tickets
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                @endif
            </div>
        </div>

        {{-- Tickets récents --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-800">Tickets récents</h2>
            </div>
            @if($mesTickets->isNotEmpty())
                <div class="divide-y divide-gray-50">
                    @foreach($mesTickets as $ticket)
                        @php
                            $sc = match($ticket->statut) {
                                'ouvert'   => 'bg-blue-100 text-blue-700',
                                'assigne'  => 'bg-yellow-100 text-yellow-700',
                                'en_cours' => 'bg-orange-100 text-orange-700',
                                'ferme'    => 'bg-green-100 text-green-700',
                                default    => 'bg-gray-100 text-gray-600'
                            };
                            $sl = match($ticket->statut) {
                                'ouvert'   => 'Ouvert',
                                'assigne'  => 'Assigné',
                                'en_cours' => 'En cours',
                                'ferme'    => 'Fermé',
                                default    => $ticket->statut
                            };
                        @endphp
                        <a href="{{ route('tickets.show', $ticket) }}" wire:navigate
                           class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50 transition-colors">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $ticket->titre }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $ticket->reference }} · {{ $ticket->created_at?->diffForHumans() }}</p>
                            </div>
                            <span class="ml-4 shrink-0 text-xs font-medium px-2.5 py-1 rounded-full {{ $sc }}">{{ $sl }}</span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="py-14 text-center">
                    <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    <p class="text-sm text-gray-400">Aucun ticket pour le moment.</p>
                </div>
            @endif
        </div>

    @else
    {{-- ============================================================ --}}
    {{-- DASHBOARD ADMIN / AGENT                                      --}}
    {{-- ============================================================ --}}

    {{-- Bandeau nouvelle installation --}}
    @if($totalBiens === 0 && $totalLocalisations === 0)
        <div class="flex items-start gap-4 bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-8">
            <div class="shrink-0 w-9 h-9 flex items-center justify-center bg-blue-100 rounded-xl">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-blue-800 mb-1">Bienvenue dans votre système de gestion d'inventaire !</h3>
                <ul class="text-sm text-blue-700 list-disc list-inside space-y-0.5">
                    <li>Créer des localisations (bureaux, ateliers…)</li>
                    <li>Ajouter des immobilisations à inventorier</li>
                    <li>Démarrer votre premier inventaire</li>
                </ul>
            </div>
        </div>
    @endif

    {{-- ---- KPI IMMOBILISATIONS / INVENTAIRE ---- --}}
    @if(auth()->user()->hasPermission('dashboard.stats_immobilisations') || auth()->user()->hasPermission('dashboard.stats_inventaire'))
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        @if(auth()->user()->hasPermission('dashboard.stats_immobilisations'))
        {{-- Total Immobilisations --}}
        <div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute inset-y-0 left-0 w-1 bg-blue-500 rounded-l-2xl"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Immobilisations</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalBiens, 0, ',', ' ') }}</p>
                    <p class="text-xs text-green-600 font-medium mt-2 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        +{{ $biensCetteAnnee }} cette année
                    </p>
                </div>
                <div class="w-10 h-10 flex items-center justify-center bg-blue-50 rounded-xl">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                </div>
            </div>
            <a href="{{ route('biens.index') }}" wire:navigate class="mt-4 inline-flex items-center text-xs text-blue-600 font-medium hover:text-blue-800 gap-1">
                Voir toutes <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Localisations --}}
        <div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute inset-y-0 left-0 w-1 bg-emerald-500 rounded-l-2xl"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Localisations</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalLocalisations, 0, ',', ' ') }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $nombreBatiments }} bâtiment(s)</p>
                </div>
                <div class="w-10 h-10 flex items-center justify-center bg-emerald-50 rounded-xl">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <a href="{{ route('localisations.index') }}" wire:navigate class="mt-4 inline-flex items-center text-xs text-emerald-600 font-medium hover:text-emerald-800 gap-1">
                Gérer <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        @endif

        @if(auth()->user()->hasPermission('dashboard.stats_inventaire'))
        {{-- Dernier inventaire --}}
        <div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute inset-y-0 left-0 w-1 bg-violet-500 rounded-l-2xl"></div>
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Inventaire</p>
                    @if($inventaireEnCours)
                        <p class="text-lg font-bold text-gray-900 mt-2">{{ $inventaireEnCours->annee }}</p>
                        <div class="mt-1">
                            @php
                                $badgeInv = match($inventaireEnCours->statut) {
                                    'en_preparation' => ['bg-gray-100 text-gray-600',    'En préparation'],
                                    'en_cours'       => ['bg-blue-100 text-blue-700',    'En cours'],
                                    'termine'        => ['bg-orange-100 text-orange-700','Terminé'],
                                    'cloture'        => ['bg-green-100 text-green-700',  'Clôturé'],
                                    default          => ['bg-gray-100 text-gray-600',    $inventaireEnCours->statut],
                                };
                            @endphp
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $badgeInv[0] }}">{{ $badgeInv[1] }}</span>
                        </div>
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progression</span>
                                <span class="font-semibold">{{ round($statistiquesInventaire['progression'] ?? 0, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full transition-all duration-500
                                    {{ ($statistiquesInventaire['progression'] ?? 0) >= 100 ? 'bg-green-500' :
                                       (($statistiquesInventaire['progression'] ?? 0) >= 50  ? 'bg-blue-500' :
                                       (($statistiquesInventaire['progression'] ?? 0) > 0    ? 'bg-yellow-400' : 'bg-gray-300')) }}"
                                     style="width: {{ $statistiquesInventaire['progression'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 mt-2 italic">Aucun inventaire</p>
                    @endif
                </div>
                <div class="w-10 h-10 flex items-center justify-center bg-violet-50 rounded-xl ml-3">
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            @if($inventaireEnCours)
                <a href="{{ route('inventaires.show', $inventaireEnCours->id) }}" wire:navigate class="mt-4 inline-flex items-center text-xs text-violet-600 font-medium hover:text-violet-800 gap-1">
                    Détails <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @endif
        </div>
        @endif

        @if(auth()->user()->hasPermission('dashboard.stats_immobilisations'))
        {{-- Valeur totale --}}
        <div class="relative bg-white rounded-2xl shadow-sm border border-gray-100 p-6 overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute inset-y-0 left-0 w-1 bg-amber-400 rounded-l-2xl"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Valeur totale</p>
                    <p class="text-2xl font-extrabold text-gray-900 mt-2 leading-tight">
                        {{ number_format($valeurTotale, 0, ',', ' ') }}<span class="text-base font-semibold text-gray-500 ml-1">MRU</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-2">Valeur déclarée</p>
                </div>
                <div class="w-10 h-10 flex items-center justify-center bg-amber-50 rounded-xl">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- ---- TICKETS DE MAINTENANCE ---- --}}
    @if(auth()->user()->hasPermission('dashboard.stats_tickets') && !empty($statsTickets))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 flex items-center justify-center bg-indigo-50 rounded-lg">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </span>
                    Tickets de maintenance
                </h3>
                <p class="text-xs text-gray-400 mt-1 ml-9">Vue d'ensemble des demandes d'intervention</p>
            </div>
            <a href="{{ route('tickets.index') }}" wire:navigate class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                Tous les tickets <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- KPI 4 blocs --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            @php
                $kpis = [
                    ['label' => 'Ouverts',   'value' => $statsTickets['ouverts'],   'bg' => 'bg-yellow-50',  'border' => 'border-yellow-200',  'text' => 'text-yellow-700',  'sub' => 'text-yellow-500'],
                    ['label' => 'En cours',  'value' => $statsTickets['en_cours'],  'bg' => 'bg-indigo-50',  'border' => 'border-indigo-200',  'text' => 'text-indigo-700',  'sub' => 'text-indigo-400'],
                    ['label' => 'Résolus',   'value' => $statsTickets['resolus'],   'bg' => 'bg-green-50',   'border' => 'border-green-200',   'text' => 'text-green-700',   'sub' => 'text-green-500'],
                    ['label' => 'Total',     'value' => $statsTickets['total'],     'bg' => 'bg-gray-50',    'border' => 'border-gray-200',    'text' => 'text-gray-700',    'sub' => 'text-gray-400'],
                ];
            @endphp
            @foreach($kpis as $kpi)
            <div class="rounded-xl border {{ $kpi['bg'] }} {{ $kpi['border'] }} p-4 text-center">
                <p class="text-2xl font-extrabold {{ $kpi['text'] }}">{{ $kpi['value'] }}</p>
                <p class="text-xs font-medium {{ $kpi['sub'] }} mt-1">{{ $kpi['label'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Priorités --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Actifs par priorité</h4>
                <div class="space-y-3">
                    @php
                        $priorites = [
                            'urgente' => ['label' => 'Urgente', 'bar' => 'bg-red-500',    'text' => 'text-red-600'],
                            'haute'   => ['label' => 'Haute',   'bar' => 'bg-orange-500', 'text' => 'text-orange-600'],
                            'normale' => ['label' => 'Normale', 'bar' => 'bg-blue-500',   'text' => 'text-blue-600'],
                            'basse'   => ['label' => 'Basse',   'bar' => 'bg-green-500',  'text' => 'text-green-600'],
                        ];
                        $totalActifs = max(1, $statsTickets['ouverts'] + $statsTickets['en_cours']);
                    @endphp
                    @foreach($priorites as $key => $cfg)
                        @php $nb = $statsTickets['par_priorite'][$key] ?? 0; $pct = round($nb / $totalActifs * 100); @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-14 text-xs font-semibold {{ $cfg['text'] }}">{{ $cfg['label'] }}</span>
                            <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="{{ $cfg['bar'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="w-5 text-xs font-bold text-gray-600 text-right">{{ $nb }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Indicateurs --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-1">Ouverts ce mois</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $statsTickets['ces_mois'] }}</p>
                </div>
                <div class="rounded-xl p-4 border {{ $statsTickets['urgents_en_attente'] > 0 ? 'bg-red-50 border-red-100' : 'bg-gray-50 border-gray-100' }}">
                    <p class="text-xs {{ $statsTickets['urgents_en_attente'] > 0 ? 'text-red-400' : 'text-gray-400' }} mb-1">Urgents non traités</p>
                    <p class="text-2xl font-extrabold {{ $statsTickets['urgents_en_attente'] > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $statsTickets['urgents_en_attente'] }}</p>
                </div>
                <div class="col-span-2 bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <p class="text-xs text-gray-400 mb-1">Délai moyen de résolution (ce mois)</p>
                    @if($statsTickets['delai_moyen_h'] !== null)
                        @php
                            $h = $statsTickets['delai_moyen_h'];
                            $delaiLabel = $h < 24 ? round($h, 1).'h' : round($h / 24, 1).' jours';
                        @endphp
                        <p class="text-2xl font-extrabold text-gray-800">{{ $delaiLabel }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">Aucune donnée ce mois</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ---- INVENTAIRE EN COURS ---- --}}
    @if($inventaireEnCours && auth()->user()->hasPermission('dashboard.stats_inventaire'))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        {{-- Header inventaire --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 flex items-center justify-center bg-violet-50 rounded-lg">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </span>
                    Inventaire {{ $inventaireEnCours->annee }}
                    @php
                        $badgeInv2 = match($inventaireEnCours->statut) {
                            'en_preparation' => 'bg-gray-100 text-gray-600',
                            'en_cours'       => 'bg-blue-100 text-blue-700',
                            'termine'        => 'bg-orange-100 text-orange-700',
                            'cloture'        => 'bg-green-100 text-green-700',
                            default          => 'bg-gray-100 text-gray-600',
                        };
                        $labelInv2 = match($inventaireEnCours->statut) {
                            'en_preparation' => 'En préparation',
                            'en_cours'       => 'En cours',
                            'termine'        => 'Terminé',
                            'cloture'        => 'Clôturé',
                            default          => $inventaireEnCours->statut,
                        };
                    @endphp
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $badgeInv2 }}">{{ $labelInv2 }}</span>
                </h3>
                <div class="text-xs text-gray-400 mt-1 ml-9 space-y-0.5">
                    @if($inventaireEnCours->date_debut)
                        <p>Démarré le {{ \Carbon\Carbon::parse($inventaireEnCours->date_debut)->format('d/m/Y') }}</p>
                    @endif
                    @if($inventaireEnCours->date_fin)
                        <p>Terminé le {{ \Carbon\Carbon::parse($inventaireEnCours->date_fin)->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('inventaires.show', $inventaireEnCours->id) }}" wire:navigate
               class="text-xs font-medium text-violet-600 hover:text-violet-800 flex items-center gap-1 shrink-0">
                Détails complets <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Stats inventaire --}}
        @if(!empty($statistiquesInventaire))
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 pb-6 border-b border-gray-100">
            @php
                $statsInv = [
                    ['label' => 'Localisations', 'value' => ($statistiquesInventaire['localisations_terminees'] ?? 0).' / '.($statistiquesInventaire['total_localisations'] ?? 0), 'bg' => 'bg-blue-50',   'icon_color' => 'text-blue-500',   'text' => 'text-blue-800',   'svg' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['label' => 'Total scans',   'value' => number_format($statistiquesInventaire['total_scans'] ?? 0, 0, ',', ' '),             'bg' => 'bg-green-50',  'icon_color' => 'text-green-500',  'text' => 'text-green-800',  'svg' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Progression',   'value' => round($statistiquesInventaire['progression'] ?? 0, 1).'%',                           'bg' => 'bg-violet-50', 'icon_color' => 'text-violet-500', 'text' => 'text-violet-800', 'svg' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ['label' => 'Conformité',    'value' => round($statistiquesInventaire['taux_conformite'] ?? 0, 1).'%',                       'bg' => 'bg-indigo-50', 'icon_color' => 'text-indigo-500', 'text' => 'text-indigo-800', 'svg' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                ];
            @endphp
            @foreach($statsInv as $si)
            <div class="rounded-xl {{ $si['bg'] }} p-4 flex items-center gap-3">
                <div class="w-9 h-9 flex items-center justify-center bg-white/60 rounded-lg shrink-0">
                    <svg class="w-4.5 h-4.5 {{ $si['icon_color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.1rem;height:1.1rem">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $si['svg'] }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">{{ $si['label'] }}</p>
                    <p class="text-xl font-extrabold {{ $si['text'] }} leading-tight">{{ $si['value'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Tableau localisations --}}
        <div class="overflow-x-auto mb-8 rounded-xl border border-gray-100">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left">Localisation</th>
                        <th class="px-5 py-3 text-center">Attendus</th>
                        <th class="px-5 py-3 text-center">Scannés</th>
                        <th class="px-5 py-3 text-left">Progression</th>
                        <th class="px-5 py-3 text-center">Statut</th>
                        <th class="px-5 py-3 text-left">Agent assigné</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($localisationsInventaire as $loc)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-3.5 font-medium text-gray-800">{{ $loc['localisation'] }}</td>
                            <td class="px-5 py-3.5 text-center font-semibold text-gray-700">{{ number_format($loc['biens_attendus'], 0, ',', ' ') }}</td>
                            <td class="px-5 py-3.5 text-center font-semibold {{ $loc['biens_scannes'] > 0 ? 'text-blue-600' : 'text-gray-300' }}">
                                {{ number_format($loc['biens_scannes'], 0, ',', ' ') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-20 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full transition-all duration-300
                                            {{ $loc['progression'] >= 100 ? 'bg-green-500' : ($loc['progression'] >= 50 ? 'bg-blue-500' : ($loc['progression'] > 0 ? 'bg-yellow-400' : 'bg-gray-200')) }}"
                                            style="width: {{ min($loc['progression'], 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold {{ $loc['progression'] >= 100 ? 'text-green-600' : ($loc['progression'] > 0 ? 'text-gray-600' : 'text-gray-300') }}">
                                        {{ round($loc['progression'], 1) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full
                                    {{ $loc['statut'] === 'termine'   ? 'bg-green-100 text-green-700' :
                                       ($loc['statut'] === 'en_cours' ? 'bg-blue-100 text-blue-700' :
                                       ($loc['statut'] === 'en_attente' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600')) }}">
                                    @if($loc['statut'] === 'en_attente') En attente
                                    @elseif($loc['statut'] === 'en_cours') En cours
                                    @elseif($loc['statut'] === 'termine') Terminé
                                    @else {{ ucfirst(str_replace('_', ' ', $loc['statut'])) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">
                                @if($loc['agent'] === 'Non assigné')
                                    <span class="text-gray-300 italic text-xs">Non assigné</span>
                                @else
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                        {{ $loc['agent'] }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-10 h-10 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="text-sm text-gray-400">Aucune localisation assignée</p>
                                <p class="text-xs text-gray-300 mt-0.5">Assignez des localisations pour commencer</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inventaireEnCours && in_array($inventaireEnCours->statut, ['en_preparation', 'en_cours']))
            <div wire:poll.10s="refresh" class="hidden"></div>
        @endif

    </div>
    @endif

    {{-- ---- EMPLACEMENTS INVENTORIÉS ---- --}}
    @if($inventaireEnCours && !empty($emplacementsInventories) && auth()->user()->hasPermission('dashboard.stats_inventaire'))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 flex items-center justify-center bg-sky-50 rounded-lg">
                        <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                    Emplacements inventoriés
                </h3>
                <p class="text-xs text-gray-400 mt-1 ml-9">{{ count($emplacementsInventories) }} emplacement(s) avec des biens scannés</p>
            </div>
            <a href="{{ route('emplacements.index') }}" wire:navigate class="text-xs font-medium text-sky-600 hover:text-sky-800 flex items-center gap-1">
                Tous les emplacements <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left">Emplacement</th>
                        <th class="px-5 py-3 text-left">Localisation</th>
                        <th class="px-5 py-3 text-left">Affectation</th>
                        <th class="px-5 py-3 text-center">Scannés</th>
                        <th class="px-5 py-3 text-center">Total</th>
                        <th class="px-5 py-3 text-left">Progression</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @foreach($emplacementsInventories as $emplacement)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-gray-800">{{ $emplacement['nom'] }}</p>
                            @if(!empty($emplacement['code']))
                                <p class="text-xs text-gray-400">{{ $emplacement['code'] }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-gray-600">{{ $emplacement['localisation'] }}</td>
                        <td class="px-5 py-3.5 text-gray-600">{{ $emplacement['affectation'] }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-block px-2.5 py-1 text-xs font-semibold bg-blue-100 text-blue-700 rounded-full">
                                {{ number_format($emplacement['biens_scannes'], 0, ',', ' ') }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center font-semibold text-gray-700">
                            {{ number_format($emplacement['total_biens'], 0, ',', ' ') }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-20 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-1.5 rounded-full transition-all duration-300
                                        {{ $emplacement['progression'] >= 100 ? 'bg-green-500' : ($emplacement['progression'] >= 50 ? 'bg-blue-500' : ($emplacement['progression'] > 0 ? 'bg-yellow-400' : 'bg-gray-200')) }}"
                                        style="width: {{ min($emplacement['progression'], 100) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold {{ $emplacement['progression'] >= 100 ? 'text-green-600' : ($emplacement['progression'] > 0 ? 'text-gray-600' : 'text-gray-300') }}">
                                    {{ round($emplacement['progression'], 1) }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ---- ACTIVITÉ RÉCENTE ---- --}}
    @if(auth()->user()->hasPermission('dashboard.activite_recente'))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <h3 class="text-sm font-semibold text-gray-800 mb-5">Activité récente</h3>
        <div class="space-y-1">
            @forelse($dernieresActions as $action)
                @php
                    $actIcon = match($action['icon']) {
                        'scan'                => ['bg' => 'bg-violet-100', 'color' => 'text-violet-600', 'path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        'inventaire_started'  => ['bg' => 'bg-blue-100',   'color' => 'text-blue-600',   'path' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        'inventaire_closed'   => ['bg' => 'bg-green-100',  'color' => 'text-green-600',  'path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'localisation'        => ['bg' => 'bg-emerald-100','color' => 'text-emerald-600','path' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                        default               => ['bg' => 'bg-gray-100',   'color' => 'text-gray-500',   'path' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    };
                @endphp
                <div class="flex items-start gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="shrink-0 mt-0.5 w-7 h-7 flex items-center justify-center {{ $actIcon['bg'] }} rounded-lg">
                        <svg class="w-3.5 h-3.5 {{ $actIcon['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $actIcon['path'] }}"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-700 leading-snug">{{ $action['message'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $action['time_ago'] }}</p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-10">Aucune activité récente</p>
            @endforelse
        </div>
    </div>
    @endif

    {{-- ---- ACTIONS RAPIDES ---- --}}
    @if(auth()->user()->hasPermission('dashboard.actions_rapides'))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-800 mb-5">Actions rapides</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <a href="{{ route('biens.create') }}" wire:navigate
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl border border-blue-100 bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium text-sm transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Ajouter une immobilisation
            </a>
            <a href="{{ route('localisations.create') }}" wire:navigate
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl border border-green-100 bg-green-50 hover:bg-green-100 text-green-700 font-medium text-sm transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Ajouter une localisation
            </a>
            <a href="{{ route('inventaires.create') }}" wire:navigate
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl border border-violet-100 bg-violet-50 hover:bg-violet-100 text-violet-700 font-medium text-sm transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Démarrer inventaire
            </a>
            <a href="{{ route('users.index') }}" wire:navigate
               class="flex items-center gap-3 px-4 py-3.5 rounded-xl border border-indigo-100 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium text-sm transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Gérer les utilisateurs
            </a>
        </div>
    </div>
    @endif


    @endif {{-- Fin dashboard admin/agent --}}
</div>
