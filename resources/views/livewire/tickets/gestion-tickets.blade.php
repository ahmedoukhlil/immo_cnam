<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestion des tickets</h1>
            <p class="text-sm text-gray-500 mt-1">Administration et suivi des demandes de maintenance</p>
        </div>
        <a href="{{ route('tickets.index') }}" wire:navigate
           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            Vue liste
        </a>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-gray-800">{{ $kpi['total'] }}</p>
            <p class="text-xs text-gray-500 mt-1 font-medium">Total</p>
        </div>
        <div class="bg-yellow-50 rounded-xl border border-yellow-200 shadow-sm p-4 text-center cursor-pointer hover:bg-yellow-100 transition-colors"
             wire:click="$set('filtreStatut', filtreStatut === 'ouvert' ? '' : 'ouvert')">
            <p class="text-3xl font-bold text-yellow-700">{{ $kpi['ouverts'] }}</p>
            <p class="text-xs text-yellow-600 mt-1 font-medium">Ouverts</p>
        </div>
        <div class="bg-blue-50 rounded-xl border border-blue-200 shadow-sm p-4 text-center cursor-pointer hover:bg-blue-100 transition-colors"
             wire:click="$set('filtreStatut', filtreStatut === 'assigne' ? '' : 'assigne')">
            <p class="text-3xl font-bold text-blue-700">{{ $kpi['assigne'] }}</p>
            <p class="text-xs text-blue-600 mt-1 font-medium">Assignés</p>
        </div>
        <div class="bg-indigo-50 rounded-xl border border-indigo-200 shadow-sm p-4 text-center cursor-pointer hover:bg-indigo-100 transition-colors"
             wire:click="$set('filtreStatut', filtreStatut === 'en_cours' ? '' : 'en_cours')">
            <p class="text-3xl font-bold text-indigo-700">{{ $kpi['en_cours'] }}</p>
            <p class="text-xs text-indigo-600 mt-1 font-medium">En cours</p>
        </div>
        <div class="bg-green-50 rounded-xl border border-green-200 shadow-sm p-4 text-center cursor-pointer hover:bg-green-100 transition-colors"
             wire:click="$set('filtreStatut', filtreStatut === 'ferme' ? '' : 'ferme')">
            <p class="text-3xl font-bold text-green-700">{{ $kpi['resolus'] }}</p>
            <p class="text-xs text-green-600 mt-1 font-medium">Résolus</p>
        </div>
        <div class="bg-red-50 rounded-xl border border-red-200 shadow-sm p-4 text-center cursor-pointer hover:bg-red-100 transition-colors"
             wire:click="$set('filtrePriorite', filtrePriorite === 'urgente' ? '' : 'urgente')">
            <p class="text-3xl font-bold text-red-700">{{ $kpi['urgents'] }}</p>
            <p class="text-xs text-red-600 mt-1 font-medium">🚨 Urgents</p>
        </div>
    </div>

    {{-- Charge des techniciens --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Charge des techniciens
        </h2>
        @if($chargeTechniciens->isEmpty())
            <p class="text-sm text-gray-400 italic">Aucun technicien enregistré.</p>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($chargeTechniciens as $tech)
                @php $pct = $tech->tickets_total > 0 ? round($tech->tickets_resolus / $tech->tickets_total * 100) : 0; @endphp
                <div class="border border-gray-100 rounded-lg p-4 hover:border-indigo-200 hover:bg-indigo-50/30 transition-colors cursor-pointer"
                     wire:click="$set('filtreTechnicien', filtreTechnicien == {{ $tech->idUser }} ? '' : {{ $tech->idUser }})">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($tech->users ?? 'T', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $tech->users }}</p>
                            <p class="text-xs text-gray-500">{{ $tech->tickets_total }} ticket(s) au total</p>
                        </div>
                    </div>
                    <div class="flex gap-3 text-center mb-3">
                        <div class="flex-1">
                            <p class="text-lg font-bold text-indigo-700">{{ $tech->tickets_actifs }}</p>
                            <p class="text-xs text-gray-500">actifs</p>
                        </div>
                        <div class="flex-1">
                            <p class="text-lg font-bold text-green-600">{{ $tech->tickets_resolus }}</p>
                            <p class="text-xs text-gray-500">résolus</p>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1 text-right">{{ $pct }}% résolus</p>
                </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex flex-wrap gap-3 items-center">
            {{-- Recherche --}}
            <div class="relative flex-1 min-w-52">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Référence, titre..."
                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Statut --}}
            <select wire:model.live="filtreStatut"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 appearance-none bg-white pr-8">
                <option value="">Tous les statuts</option>
                <option value="ouvert">Ouvert</option>
                <option value="assigne">Assigné</option>
                <option value="en_cours">En cours</option>
                <option value="resolu">Résolu</option>
                <option value="ferme">Fermé</option>
            </select>

            {{-- Priorité --}}
            <select wire:model.live="filtrePriorite"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 appearance-none bg-white pr-8">
                <option value="">Toutes priorités</option>
                <option value="urgente">🚨 Urgente</option>
                <option value="haute">🔴 Haute</option>
                <option value="normale">🔵 Normale</option>
                <option value="basse">⚪ Basse</option>
            </select>

            {{-- Technicien --}}
            <select wire:model.live="filtreTechnicien"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 appearance-none bg-white pr-8">
                <option value="">Tous les techniciens</option>
                <option value="-1">Non assigné</option>
                @foreach($techniciens as $tech)
                    <option value="{{ $tech->idUser }}">{{ $tech->users }}</option>
                @endforeach
            </select>

            @if($search || $filtreStatut || $filtrePriorite || $filtreTechnicien)
                <button wire:click="$set('search', ''); $set('filtreStatut', ''); $set('filtrePriorite', ''); $set('filtreTechnicien', '')"
                        class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    ✕ Réinitialiser
                </button>
            @endif
        </div>
    </div>

    {{-- Tableau des tickets --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Problème</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emplacement</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priorité</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technicien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créé le</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($tickets as $ticket)
                        @php
                            $prioriteCfg = match($ticket->priorite) {
                                'urgente' => ['bg'=>'bg-red-100',    'text'=>'text-red-800',    'label'=>'🚨 Urgente'],
                                'haute'   => ['bg'=>'bg-orange-100', 'text'=>'text-orange-800', 'label'=>'🔴 Haute'],
                                'normale' => ['bg'=>'bg-blue-100',   'text'=>'text-blue-800',   'label'=>'🔵 Normale'],
                                'basse'   => ['bg'=>'bg-gray-100',   'text'=>'text-gray-700',   'label'=>'⚪ Basse'],
                                default   => ['bg'=>'bg-gray-100',   'text'=>'text-gray-700',   'label'=>$ticket->priorite],
                            };
                            $statutCfg = match($ticket->statut) {
                                'ouvert'   => ['bg'=>'bg-yellow-100', 'text'=>'text-yellow-800', 'label'=>'Ouvert'],
                                'assigne'  => ['bg'=>'bg-blue-100',   'text'=>'text-blue-800',   'label'=>'Assigné'],
                                'en_cours' => ['bg'=>'bg-indigo-100', 'text'=>'text-indigo-800', 'label'=>'En cours'],
                                'resolu'   => ['bg'=>'bg-green-100',  'text'=>'text-green-800',  'label'=>'Résolu'],
                                'ferme'    => ['bg'=>'bg-gray-100',   'text'=>'text-gray-600',   'label'=>'Fermé'],
                                default    => ['bg'=>'bg-gray-100',   'text'=>'text-gray-700',   'label'=>$ticket->statut],
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $ticket->priorite === 'urgente' && !in_array($ticket->statut, ['resolu','ferme']) ? 'border-l-4 border-red-400' : '' }}">
                            <td class="px-4 py-3">
                                <a href="{{ route('tickets.show', $ticket) }}" wire:navigate
                                   class="text-sm font-mono font-medium text-indigo-600 hover:text-indigo-800">
                                    {{ $ticket->reference }}
                                </a>
                                <p class="text-xs text-gray-400">{{ $ticket->createdBy?->users ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 max-w-xs">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $ticket->titre }}</p>
                                @if($ticket->bien?->designation?->designation)
                                    <p class="text-xs text-gray-400 truncate">{{ $ticket->bien->designation->designation }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-700">{{ $ticket->emplacement?->Emplacement ?? '—' }}</p>
                                @if($ticket->emplacement?->affectation)
                                    <p class="text-xs text-gray-400">{{ $ticket->emplacement->affectation->Affectation }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $prioriteCfg['bg'] }} {{ $prioriteCfg['text'] }}">
                                    {{ $prioriteCfg['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statutCfg['bg'] }} {{ $statutCfg['text'] }}">
                                    {{ $statutCfg['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($ticket->assignedTo)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700">
                                            {{ strtoupper(substr($ticket->assignedTo->users ?? 'T', 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $ticket->assignedTo->users }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Non assigné</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-xs text-gray-500">{{ $ticket->created_at?->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-400">{{ $ticket->created_at?->diffForHumans() }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    {{-- Assigner --}}
                                    @if(!in_array($ticket->statut, ['resolu', 'ferme']))
                                        <button wire:click="openAssign({{ $ticket->id }})"
                                                class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Assigner">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Clôturer --}}
                                    @if(!in_array($ticket->statut, ['ferme']))
                                        <button wire:click="confirmerCloture({{ $ticket->id }})"
                                                class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Clôturer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Réouvrir --}}
                                    @if($ticket->statut === 'ferme')
                                        <button wire:click="reouvrirTicket({{ $ticket->id }})"
                                                class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Réouvrir">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Voir --}}
                                    <a href="{{ route('tickets.show', $ticket) }}" wire:navigate
                                       class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors" title="Voir le détail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                </svg>
                                <p class="text-sm font-medium text-gray-500">Aucun ticket trouvé</p>
                                <p class="text-xs text-gray-400 mt-1">Modifiez vos filtres pour voir plus de résultats</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    {{-- Modal : Assigner un technicien --}}
    @if($assignTicketId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="closeAssign">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6" @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-semibold text-gray-900">Assigner le ticket</h3>
                    <button wire:click="closeAssign" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <label class="block text-sm font-medium text-gray-700 mb-2">Technicien</label>
                <select wire:model="technicienAssignId"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none bg-white">
                    <option value="">— Sélectionner un technicien —</option>
                    @foreach($techniciens as $tech)
                        @php
                            $actifs = $tech->ticketsAssignes()->whereIn('statut', ['assigne','en_cours'])->count();
                        @endphp
                        <option value="{{ $tech->idUser }}">
                            {{ $tech->users }} ({{ $actifs }} en cours)
                        </option>
                    @endforeach
                </select>
                @error('technicienAssignId')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeAssign"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button wire:click="assigner"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        Assigner
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal : Confirmer clôture --}}
    @if($closeTicketId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="annulerCloture">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6" @click.stop>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Clôturer le ticket</h3>
                        <p class="text-sm text-gray-500">Cette action est réversible.</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-5">Êtes-vous sûr de vouloir clôturer ce ticket ?</p>
                <div class="flex justify-end gap-3">
                    <button wire:click="annulerCloture"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button wire:click="cloturer"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                        Clôturer
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
