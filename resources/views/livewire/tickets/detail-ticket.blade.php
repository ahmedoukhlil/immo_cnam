<div class="max-w-7xl mx-auto">


    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('tickets.index') }}" wire:navigate class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $ticket->reference }}</h1>
                @php $sc = $ticket->statut_color; @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $sc === 'yellow' ? 'bg-yellow-100 text-yellow-800' : ($sc === 'blue' ? 'bg-blue-100 text-blue-800' : ($sc === 'indigo' ? 'bg-indigo-100 text-indigo-800' : ($sc === 'green' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) }}">
                    {{ $ticket->statut_label }}
                </span>
            </div>
            <p class="text-gray-600 text-lg">{{ $ticket->titre }}</p>
        </div>

        @if(auth()->user()->isTechnicien() && $ticket->assigned_to === auth()->user()->idUser && in_array($ticket->statut, ['assigne', 'en_cours']))
            <a href="{{ route('tickets.traiter', $ticket) }}" wire:navigate
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Traiter ce ticket
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Détails du ticket -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Détails du problème</h2>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->description }}</p>
            </div>

            <!-- Assignation (admin uniquement) -->
            @if(auth()->user()->isAdmin() && !in_array($ticket->statut, ['resolu', 'ferme']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">Assigner à un technicien</h2>
                    @if($techniciens->isEmpty())
                        <p class="text-sm text-gray-500">Aucun technicien disponible. Créez d'abord un compte technicien.</p>
                    @else
                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                                <label class="block text-sm text-gray-600 mb-1">Technicien</label>
                                <select wire:model="technicienId" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">— Choisir un technicien —</option>
                                    @foreach($techniciens as $tech)
                                        <option value="{{ $tech->idUser }}">{{ $tech->users }}</option>
                                    @endforeach
                                </select>
                                @error('technicienId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <button wire:click="assigner"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>Assigner</span>
                                <span wire:loading>...</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Rapport d'intervention -->
            @if($ticket->intervention)
                <div class="bg-white rounded-xl shadow-sm border border-green-200 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2 class="text-base font-semibold text-gray-900">Rapport d'intervention</h2>
                        <span class="text-xs text-gray-500 ml-auto">
                            Par {{ $ticket->intervention->technicien?->users }} — {{ $ticket->intervention->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-1">Problème identifié</h3>
                            <p class="text-gray-700 text-sm bg-red-50 rounded-lg p-3 whitespace-pre-wrap">{{ $ticket->intervention->probleme_identifie }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-1">Solution appliquée</h3>
                            <p class="text-gray-700 text-sm bg-green-50 rounded-lg p-3 whitespace-pre-wrap">{{ $ticket->intervention->solution_appliquee }}</p>
                        </div>
                        @if($ticket->intervention->observations)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">Observations</h3>
                                <p class="text-gray-700 text-sm bg-gray-50 rounded-lg p-3 whitespace-pre-wrap">{{ $ticket->intervention->observations }}</p>
                            </div>
                        @endif

                        <!-- Pièces jointes -->
                        @if($ticket->intervention->piecesJointes->isNotEmpty())
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Captures d'écran ({{ $ticket->intervention->piecesJointes->count() }})</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                    @foreach($ticket->intervention->piecesJointes as $pj)
                                        <a href="{{ $pj->url }}" target="_blank" class="block group">
                                            @if($pj->isImage())
                                                <img src="{{ $pj->url }}" alt="{{ $pj->nom_fichier }}"
                                                     class="w-full h-32 object-cover rounded-lg border border-gray-200 group-hover:opacity-90 transition-opacity">
                                            @else
                                                <div class="w-full h-32 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200">
                                                    <span class="text-xs text-gray-500">{{ $pj->nom_fichier }}</span>
                                                </div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Colonne latérale -->
        <div class="space-y-4">

            <!-- Infos ticket -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wider">Informations</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Priorité</dt>
                        <dd>
                            @php $pc = $ticket->priorite_color; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $pc === 'red' ? 'bg-red-100 text-red-800' : ($pc === 'orange' ? 'bg-orange-100 text-orange-800' : ($pc === 'blue' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                {{ ucfirst($ticket->priorite) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Emplacement</dt>
                        <dd class="font-medium text-gray-800">{{ $ticket->emplacement?->Emplacement ?? '—' }}</dd>
                    </div>
                    @if($ticket->bien)
                        <div>
                            <dt class="text-gray-500">Bien concerné</dt>
                            <dd class="font-medium text-gray-800">N°{{ $ticket->bien->NumOrdre }} — {{ $ticket->bien->designation?->Designation ?? '—' }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-gray-500">Signalé par</dt>
                        <dd class="font-medium text-gray-800">{{ $ticket->createdBy?->users ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Date de création</dt>
                        <dd class="font-medium text-gray-800">{{ $ticket->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    @if($ticket->assignedTo)
                        <div>
                            <dt class="text-gray-500">Technicien assigné</dt>
                            <dd class="font-medium text-gray-800">{{ $ticket->assignedTo->users }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Date d'assignation</dt>
                            <dd class="font-medium text-gray-800">{{ $ticket->assigned_at?->format('d/m/Y à H:i') ?? '—' }}</dd>
                        </div>
                    @endif
                    @if($ticket->resolved_at)
                        <div>
                            <dt class="text-gray-500">Date de résolution</dt>
                            <dd class="font-medium text-green-700 font-semibold">{{ $ticket->resolved_at->format('d/m/Y à H:i') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Action technicien : démarrer intervention -->
            @if(auth()->user()->isTechnicien() && $ticket->assigned_to === auth()->user()->idUser && $ticket->statut === 'assigne')
                <button wire:click="demarrerIntervention"
                        class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                    Démarrer l'intervention
                </button>
            @endif
        </div>
    </div>
</div>
