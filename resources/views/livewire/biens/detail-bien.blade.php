<div class="max-w-7xl mx-auto" x-data="{
    confirmDelete: false,
    doDelete() { this.confirmDelete = false; $wire.supprimer(); }
}">
    @php $isAdmin = auth()->user()->isAdmin(); @endphp

    {{-- Breadcrumb --}}
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center gap-1.5 text-sm text-gray-500">
            <li><a href="{{ route('dashboard') }}" wire:navigate class="hover:text-indigo-600 transition-colors">Dashboard</a></li>
            <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
            <li><a href="{{ route('biens.index') }}" wire:navigate class="hover:text-indigo-600 transition-colors">Immobilisations</a></li>
            <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg></li>
            <li class="font-medium text-gray-700">{{ $bien->NumOrdre }}</li>
        </ol>
    </nav>

    {{-- Header --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h1 class="text-xl font-bold text-gray-900">{{ $bien->designation ? $bien->designation->designation : 'N/A' }}</h1>
                        @if($bien->categorie)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $bien->categorie->Categorie }}</span>
                        @endif
                        @if($bien->etat)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $bien->etat->Etat }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-500">
                        <span class="font-mono font-semibold text-indigo-600 text-base">N° {{ $bien->NumOrdre }}</span>
                        @if($bien->code_formate)
                            <span class="text-gray-300">|</span>
                            <code class="text-xs bg-gray-100 px-2 py-0.5 rounded font-mono">{{ $bien->code_formate }}</code>
                            <button @click="navigator.clipboard.writeText('{{ $bien->code_formate }}').then(() => $dispatch('notify', 'Code copié !'))" class="text-gray-400 hover:text-indigo-600 transition-colors" title="Copier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('biens.index') }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Retour
                </a>

                @if($isAdmin && !$editing)
                    <button wire:click="startEditing" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-amber-700 bg-amber-100 rounded-lg hover:bg-amber-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Modifier
                    </button>
                    <a href="{{ route('biens.edit', $bien) }}" wire:navigate class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        Formulaire complet
                    </a>
                @endif

                @if($editing)
                    <button wire:click="saveDetails" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer
                    </button>
                    <button wire:click="cancelEditing" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Annuler
                    </button>
                @endif

                <button data-print-etiquette="{{ $bien->NumOrdre }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimer
                </button>

                @if($isAdmin && !$editing)
                    <button @click="confirmDelete = true" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Supprimer
                    </button>
                @endif
            </div>
        </div>

        @if($editing)
            <div class="mt-4 flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2.5">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span class="text-sm text-amber-800 font-medium">Mode modification — modifiez les champs puis cliquez sur "Enregistrer"</span>
            </div>
        @endif
    </div>

    {{-- Corps : 2 colonnes --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Colonne gauche --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Informations générales --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Informations générales</h2>
                <div class="grid grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Numéro d'ordre</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ $bien->NumOrdre }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Désignation</p>
                        <p class="text-sm font-medium text-gray-900">{{ $bien->designation->designation ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Année d'acquisition</p>
                        @if($editing)
                            <input type="number" wire:model="editDateAcquisition" min="1900" max="{{ now()->year + 1 }}"
                                class="mt-0.5 block w-full px-3 py-1.5 border border-amber-300 rounded-lg text-sm bg-amber-50 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('editDateAcquisition') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @else
                            <p class="text-sm font-medium text-gray-900">
                                @if($bien->DateAcquisition && $bien->DateAcquisition > 1970)
                                    {{ $bien->DateAcquisition }}
                                    @if($this->age && $this->age > 0)
                                        <span class="text-gray-400 text-xs">({{ $this->age }} an{{ $this->age > 1 ? 's' : '' }})</span>
                                    @endif
                                @else <span class="text-gray-300">—</span> @endif
                            </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Date de mise en service</p>
                        @if($editing)
                            <input type="date" wire:model="editDateMiseEnService"
                                class="mt-0.5 block w-full px-3 py-1.5 border border-amber-300 rounded-lg text-sm bg-amber-50 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('editDateMiseEnService') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @else
                            <p class="text-sm font-medium text-gray-900">{{ $bien->date_mise_en_service ? $bien->date_mise_en_service->format('d/m/Y') : '—' }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Valeur d'acquisition</p>
                        @if($editing)
                            <input type="number" wire:model="editValeurAcquisition" min="0" step="0.01"
                                class="mt-0.5 block w-full px-3 py-1.5 border border-amber-300 rounded-lg text-sm bg-amber-50 focus:ring-indigo-500 focus:border-indigo-500">
                            @error('editValeurAcquisition') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @else
                            <p class="text-sm font-medium text-gray-900">
                                @if($bien->valeur_acquisition)
                                    <span class="font-semibold">{{ number_format($bien->valeur_acquisition, 2, ',', ' ') }}</span>
                                    <span class="text-xs text-gray-400">MRU</span>
                                @else <span class="text-gray-300">—</span> @endif
                            </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Nature juridique</p>
                        <p class="text-sm font-medium text-gray-900">{{ $bien->natureJuridique->NatJur ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Source de financement</p>
                        <p class="text-sm font-medium text-gray-900">{{ $bien->sourceFinancement->SourceFin ?? '—' }}</p>
                    </div>
                    @if($bien->categorie && $bien->categorie->duree_amortissement)
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Type CGI / Amortissement</p>
                        <p class="text-sm font-medium text-gray-900">{{ $bien->categorie->type_cgi ?? '—' }} — {{ $bien->categorie->duree_amortissement }} ans ({{ $bien->categorie->taux_amortissement }}%)</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Emplacement --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Localisation & Emplacement</h2>
                @if($bien->emplacement)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($bien->emplacement->localisation)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-400 mb-1">Localisation</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $bien->emplacement->localisation->Localisation }}</p>
                            @if($bien->emplacement->localisation->CodeLocalisation)
                                <p class="text-xs text-gray-400 mt-1 font-mono">{{ $bien->emplacement->localisation->CodeLocalisation }}</p>
                            @endif
                        </div>
                        @endif
                        @if($bien->emplacement->affectation)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-400 mb-1">Affectation</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $bien->emplacement->affectation->Affectation }}</p>
                            @if($bien->emplacement->affectation->CodeAffectation)
                                <p class="text-xs text-gray-400 mt-1 font-mono">{{ $bien->emplacement->affectation->CodeAffectation }}</p>
                            @endif
                        </div>
                        @endif
                        <div class="bg-indigo-50 rounded-xl p-4">
                            <p class="text-xs text-indigo-400 mb-1">Emplacement</p>
                            <p class="text-sm font-semibold text-indigo-800">{{ $bien->emplacement->Emplacement }}</p>
                            @if($bien->emplacement->CodeEmplacement)
                                <p class="text-xs text-indigo-400 mt-1 font-mono">{{ $bien->emplacement->CodeEmplacement }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Aucun emplacement assigné</p>
                @endif
            </div>

            {{-- Observations --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Observations</h2>
                @if($editing)
                    <textarea wire:model="editObservations" rows="4" placeholder="Saisir des observations..."
                        class="block w-full px-3 py-2 border border-amber-300 rounded-lg text-sm bg-amber-50 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    @error('editObservations') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                @elseif($bien->Observations)
                    <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $bien->Observations }}</p>
                @else
                    <p class="text-sm text-gray-400 italic">Aucune observation</p>
                @endif
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="space-y-6">

            {{-- QR Code --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">QR Code</h2>
                <div class="cursor-pointer hover:opacity-80 transition-opacity bg-white border border-gray-200 rounded-xl p-3 mb-2 flex flex-col items-center justify-center"
                    @click="$dispatch('open-barcode-modal')" title="Cliquez pour agrandir">
                    <div id="qrcode-{{ $bien->NumOrdre }}" style="display:flex;justify-content:center;"></div>
                    @if($bien->code_formate)
                    <p class="text-center text-[10px] font-mono text-gray-800 mt-2 tracking-wider">{{ $bien->code_formate }}</p>
                    @endif
                    @if($bien->designation)
                    <p class="text-center text-[9px] text-gray-500 mt-0.5 leading-tight">{{ $bien->designation->designation }}</p>
                    @endif
                </div>
                <button data-print-etiquette="{{ $bien->NumOrdre }}"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimer l'étiquette
                </button>
            </div>

            {{-- Liens rapides --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Voir aussi</h2>
                <div class="space-y-2">
                    @if($bien->emplacement)
                        <a href="{{ route('biens.index', ['filterEmplacement' => $bien->idEmplacement]) }}" wire:navigate
                            class="flex items-center gap-2 w-full px-3 py-2.5 text-sm font-medium text-gray-700 bg-gray-50 rounded-xl hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                            Biens de cet emplacement
                        </a>
                    @endif
                    @if($bien->categorie)
                        <a href="{{ route('biens.index', ['filterCategorie' => $bien->idCategorie]) }}" wire:navigate
                            class="flex items-center gap-2 w-full px-3 py-2.5 text-sm font-medium text-gray-700 bg-gray-50 rounded-xl hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            Biens de cette catégorie
                        </a>
                    @endif
                    @if($bien->designation)
                        <a href="{{ route('biens.index', ['filterDesignation' => $bien->idDesignation]) }}" wire:navigate
                            class="flex items-center gap-2 w-full px-3 py-2.5 text-sm font-medium text-gray-700 bg-gray-50 rounded-xl hover:bg-indigo-50 hover:text-indigo-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h7"/></svg>
                            Biens de cette désignation
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Amortissement --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-5 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Amortissement
        </h2>

        @if($this->resumeAmortissement)
            @php $resume = $this->resumeAmortissement; @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-xs text-blue-500 font-medium mb-1">Valeur d'acquisition</p>
                    <p class="text-lg font-bold text-blue-900">{{ number_format($resume['valeur_acquisition'], 2, ',', ' ') }}</p>
                    <p class="text-xs text-blue-400">MRU</p>
                </div>
                <div class="bg-amber-50 rounded-xl p-4">
                    <p class="text-xs text-amber-500 font-medium mb-1">Dotation annuelle</p>
                    <p class="text-lg font-bold text-amber-900">{{ number_format($resume['dotation_annuelle'], 2, ',', ' ') }}</p>
                    <p class="text-xs text-amber-400">MRU/an · {{ $resume['taux'] }}%</p>
                </div>
                <div class="bg-green-50 rounded-xl p-4">
                    <p class="text-xs text-green-500 font-medium mb-1">VNC actuelle</p>
                    <p class="text-lg font-bold text-green-900">{{ number_format($resume['vnc'], 2, ',', ' ') }}</p>
                    <p class="text-xs text-green-400">MRU</p>
                </div>
                <div class="{{ $resume['est_totalement_amorti'] ? 'bg-red-50' : 'bg-purple-50' }} rounded-xl p-4">
                    <p class="text-xs {{ $resume['est_totalement_amorti'] ? 'text-red-500' : 'text-purple-500' }} font-medium mb-1">Statut</p>
                    @if($resume['est_totalement_amorti'])
                        <p class="text-sm font-bold text-red-800">Totalement amorti</p>
                    @else
                        <p class="text-lg font-bold text-purple-900">{{ $resume['annees_restantes'] }} an{{ $resume['annees_restantes'] > 1 ? 's' : '' }}</p>
                        <p class="text-xs text-purple-400">restant{{ $resume['annees_restantes'] > 1 ? 's' : '' }}</p>
                    @endif
                </div>
            </div>

            <div class="mb-5">
                <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                    <span>{{ $resume['type_cgi'] }} · {{ $resume['duree'] }} ans · Cumulé : <strong>{{ number_format($resume['amortissement_cumule'], 2, ',', ' ') }} MRU</strong></span>
                    <span>{{ $resume['date_debut'] }} → {{ $resume['date_fin'] }} · <strong>{{ $resume['pourcentage_amorti'] }}%</strong></span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full transition-all duration-500 {{ $resume['est_totalement_amorti'] ? 'bg-red-500' : 'bg-indigo-500' }}" style="width: {{ min($resume['pourcentage_amorti'], 100) }}%"></div>
                </div>
            </div>

            @if($this->tableauAmortissement)
                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                <th class="px-4 py-3 text-left">Exercice</th>
                                <th class="px-4 py-3 text-right">Valeur amortissable</th>
                                <th class="px-4 py-3 text-right">Taux</th>
                                <th class="px-4 py-3 text-right">Dotation</th>
                                <th class="px-4 py-3 text-right">Amort. cumulé</th>
                                <th class="px-4 py-3 text-right">VNC</th>
                                <th class="px-4 py-3 text-center">Note</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @foreach($this->tableauAmortissement['lignes'] as $ligne)
                                <tr class="hover:bg-gray-50 transition-colors {{ $ligne['exercice'] == now()->year ? 'bg-indigo-50 font-semibold' : '' }}">
                                    <td class="px-4 py-2.5 text-gray-900">
                                        {{ $ligne['exercice'] }}
                                        @if($ligne['exercice'] == now()->year)
                                            <span class="ml-1 text-[10px] bg-indigo-200 text-indigo-800 px-1.5 py-0.5 rounded-full">en cours</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-gray-600">{{ number_format($ligne['valeur_amortissable'], 2, ',', ' ') }}</td>
                                    <td class="px-4 py-2.5 text-right text-gray-600">{{ $ligne['taux'] }}%</td>
                                    <td class="px-4 py-2.5 text-right font-medium text-gray-900">{{ number_format($ligne['dotation'], 2, ',', ' ') }}</td>
                                    <td class="px-4 py-2.5 text-right text-gray-600">{{ number_format($ligne['cumul'], 2, ',', ' ') }}</td>
                                    <td class="px-4 py-2.5 text-right font-medium text-gray-900">{{ number_format($ligne['vnc'], 2, ',', ' ') }}</td>
                                    <td class="px-4 py-2.5 text-center text-xs text-gray-400">{{ $ligne['prorata'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <div class="text-center py-10">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500">Amortissement non disponible</p>
                <p class="text-xs text-gray-400 mt-1">{{ $this->raisonNonAmortissable }}</p>
                @if($isAdmin && !$editing && (!$bien->valeur_acquisition || !$bien->date_mise_en_service))
                    <button wire:click="startEditing" class="mt-3 inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200 transition-colors">
                        Renseigner les informations financières
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- Modal QR Code agrandi --}}
    <div x-data="{ open: false }" x-on:open-barcode-modal.window="open = true"
        x-show="open" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;" x-cloak>
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-white rounded-2xl p-8 max-w-sm w-full shadow-2xl" @click.stop
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="flex flex-col items-center justify-center bg-white p-6 rounded-xl border border-gray-100">
                <div id="qrcode-modal-{{ $bien->NumOrdre }}" style="display:flex;justify-content:center;"></div>
                @if($bien->code_formate)
                <p class="text-center text-xs font-mono font-bold text-gray-800 mt-3 tracking-wider">{{ $bien->code_formate }}</p>
                @endif
                @if($bien->designation)
                <p class="text-center text-xs text-gray-500 mt-1">{{ $bien->designation->designation }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal confirmation suppression --}}
    <div x-show="confirmDelete"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display:none;" @keydown.escape.window="confirmDelete = false">
        <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="confirmDelete = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="px-6 pt-6 pb-4 flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Supprimer ce bien</h3>
                    <p class="mt-1 text-sm text-gray-500">Le bien <strong>{{ $bien->NumOrdre }}</strong> sera définitivement supprimé. Cette action est irréversible.</p>
                </div>
            </div>
            <div class="h-px bg-gray-100 mx-6"></div>
            <div class="px-6 py-4 flex justify-end gap-3">
                <button @click="confirmDelete = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Annuler</button>
                <button @click="doDelete()" class="px-5 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">Supprimer</button>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
            class="fixed bottom-5 right-5 z-50 flex items-center gap-3 bg-gray-900 text-white px-4 py-3 rounded-xl shadow-lg text-sm">
            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
            class="fixed bottom-5 right-5 z-50 flex items-center gap-3 bg-red-600 text-white px-4 py-3 rounded-xl shadow-lg text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    @if(isset($bien) && $bien->NumOrdre)
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        (function() {
            const BIEN_ID      = {{ $bien->NumOrdre }};
            const CODE_VALUE   = '{{ $bien->NumOrdre }}';
            const CODE_FORMATE = @json($bien->code_formate ?? '');
            const DESIGNATION  = @json($bien->designation->designation ?? '');

            function generateQRCode() {
                if (typeof QRCode === 'undefined') return;

                const mainEl = document.getElementById('qrcode-' + BIEN_ID);
                if (mainEl && mainEl.innerHTML === '') {
                    new QRCode(mainEl, {
                        text: CODE_VALUE,
                        width: 120,
                        height: 120,
                        colorDark: '#000000',
                        colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel.M
                    });
                }

                const modalEl = document.getElementById('qrcode-modal-' + BIEN_ID);
                if (modalEl && modalEl.innerHTML === '') {
                    new QRCode(modalEl, {
                        text: CODE_VALUE,
                        width: 240,
                        height: 240,
                        colorDark: '#000000',
                        colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel.M
                    });
                }
            }

            async function imprimerEtiquette() {
                try {
                    if (typeof QRCode === 'undefined' || typeof window.jspdf === 'undefined') { alert('Bibliothèques non chargées, rechargez la page.'); return; }
                    const { jsPDF } = window.jspdf;
                    const labelWidthMm = 60, labelHeightMm = 60;
                    const mmToPx = 3.779527559;

                    // Générer QR code dans un canvas temporaire
                    const tempDiv = document.createElement('div');
                    tempDiv.style.cssText = 'position:absolute;left:-9999px';
                    document.body.appendChild(tempDiv);
                    const qr = new QRCode(tempDiv, {
                        text: CODE_VALUE,
                        width: 200, height: 200,
                        colorDark: '#000000', colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel.M
                    });

                    setTimeout(() => {
                        const qrImg = tempDiv.querySelector('img') || tempDiv.querySelector('canvas');
                        const pdfCanvas = document.createElement('canvas');
                        pdfCanvas.width = labelWidthMm * mmToPx;
                        pdfCanvas.height = labelHeightMm * mmToPx;
                        const ctx = pdfCanvas.getContext('2d');
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, pdfCanvas.width, pdfCanvas.height);

                        const qrSize = Math.min(labelWidthMm, labelHeightMm) - 10;
                        const qrSizePx = qrSize * mmToPx;
                        const offsetX = ((labelWidthMm - qrSize) / 2) * mmToPx;
                        const offsetY = 4 * mmToPx;

                        if (qrImg) ctx.drawImage(qrImg, offsetX, offsetY, qrSizePx, qrSizePx);
                        document.body.removeChild(tempDiv);

                        const pdf = new jsPDF({ orientation:'portrait', unit:'mm', format:[labelWidthMm, labelHeightMm] });
                        pdf.addImage(pdfCanvas.toDataURL('image/png', 1.0), 'PNG', 0, 0, labelWidthMm, labelHeightMm);

                        let y = offsetY + qrSize + 6;
                        if (CODE_FORMATE && CODE_FORMATE.trim()) {
                            pdf.setFontSize(6); pdf.setFont('courier','normal');
                            pdf.text(CODE_FORMATE, labelWidthMm/2, y, { align:'center' }); y += 4;
                        }
                        if (DESIGNATION && DESIGNATION.trim()) {
                            pdf.setFontSize(5); pdf.setFont('helvetica','normal');
                            pdf.splitTextToSize(DESIGNATION, labelWidthMm - 4).slice(0,2).forEach(line => {
                                if (y < labelHeightMm - 1) { pdf.text(line, labelWidthMm/2, y, { align:'center' }); y += 3; }
                            });
                        }

                        const url = URL.createObjectURL(pdf.output('blob'));
                        const win = window.open(url, '_blank');
                        if (win) { win.onload = () => setTimeout(() => { win.print(); setTimeout(() => URL.revokeObjectURL(url), 1000); }, 250); }
                        else { pdf.save('etiquette_' + CODE_VALUE + '.pdf'); }
                    }, 200);
                } catch(e) { alert('Erreur: ' + e.message); }
            }

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-print-etiquette]');
                if (btn && btn.dataset.printEtiquette == BIEN_ID) imprimerEtiquette();
            });

            function init() { setTimeout(generateQRCode, 100); }
            document.addEventListener('livewire:navigated', init);
            if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }
        })();
    </script>
    @endif
</div>
