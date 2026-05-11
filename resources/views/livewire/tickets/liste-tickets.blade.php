<div class="max-w-7xl mx-auto">


    <!-- En-tête -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tickets de maintenance</h1>
            <p class="text-sm text-gray-500 mt-1">Suivi des demandes d'intervention</p>
        </div>
        @if(auth()->user()->isOccupant() || auth()->user()->canManageInventaire())
            <a href="{{ route('tickets.create') }}" wire:navigate
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau ticket
            </a>
        @endif
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher (référence, titre)..."
                       class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select wire:model.live="filtreStatut" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="ouvert">Ouvert</option>
                    <option value="assigne">Assigné</option>
                    <option value="en_cours">En cours</option>
                    <option value="resolu">Résolu</option>
                    <option value="ferme">Fermé</option>
                </select>
            </div>
            <div>
                <select wire:model.live="filtrePriorite" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Toutes les priorités</option>
                    <option value="urgente">Urgente</option>
                    <option value="haute">Haute</option>
                    <option value="normale">Normale</option>
                    <option value="basse">Basse</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($tickets->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-lg font-medium">Aucun ticket trouvé</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Référence</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Titre</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Emplacement</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Priorité</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Technicien</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-sm font-medium text-indigo-600">{{ $ticket->reference }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 max-w-xs truncate">{{ $ticket->titre }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $ticket->emplacement?->Emplacement ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @php $pc = $ticket->priorite_color; @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $pc === 'red' ? 'bg-red-100 text-red-800' : ($pc === 'orange' ? 'bg-orange-100 text-orange-800' : ($pc === 'blue' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                        {{ ucfirst($ticket->priorite) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php $sc = $ticket->statut_color; @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $sc === 'yellow' ? 'bg-yellow-100 text-yellow-800' : ($sc === 'blue' ? 'bg-blue-100 text-blue-800' : ($sc === 'indigo' ? 'bg-indigo-100 text-indigo-800' : ($sc === 'green' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) }}">
                                        {{ $ticket->statut_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $ticket->assignedTo?->users ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('tickets.show', $ticket) }}" wire:navigate
                                       class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Voir</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
