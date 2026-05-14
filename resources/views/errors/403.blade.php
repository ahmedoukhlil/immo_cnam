<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès non autorisé</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="mb-6">
            <svg class="mx-auto w-24 h-24 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <h1 class="text-5xl font-bold text-gray-800 mb-2">403</h1>
        <p class="text-xl text-gray-600 mb-2">Accès non autorisé</p>
        <p class="text-sm text-gray-400 mb-8">Vous n'avez pas la permission d'accéder à cette page.</p>
        <a href="{{ url('/dashboard') }}"
           class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            Retour au tableau de bord
        </a>
    </div>
</body>
</html>
