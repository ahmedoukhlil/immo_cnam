<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Catégories — Type de parc</h1>
        <p class="mt-1 text-sm text-gray-500">
            Associez chaque catégorie à un type de parc (<strong>Informatique</strong> ou <strong>Matériel</strong>)
            pour contrôler l'accès des admins spécialisés.
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Catégorie</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Code</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Type de parc</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $cat->Categorie }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $cat->CodeCategorie }}</td>
                    <td class="px-4 py-3">
                        <select wire:change="setTypeParc({{ $cat->idCategorie }}, $event.target.value)"
                            class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500
                                   {{ $cat->type_parc === 'informatique' ? 'bg-blue-50 text-blue-700 border-blue-300' : ($cat->type_parc === 'materiel' ? 'bg-amber-50 text-amber-700 border-amber-300' : '') }}">
                            <option value="" @selected(!$cat->type_parc)>— Non défini —</option>
                            <option value="informatique" @selected($cat->type_parc === 'informatique')>Informatique</option>
                            <option value="materiel" @selected($cat->type_parc === 'materiel')>Matériel</option>
                        </select>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-gray-400">Aucune catégorie.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-xs text-gray-400">
        Les catégories sans type de parc sont accessibles à tous les administrateurs.
    </p>
</div>
