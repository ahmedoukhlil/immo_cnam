<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{
    sidebarOpen: window.innerWidth >= 768,
    isDesktop: window.innerWidth >= 768,
    _resizeHandler: null,
    init() {
        this.isDesktop   = window.innerWidth >= 768;
        this.sidebarOpen = this.isDesktop;
        this._resizeHandler = () => {
            this.isDesktop   = window.innerWidth >= 768;
            this.sidebarOpen = this.isDesktop;
        };
        window.addEventListener('resize', this._resizeHandler);
    },
    destroy() {
        window.removeEventListener('resize', this._resizeHandler);
    }
}" class="h-full" :class="{ 'overflow-hidden': sidebarOpen && !isDesktop }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- PWA Meta Tags --}}
    <meta name="theme-color" content="#383f7b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Immos CNAM">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="description" content="Immos CNAM - Gestion des immobilisations">

    <title>{{ config('app.name', 'Immos CNAM') }} - @yield('title', 'Dashboard')</title>

    {{-- PWA Manifest --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    {{-- PWA Icons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('images/icons/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/icons/icon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/icons/icon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/icons/icon-180x180.png') }}">
    <meta name="theme-color" content="#4f46e5">

    <!-- Fonts (local, pas de requête réseau externe) -->
    <style>
        @font-face {
            font-family: 'Figtree';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
            src: url('/fonts/figtree-400.woff2') format('woff2');
        }
        @font-face {
            font-family: 'Figtree';
            font-style: normal;
            font-weight: 500;
            font-display: swap;
            src: url('/fonts/figtree-500.woff2') format('woff2');
        }
        @font-face {
            font-family: 'Figtree';
            font-style: normal;
            font-weight: 600;
            font-display: swap;
            src: url('/fonts/figtree-600.woff2') format('woff2');
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    {{-- Alpine.js est déjà inclus dans Livewire 3, ne pas le charger séparément --}}

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased h-screen overflow-hidden">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            x-show="sidebarOpen || isDesktop"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-indigo-800 text-white flex flex-col"
            :class="{ 'translate-x-0': isDesktop || sidebarOpen }"
        >
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-6 bg-indigo-900 border-b border-indigo-700">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('cnam-logo.jpg') }}" alt="CNAM" class="h-10 w-auto object-contain rounded">
                    <div class="flex flex-col leading-tight">
                        <span class="font-bold text-lg">Immos CNAM</span>
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider">Immobilisations</span>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3" x-data="{
                openMenu: '',
                currentPath: window.location.pathname,
                activeMenu(path) {
                    if (path.startsWith('/biens')) return 'immobilisations';
                    if (path.startsWith('/localisations') || path.startsWith('/affectations') || path.startsWith('/emplacements') || path.startsWith('/designations')) return 'parametres';
                    if (path.startsWith('/tickets')) return 'tickets';
                    return '';
                },
                isActive(prefix) {
                    return this.currentPath.startsWith(prefix);
                },
                isActiveExact(path) {
                    return this.currentPath === path || this.currentPath === path + '/';
                },
                init() {
                    this.openMenu = this.activeMenu(this.currentPath);
                    document.addEventListener('livewire:navigated', () => {
                        this.currentPath = window.location.pathname;
                        this.openMenu = this.activeMenu(this.currentPath);
                    });
                },
                toggle(menu) {
                    this.openMenu = this.openMenu === menu ? '' : menu;
                }
            }">
                <ul class="space-y-1">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('dashboard') }}" wire:navigate
                           class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                           :class="{ 'bg-indigo-700 text-white': isActiveExact('/dashboard') }">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    @auth
                        @if(auth()->user()->canManageInventaire())
                            <!-- IMMOBILISATIONS - Accordéon -->
                            <li>
                                <button @click="toggle('immobilisations')"
                                        class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                        :class="{ 'bg-indigo-700 text-white': openMenu === 'immobilisations' }">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <span>Immobilisations</span>
                                    </div>
                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenu === 'immobilisations' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <ul x-show="openMenu === 'immobilisations'" x-cloak x-transition class="mt-2 space-y-1 pl-4">
                                    <li>
                                        <a href="{{ route('biens.index') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/biens') && !isActive('/biens/create') && !isActive('/biens/transfert') && !isActive('/biens/amortissements') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                            <span>Liste</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('biens.create') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/biens/create') || currentPath.includes('/edit') && isActive('/biens') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            <span>Ajouter</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('biens.transfert') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActiveExact('/biens/transfert') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                            <span>Transfert</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('biens.transfert.historique') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/biens/transfert/historique') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Historique Transferts</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('biens.amortissements') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/biens/amortissements') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                            <span>Amortissements</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- PARAMETRES - Accordéon -->
                            <li>
                                <button @click="toggle('parametres')"
                                        class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                        :class="{ 'bg-indigo-700 text-white': openMenu === 'parametres' }">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>Paramètres</span>
                                    </div>
                                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenu === 'parametres' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <ul x-show="openMenu === 'parametres'" x-cloak x-transition class="mt-2 space-y-1 pl-4">
                                    <li>
                                        <a href="{{ route('localisations.index') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/localisations') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span>Localisations</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('affectations.index') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/affectations') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <span>Affectations</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('emplacements.index') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/emplacements') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                            <span>Emplacements</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('designations.index') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/designations') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                            <span>Désignations</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('parametres.etats') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/parametres/etats') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>États</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('parametres.natures-juridiques') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/parametres/natures-juridiques') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                            <span>Natures Juridiques</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('parametres.sources-financement') }}" wire:navigate
                                           class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                           :class="{ 'bg-indigo-700 text-white': isActive('/parametres/sources-financement') }">
                                            <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Sources Financement</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- Inventaires -->
                            <li>
                                <a href="{{ route('inventaires.index') }}" wire:navigate
                                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                   :class="{ 'bg-indigo-700 text-white': isActive('/inventaires') }">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <span>Inventaires</span>
                                </a>
                            </li>
                        @endif

                        @if(auth()->check() && auth()->user()->canAccessTickets())
                            @php
                                $nbOuverts = auth()->user()->isAdmin()
                                    ? \App\Models\Ticket::where('statut', 'ouvert')->count()
                                    : (auth()->user()->isTechnicien()
                                        ? \App\Models\Ticket::where('assigned_to', auth()->user()->idUser)->whereIn('statut', ['assigne','en_cours'])->count()
                                        : 0);
                            @endphp

                            @if(auth()->user()->isAdmin())
                                <!-- Tickets - Accordéon Admin -->
                                <li>
                                    <button @click="toggle('tickets')"
                                            class="w-full flex items-center justify-between px-4 py-3 text-gray-300 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                            :class="{ 'bg-indigo-700 text-white': openMenu === 'tickets' }">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                            </svg>
                                            <span>Tickets</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            @if($nbOuverts > 0)
                                                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $nbOuverts }}</span>
                                            @endif
                                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenu === 'tickets' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </button>
                                    <ul x-show="openMenu === 'tickets'" x-cloak x-transition class="mt-2 space-y-1 pl-4">
                                        <li>
                                            <a href="{{ route('tickets.index') }}" wire:navigate
                                               class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                               :class="{ 'bg-indigo-700 text-white': isActive('/tickets') && !isActive('/tickets/gestion') }">
                                                <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                                <span>Liste</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('tickets.gestion') }}" wire:navigate
                                               class="flex items-center px-4 py-2 text-sm text-gray-400 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                               :class="{ 'bg-indigo-700 text-white': isActive('/tickets/gestion') }">
                                                <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                                <span>Gestion</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @else
                                <!-- Tickets - Lien simple -->
                                <li>
                                    <a href="{{ route('tickets.index') }}" wire:navigate
                                       class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                       :class="{ 'bg-indigo-700 text-white': isActive('/tickets') }">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                        </svg>
                                        <span>Tickets</span>
                                        @if($nbOuverts > 0)
                                            <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $nbOuverts }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        @endif

                        @if(auth()->check() && auth()->user()->isAdmin())
                            <!-- Utilisateurs -->
                            <li>
                                <a href="{{ route('users.index') }}" wire:navigate
                                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                                   :class="{ 'bg-indigo-700 text-white': isActive('/users') }">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <span>Utilisateurs</span>
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
            </nav>

            <!-- Footer Sidebar -->
            <div class="px-4 py-4 border-t border-indigo-700">
                <div class="text-xs text-gray-400 mb-2">
                    Version 1.0.0
                </div>
            </div>
        </aside>

        <!-- Overlay mobile -->
        <div 
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 md:hidden"
            x-cloak
        ></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 shadow-sm h-16 flex items-center justify-between px-4 md:px-6 z-30">
                <!-- Left: Hamburger -->
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Right: Notifications + Profile -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" x-init="document.addEventListener('livewire:navigated', () => open = false)">
                            <button 
                                @click="open = !open"
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                            >
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr(auth()->user()->users ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="hidden md:block text-left">
                                        <div class="text-sm font-medium text-gray-900">{{ auth()->user()->users ?? 'Utilisateur' }}</div>
                                        <div class="text-xs text-gray-500">
                                            @php
                                                $roleClass = match(auth()->user()->role) {
                                                    'admin'      => 'bg-purple-100 text-purple-800',
                                                    'agent'      => 'bg-blue-100 text-blue-800',
                                                    'technicien' => 'bg-green-100 text-green-800',
                                                    'occupant'   => 'bg-orange-100 text-orange-800',
                                                    default      => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $roleClass }}">
                                                {{ auth()->user()->role_name }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div 
                                x-show="open"
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200"
                                x-cloak
                            >
                                <a href="{{ route('profil') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mon profil</a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Toast notification globale -->
            <div
                x-data="{ show: false, message: '' }"
                x-on:notify.window="message = $event.detail; show = true; setTimeout(() => show = false, 2500)"
                x-show="show"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed bottom-5 right-5 z-50 bg-gray-900 text-white text-sm px-4 py-2 rounded-lg shadow-lg"
                x-cloak>
                <span x-text="message"></span>
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-secondary-50">
                <!-- Main Content -->
                <div class="p-4 md:p-6">
                    {{ $slot ?? '' }}
                </div>

                <!-- Footer -->
                <footer class="bg-white border-t border-gray-200 px-4 md:px-6 py-4">
                    <p class="text-sm text-gray-500 text-center">© 2025 Immos CNAM</p>
                </footer>
            </main>
        </div>
    </div>

    @livewireScripts
    
    @stack('scripts')
    
    {{-- PWA Service Worker Registration --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('sw.js') }}')
                    .then((registration) => {
                        console.log('✅ Service Worker enregistré:', registration.scope);
                        setInterval(() => registration.update(), 60000);
                    })
                    .catch((error) => {
                        console.error('❌ Erreur Service Worker:', error);
                    });
            });
        }
    </script>
</body>
</html>

