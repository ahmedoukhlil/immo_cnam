<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Natures Juridiques</h1>
            <p class="mt-1 text-sm text-gray-500">Gérez les natures juridiques. Une nature inactive n'apparaît plus dans le formulaire.</p>
        </div>
        @if(!$showForm)
        <button wire:click="openCreate"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajouter
        </button>
        @endif
    </div>

    @if($showForm)
    <div class="bg-white border border-indigo-200 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $editId ? 'Modifier la nature juridique' : 'Nouvelle nature juridique' }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Libellé <span class="text-red-500">*</span></label>
                <input type="text" wire:model="libelle" placeholder="Ex: Propriété"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('libelle') border-red-400 @enderror">
                @error('libelle') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                <input type="text" wire:model="code" placeholder="Ex: PROP"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
        <div class="flex gap-3 mt-4">
            <button wire:click="save"
                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                {{ $editId ? 'Modifier' : 'Enregistrer' }}
            </button>
            <button wire:click="cancel"
                class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Annuler
            </button>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Libellé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($natures as $nature)
                <tr class="hover:bg-gray-50 transition-colors {{ !$nature->actif ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $nature->NatJur }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $nature->CodeNatJur ?? '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        <button wire:click="toggleActif({{ $nature->idNatJur }})"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors
                                {{ $nature->actif ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            <span class="w-2 h-2 rounded-full mr-1.5 {{ $nature->actif ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $nature->actif ? 'Actif' : 'Inactif' }}
                        </button>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button wire:click="openEdit({{ $nature->idNatJur }})"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Modifier
                        </button>
                        <button wire:click="delete({{ $nature->idNatJur }})"
                            wire:confirm="Supprimer cette nature juridique ?"
                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Supprimer
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">Aucune nature juridique trouvée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(session()->has('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
        class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
    @endif
    @if(session()->has('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
        class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('error') }}
    </div>
    @endif
</div>
