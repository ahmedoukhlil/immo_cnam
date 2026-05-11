<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Paramètres des champs</span>
                    </div>
                </li>
            </ol>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900">Paramètres des champs</h1>
        <p class="mt-1 text-sm text-gray-500">
            Activez ou désactivez les champs du formulaire d'immobilisation. Un champ actif est <strong>obligatoire</strong>, un champ inactif est <strong>masqué</strong>.
        </p>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Champ</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($champs as $key => $champ)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 rounded-full {{ $champ['actif'] ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                            <span class="text-sm font-medium text-gray-900">{{ $champ['label'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($champ['actif'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Actif — Obligatoire
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Inactif — Masqué
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button
                            wire:click="toggle('{{ $key }}')"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                {{ $champ['actif']
                                    ? 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-200'
                                    : 'bg-green-50 text-green-700 hover:bg-green-100 border border-green-200' }}">
                            <span wire:loading.remove wire:target="toggle('{{ $key }}')">
                                {{ $champ['actif'] ? 'Désactiver' : 'Activer' }}
                            </span>
                            <span wire:loading wire:target="toggle('{{ $key }}')">...</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Message flash --}}
    @if(session()->has('success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 3000)"
            x-transition
            class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif
</div>
