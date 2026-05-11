<div class="max-w-7xl mx-auto">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $isEdit ? 'Modifier l\'utilisateur' : 'Créer un utilisateur' }}
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $isEdit ? 'Modifiez les informations de l\'utilisateur' : 'Ajoutez un nouvel utilisateur au système' }}
                </p>
            </div>
            <a 
                href="{{ route('users.index') }}" wire:navigate
                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </a>
        </div>

        {{-- Messages d'erreur --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Des erreurs ont été détectées
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulaire --}}
        <form wire:submit.prevent="save" class="space-y-6">
            <div 
                wire:loading.class="opacity-50 pointer-events-none"
                class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                
                {{-- Section 1 : Informations personnelles --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Informations personnelles
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nom complet --}}
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom complet
                            </label>
                            <input
                                type="text"
                                id="nom"
                                wire:model="nom"
                                placeholder="Ex: Jean Dupont"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('nom') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('nom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Adresse email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Adresse email
                            </label>
                            <input
                                type="email"
                                id="email"
                                wire:model="email"
                                placeholder="Ex: jdupont@exemple.com"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nom d'utilisateur --}}
                        <div class="md:col-span-2">
                            <label for="users" class="block text-sm font-medium text-gray-700 mb-1">
                                Nom d'utilisateur <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="users"
                                wire:model="users"
                                placeholder="Ex: jdupont"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('users') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('users')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Identifiant unique pour la connexion</p>
                        </div>
                    </div>
                </div>

                {{-- Section 2 : Authentification --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Authentification
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Mot de passe --}}
                        <div>
                            <label for="mdp" class="block text-sm font-medium text-gray-700 mb-1">
                                Mot de passe @if(!$isEdit)<span class="text-red-500">*</span>@else<span class="text-gray-400">(laisser vide pour ne pas modifier)</span>@endif
                            </label>
                            <input 
                                type="password"
                                id="mdp"
                                wire:model="mdp"
                                placeholder="Mot de passe"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('mdp') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('mdp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirmation mot de passe --}}
                        <div>
                            <label for="mdp_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmer le mot de passe @if(!$isEdit)<span class="text-red-500">*</span>@endif
                            </label>
                            <input 
                                type="password"
                                id="mdp_confirmation"
                                wire:model="mdp_confirmation"
                                placeholder="Répétez le mot de passe"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('mdp_confirmation') border-red-300 @enderror"
                                wire:loading.attr="disabled">
                            @error('mdp_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section 3 : Rôle et statut --}}
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        Rôle et statut
                    </h2>

                    <div class="grid grid-cols-1 gap-6">
                        {{-- Rôle --}}
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                Rôle <span class="text-red-500">*</span>
                            </label>
                            <livewire:components.searchable-select
                                wire:model="role"
                                :options="$this->roleOptions"
                                placeholder="Sélectionner un rôle"
                                search-placeholder="Rechercher un rôle..."
                                no-results-text="Aucun rôle trouvé"
                                :allow-clear="false"
                                name="role"
                            />
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <strong>Agent :</strong> Gestion des localisations, biens et inventaires.<br>
                                <strong>Administrateur :</strong> Accès complet, gestion des utilisateurs et tickets.<br>
                                <strong>Technicien :</strong> Traitement des tickets de maintenance assignés.<br>
                                <strong>Occupant :</strong> Signalement de tickets sur ses emplacements.
                            </p>
                        </div>

                        {{-- Emplacements assignés --}}
                        <div
                            x-data="{
                                open: false,
                                search: '',
                                get options() {
                                    return @js($this->emplacementOptions);
                                },
                                get filtered() {
                                    if (!this.search) return this.options;
                                    const q = this.search.toLowerCase();
                                    return this.options.filter(o => o.text.toLowerCase().includes(q));
                                },
                                isSelected(val) {
                                    return $wire.emplacementIds.includes(String(val));
                                },
                                toggle(val) {
                                    const v = String(val);
                                    const idx = $wire.emplacementIds.indexOf(v);
                                    if (idx === -1) {
                                        $wire.emplacementIds = [...$wire.emplacementIds, v];
                                    } else {
                                        $wire.emplacementIds = $wire.emplacementIds.filter(x => x !== v);
                                    }
                                },
                                removeOne(val) {
                                    $wire.emplacementIds = $wire.emplacementIds.filter(x => x !== String(val));
                                },
                                labelOf(val) {
                                    const o = this.options.find(o => String(o.value) === String(val));
                                    return o ? o.text.split(' — ')[0] : val;
                                }
                            }"
                            @click.outside="open = false"
                            class="relative"
                        >
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Emplacements assignés
                                @if($role === 'occupant')
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <p class="text-xs text-gray-500 mb-2">L'utilisateur pourra créer des tickets uniquement sur ces emplacements.</p>

                            {{-- Bouton déclencheur --}}
                            <button
                                type="button"
                                @click="open = !open"
                                class="relative w-full bg-white border border-gray-300 rounded-lg shadow-sm px-3 py-2.5 text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm hover:border-indigo-400 transition-all"
                                :class="{ 'ring-2 ring-indigo-500 border-indigo-500': open }"
                            >
                                <span class="flex flex-wrap gap-1 min-h-[1.25rem]">
                                    <template x-if="$wire.emplacementIds.length === 0">
                                        <span class="text-gray-400 italic">Sélectionner des emplacements...</span>
                                    </template>
                                    <template x-for="val in $wire.emplacementIds" :key="val">
                                        <span class="inline-flex items-center gap-1 bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded-full">
                                            <span x-text="labelOf(val)"></span>
                                            <button type="button" @click.stop="removeOne(val)" class="hover:text-indigo-600 text-indigo-400 leading-none">&times;</button>
                                        </span>
                                    </template>
                                </span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2">
                                    <svg class="h-5 w-5 text-gray-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </button>

                            {{-- Dropdown --}}
                            <div
                                x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden"
                                style="max-height: 380px;"
                            >
                                {{-- Recherche --}}
                                <div class="sticky top-0 bg-white px-3 py-2 border-b border-gray-100">
                                    <div class="relative">
                                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <input
                                            type="text"
                                            x-model="search"
                                            placeholder="Rechercher un emplacement..."
                                            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                            @click.stop
                                        >
                                    </div>
                                    <div class="mt-1.5 flex items-center justify-between text-xs text-gray-400">
                                        <span x-text="filtered.length + ' emplacement(s)'"></span>
                                        <button type="button" @click="$wire.emplacementIds = []" class="hover:text-red-500 transition-colors">Tout effacer</button>
                                    </div>
                                </div>

                                {{-- Liste --}}
                                <div class="overflow-y-auto" style="max-height: 280px;">
                                    <template x-if="filtered.length === 0">
                                        <div class="py-6 text-center text-sm text-gray-400">Aucun résultat</div>
                                    </template>
                                    <template x-for="opt in filtered" :key="opt.value">
                                        <div
                                            @click="toggle(opt.value)"
                                            class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-indigo-50 transition-colors"
                                            :class="{ 'bg-indigo-50': isSelected(opt.value) }"
                                        >
                                            <div class="flex-shrink-0 w-4 h-4 border-2 rounded flex items-center justify-center transition-colors"
                                                 :class="isSelected(opt.value) ? 'bg-indigo-600 border-indigo-600' : 'border-gray-300'">
                                                <svg x-show="isSelected(opt.value)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="block text-sm text-gray-900 truncate" x-text="opt.text.split(' — ')[0]"></span>
                                                <span class="block text-xs text-gray-400 truncate" x-text="opt.text.split(' — ')[1] || ''"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            @if(count($emplacementIds) > 0)
                                <p class="mt-2 text-xs text-indigo-600 font-medium">{{ count($emplacementIds) }} emplacement(s) assigné(s)</p>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button 
                        type="button"
                        wire:click="cancel"
                        class="px-6 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="save">
                            {{ $isEdit ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' }}
                        </span>
                        <span wire:loading wire:target="save">
                            Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

