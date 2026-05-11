<div class="max-w-7xl mx-auto">

    {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Permissions de {{ $user->users }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Rôle :
                    @php
                        $badge = match($user->role) {
                            'admin'      => 'bg-purple-100 text-purple-800',
                            'agent'      => 'bg-blue-100 text-blue-800',
                            'technicien' => 'bg-green-100 text-green-800',
                            'occupant'   => 'bg-orange-100 text-orange-800',
                            default      => 'bg-gray-100 text-gray-800',
                        };
                        $roleLabel = match($user->role) {
                            'admin'      => '👑 Administrateur',
                            'agent'      => '👤 Agent',
                            'technicien' => '🔧 Technicien',
                            'occupant'   => '🏢 Occupant',
                            default      => $user->role,
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                        {{ $roleLabel }}
                    </span>
                </p>
            </div>
            <a href="{{ route('users.roles') }}" wire:navigate
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                ← Retour aux rôles
            </a>
        </div>

        {{-- Flash messages --}}
        @if (session()->has('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Info défaut du rôle --}}
        <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
            <strong>ℹ️ Note :</strong> Les cases pré-cochées correspondent aux permissions par défaut du rôle <strong>{{ $roleLabel }}</strong>.
            Vous pouvez ajouter ou retirer des permissions individuellement. Les surcharges sont indiquées par un badge.
        </div>

        <form wire:submit.prevent="save">
            <div class="space-y-4">
                @foreach($permissions as $module => $modulePerms)
                    @php
                        $moduleLabel = $moduleLabels[$module] ?? ucfirst($module);
                        $modulePermNames = $modulePerms->pluck('name')->toArray();
                        $checkedCount = count(array_intersect($modulePermNames, $selectedPermissions));
                        $totalCount   = count($modulePermNames);
                        $allChecked   = $checkedCount === $totalCount;
                    @endphp
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        {{-- Module header --}}
                        <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-center gap-3">
                                <input type="checkbox"
                                       wire:click="toggleModule('{{ $module }}')"
                                       @checked($allChecked)
                                       class="w-4 h-4 text-indigo-600 rounded border-gray-300 cursor-pointer">
                                <span class="font-semibold text-gray-800">{{ $moduleLabel }}</span>
                            </div>
                            <span class="text-xs text-gray-500">{{ $checkedCount }}/{{ $totalCount }}</span>
                        </div>

                        {{-- Permissions list --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-0 divide-y divide-gray-100">
                            @foreach($modulePerms as $permission)
                                @php
                                    $isChecked  = in_array($permission->name, $selectedPermissions);
                                    $isDefault  = in_array($permission->name, $defaultPermissions);
                                    $isOverride = $isChecked !== $isDefault;
                                @endphp
                                <label class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 cursor-pointer transition-colors">
                                    <input type="checkbox"
                                           wire:model="selectedPermissions"
                                           value="{{ $permission->name }}"
                                           class="w-4 h-4 text-indigo-600 rounded border-gray-300">
                                    <span class="text-sm text-gray-700 flex-1">{{ $permission->label }}</span>
                                    @if($isOverride)
                                        @if($isChecked)
                                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">+ajouté</span>
                                        @else
                                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-medium">−retiré</span>
                                        @endif
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex items-center justify-between">
                <button type="button"
                        wire:click="resetToDefaults"
                        wire:confirm="Réinitialiser aux permissions par défaut du rôle ?"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    🔄 Réinitialiser aux défauts
                </button>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistrer les permissions
                </button>
            </div>
        </form>
</div>
