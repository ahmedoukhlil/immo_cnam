<div class="max-w-7xl mx-auto">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Créer un ticket de maintenance</h1>
                <p class="mt-1 text-sm text-gray-500">Signalez un problème sur un bien de votre emplacement</p>
            </div>
            <a href="{{ route('tickets.index') }}" wire:navigate
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>

        {{-- Erreurs --}}
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Des erreurs ont été détectées</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit="save" class="space-y-6">
            <div wire:loading.class="opacity-50 pointer-events-none"
                 class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

                {{-- Section 1 : Localisation --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Localisation
                    </h2>

                    <div class="max-w-md" x-data="{ search: '' }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Emplacement <span class="text-red-500">*</span>
                        </label>

                        {{-- Wrapper stylisé autour du select natif --}}
                        <div class="relative">
                            <select
                                wire:model.live="idEmplacement"
                                class="block w-full pl-3 pr-10 py-2.5 border border-gray-300 rounded-lg shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm appearance-none @error('idEmplacement') border-red-300 @enderror"
                                wire:loading.attr="disabled"
                            >
                                <option value="">— Sélectionnez un emplacement —</option>
                                @foreach($emplacements as $emp)
                                    <option value="{{ $emp->idEmplacement }}">
                                        {{ $emp->Emplacement }}{{ $emp->CodeEmplacement ? ' ('.$emp->CodeEmplacement.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>

                        @error('idEmplacement')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Section 2 : Bien concerné --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-1 pb-2 border-b border-gray-200">
                        Bien concerné
                        <span class="text-sm font-normal text-gray-400 ml-1">(optionnel)</span>
                    </h2>

                    @if(!$idEmplacement)
                        <p class="text-sm text-gray-400 italic mt-3">Sélectionnez d'abord un emplacement pour voir les biens disponibles.</p>
                    @elseif($biens->isEmpty())
                        <p class="text-sm text-gray-400 italic mt-3">Aucun bien enregistré pour cet emplacement.</p>
                    @else
                        <div class="mt-3 space-y-3">
                            {{-- Carte "Problème général" — pleine largeur --}}
                            <div
                                wire:click="$set('bien_id', null)"
                                class="flex items-center gap-4 p-4 rounded-lg border-2 cursor-pointer transition-all
                                       {{ is_null($bien_id) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300 bg-white' }}"
                            >
                                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                                            {{ is_null($bien_id) ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold {{ is_null($bien_id) ? 'text-indigo-700' : 'text-gray-700' }}">
                                        Problème général
                                    </p>
                                    <p class="text-xs text-gray-400">Aucun bien spécifique concerné</p>
                                </div>
                                @if(is_null($bien_id))
                                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-indigo-500 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Grille des biens --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach($biens as $bien)
                                    @php $selected = $bien_id == $bien->NumOrdre; @endphp
                                    <div
                                        wire:click="$set('bien_id', {{ $bien->NumOrdre }})"
                                        class="flex items-start gap-3 p-3 rounded-lg border-2 cursor-pointer transition-all
                                               {{ $selected ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300 bg-white' }}"
                                    >
                                        <div class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors
                                                    {{ $selected ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300' }}">
                                            @if($selected)
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                {{ $bien->designation?->designation ?? 'Bien sans désignation' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">N° {{ $bien->NumOrdre }}</p>
                                            @if($bien->valeur_acquisition)
                                                <p class="text-xs text-gray-400">{{ number_format($bien->valeur_acquisition, 0, ',', ' ') }} MRU</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @error('bien_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Section 3 : Détails du problème --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Détails du problème
                    </h2>

                    <div class="space-y-5">
                        {{-- Titre --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Titre du problème <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="titre"
                                placeholder="Ex: Écran défectueux, climatiseur en panne..."
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('titre') border-red-300 @enderror"
                                wire:loading.attr="disabled"
                            >
                            @error('titre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                wire:model="description"
                                rows="4"
                                placeholder="Décrivez le problème en détail : symptômes, fréquence, circonstances..."
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-300 @enderror"
                                wire:loading.attr="disabled"
                            ></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Priorité --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Priorité <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @foreach([
                                    'basse'   => ['Basse',   'text-green-700',  'border-green-400  bg-green-50',  'bg-green-100'],
                                    'normale' => ['Normale', 'text-blue-700',   'border-blue-400   bg-blue-50',   'bg-blue-100'],
                                    'haute'   => ['Haute',   'text-orange-700', 'border-orange-400 bg-orange-50', 'bg-orange-100'],
                                    'urgente' => ['Urgente', 'text-red-700',    'border-red-400    bg-red-50',    'bg-red-100'],
                                ] as $val => [$label, $textClass, $activeClass, $dotClass])
                                    <label class="flex items-center gap-2 px-4 py-3 border-2 rounded-lg cursor-pointer transition-all
                                        {{ $priorite === $val ? $activeClass . ' ' . $textClass : 'border-gray-200 hover:border-gray-300 text-gray-600' }}">
                                        <input type="radio" wire:model.live="priorite" value="{{ $val }}" class="sr-only">
                                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ $priorite === $val ? $dotClass : 'bg-gray-300' }}"></span>
                                        <span class="text-sm font-medium">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('priorite')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('tickets.index') }}" wire:navigate
                       class="px-6 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="save">Créer le ticket</span>
                        <span wire:loading wire:target="save">Enregistrement...</span>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
