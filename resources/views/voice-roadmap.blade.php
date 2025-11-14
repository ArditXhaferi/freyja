<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Voice-Driven Roadmap Builder - Espoo Business Advisory</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body>
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900 hover:text-blue-600">
                        Espoo Business Advisory
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-700">
                        Welcome, {{ Auth::user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <div id="voice-roadmap-root"></div>
</body>
</html>

