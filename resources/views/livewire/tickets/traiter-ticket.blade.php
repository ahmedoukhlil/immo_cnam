<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    {{-- En-tête --}}
    <div class="mb-8 flex items-center gap-3">
        <a href="{{ route('tickets.show', $ticket) }}" wire:navigate
           class="w-8 h-8 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-gray-700 hover:border-gray-300 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rapport d'intervention</h1>
            <p class="text-sm text-gray-400 mt-0.5">
                <span class="font-medium text-gray-500">{{ $ticket->reference }}</span>
                <span class="mx-1.5 text-gray-300">·</span>
                {{ $ticket->titre }}
            </p>
        </div>
    </div>

    {{-- Résumé du ticket --}}
    <div class="bg-white rounded-2xl border border-indigo-100 shadow-sm p-5 mb-8">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-6 h-6 flex items-center justify-center bg-indigo-100 rounded-lg">
                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            </div>
            <h2 class="text-sm font-semibold text-indigo-700">Détails du ticket</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm mb-4">
            <div class="flex flex-col gap-0.5">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Emplacement</span>
                <span class="text-gray-800 font-medium">{{ $ticket->emplacement?->Emplacement ?? '—' }}</span>
            </div>
            @if($ticket->bien)
            <div class="flex flex-col gap-0.5">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Bien concerné</span>
                <span class="text-gray-800 font-medium">{{ $ticket->bien->designation?->designation ?? '—' }}</span>
                @if($ticket->bien->code_inventaire)
                    <span class="text-xs text-gray-400">{{ $ticket->bien->code_inventaire }}</span>
                @endif
            </div>
            @endif
            <div class="flex flex-col gap-0.5">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Signalé par</span>
                <span class="text-gray-800 font-medium">{{ $ticket->createdBy?->users }}</span>
            </div>
        </div>
        <div class="pt-4 border-t border-gray-100">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Description</span>
            <p class="text-gray-700 text-sm mt-1.5 leading-relaxed">{{ $ticket->description }}</p>
        </div>
    </div>

    {{-- Historique des interventions sur ce bien --}}
    @if($ticket->bien_id && count($historiqueInterventions) > 0)
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-6 h-6 flex items-center justify-center bg-amber-100 rounded-lg">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-sm font-semibold text-amber-700">
                Historique des interventions sur ce bien
                <span class="ml-1 text-xs font-normal text-amber-500">({{ count($historiqueInterventions) }} intervention(s) précédente(s))</span>
            </h2>
        </div>

        <div class="space-y-3">
            @foreach($historiqueInterventions as $hist)
            <div class="bg-white rounded-2xl border border-amber-100 shadow-sm overflow-hidden">
                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-3 bg-amber-50/60 border-b border-amber-100">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full">
                            {{ $hist['ticket']['reference'] ?? '—' }}
                        </span>
                        <span class="text-sm font-medium text-gray-700 truncate max-w-xs">
                            {{ $hist['ticket']['titre'] ?? '—' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-400 shrink-0">
                        <span>{{ $hist['technicien']['users'] ?? '—' }}</span>
                        <span>{{ \Carbon\Carbon::parse($hist['created_at'])->format('d/m/Y') }}</span>
                    </div>
                </div>
                {{-- Corps --}}
                <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Problème identifié</p>
                        <p class="text-gray-700 leading-relaxed line-clamp-4 whitespace-pre-wrap">{{ $hist['probleme_identifie'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Solution appliquée</p>
                        <p class="text-gray-700 leading-relaxed line-clamp-4 whitespace-pre-wrap">{{ $hist['solution_appliquee'] }}</p>
                    </div>
                    @if(!empty($hist['observations']))
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Observations</p>
                        <p class="text-gray-600 text-xs leading-relaxed line-clamp-2 whitespace-pre-wrap">{{ $hist['observations'] }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Formulaire --}}
    <div class="max-w-2xl">
        <form wire:submit="save" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/60">
                <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <div class="w-6 h-6 flex items-center justify-center bg-green-100 rounded-lg">
                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    Compte rendu d'intervention
                </h2>
            </div>

            <div class="p-6 space-y-6">

                {{-- Problème identifié --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Problème identifié <span class="text-red-400 normal-case font-medium">*</span>
                    </label>
                    <textarea wire:model="problemeIdentifie" rows="4"
                              placeholder="Décrivez le problème technique identifié lors de l'intervention…"
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"></textarea>
                    @error('problemeIdentifie')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Solution appliquée --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Solution appliquée <span class="text-red-400 normal-case font-medium">*</span>
                    </label>
                    <textarea wire:model="solutionAppliquee" rows="4"
                              placeholder="Décrivez la solution mise en œuvre pour résoudre le problème…"
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"></textarea>
                    @error('solutionAppliquee')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Observations --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Observations
                        <span class="normal-case font-normal text-gray-400 ml-1">(optionnel)</span>
                    </label>
                    <textarea wire:model="observations" rows="3"
                              placeholder="Remarques supplémentaires, recommandations, pièces à remplacer…"
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-800 placeholder-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"></textarea>
                    @error('observations')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Photos --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Photos / Captures
                        <span class="normal-case font-normal text-gray-400 ml-1">(optionnel — max 10 × 5 Mo)</span>
                    </label>

                    <label for="photos-input"
                           class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-200 rounded-xl p-6 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/30 transition-colors group">
                        <input type="file" wire:model="photos" multiple accept="image/*" class="hidden" id="photos-input">
                        <div class="w-10 h-10 flex items-center justify-center bg-gray-100 group-hover:bg-indigo-100 rounded-xl transition-colors">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-600 group-hover:text-indigo-600 transition-colors">Cliquez pour sélectionner des images</p>
                            <p class="text-xs text-gray-400 mt-0.5">PNG, JPG, WEBP — Max 5 Mo par image</p>
                        </div>
                    </label>

                    <div wire:loading wire:target="photos" class="flex items-center gap-1.5 text-xs text-indigo-600 mt-2">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        Upload en cours…
                    </div>
                    @error('photos.*')
                        <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                    @enderror

                    @if(count($photos))
                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 mt-3">
                            @foreach($photos as $photo)
                                <div class="relative group">
                                    <img src="{{ $photo->temporaryUrl() }}" alt="Aperçu"
                                         class="w-full h-20 object-cover rounded-xl border border-gray-200 shadow-sm">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/60 flex items-center gap-3">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 disabled:opacity-60 text-white font-medium text-sm rounded-xl shadow-sm transition-colors">
                    <span wire:loading.remove wire:target="save">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span wire:loading wire:target="save">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                    </span>
                    <span wire:loading.remove wire:target="save">Marquer comme résolu</span>
                    <span wire:loading wire:target="save">Enregistrement…</span>
                </button>
                <a href="{{ route('tickets.show', $ticket) }}" wire:navigate
                   class="px-4 py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 rounded-xl hover:bg-gray-100 transition-colors">
                    Annuler
                </a>
            </div>

        </form>
    </div>

</div>
