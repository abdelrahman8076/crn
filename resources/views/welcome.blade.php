<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('welcome.title') }}</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        // Tailwind Configuration
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Instrument Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style type="text/tailwindcss">
        @layer utilities {
            .grid-bg {
                background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.05) 1px, transparent 0);
                background-size: 24px 24px;
            }
            .dark .grid-bg {
                background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
            }
        }
    </style>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen grid-bg font-sans transition-colors duration-300">
    
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-black dark:bg-white rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20">
                <span class="text-white dark:text-black font-black text-xl">N</span>
            </div>
            <span class="font-bold text-xl tracking-tight">Nexus<span class="text-orange-500">CRM</span></span>
        </div>

        <nav class="flex items-center gap-4 sm:gap-6">
            <div class="relative group">
                <button class="flex items-center gap-2 font-bold hover:text-orange-500 transition-colors uppercase text-xs tracking-widest bg-gray-100 dark:bg-white/5 px-3 py-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    {{ app()->getLocale() }}
                </button>
                <div class="absolute {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} mt-2 w-32 bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 overflow-hidden">
                    <a href="{{ url('locale/en') }}" class="block px-4 py-3 text-sm hover:bg-orange-50 dark:hover:bg-white/5 {{ app()->getLocale() == 'en' ? 'font-bold text-orange-500' : '' }}">English</a>
                    <a href="{{ url('locale/ar') }}" class="block px-4 py-3 text-sm hover:bg-orange-50 dark:hover:bg-white/5 {{ app()->getLocale() == 'ar' ? 'font-bold text-orange-500' : '' }}">العربية</a>
                </div>
            </div>

            @auth
                <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-black text-white dark:bg-white dark:text-black rounded-xl font-semibold shadow-lg shadow-black/10 dark:shadow-white/5 hover:-translate-y-0.5 transition-all">
                    {{ __('welcome.dashboard') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="font-semibold hover:text-orange-500 transition-colors">
                    {{ __('welcome.login') }}
                </a>
            @endauth
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-6 pt-20 pb-24 flex flex-col items-center text-center">
        <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full bg-orange-50 dark:bg-orange-950/30 text-orange-600 dark:text-orange-400 text-xs font-bold mb-10 border border-orange-100 dark:border-orange-900/50 shadow-sm">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
            </span>
            {{ __('welcome.new_update') }}
        </div>

        <h1 class="text-6xl lg:text-8xl font-black tracking-tighter mb-8 max-w-5xl leading-[1.1]">
            {{ __('welcome.hero_title') }} 
            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-orange-500 via-rose-500 to-amber-500">
                {{ __('welcome.hero_accent') }}
            </span>
        </h1>

        <p class="text-xl text-[#706f6c] dark:text-[#A1A09A] max-w-2xl mb-12 leading-relaxed font-medium">
            {{ __('welcome.hero_description') }}
        </p>

        <div class="flex flex-col sm:flex-row gap-5 mb-24">
            <button class="px-10 py-5 bg-orange-500 hover:bg-orange-600 text-white rounded-2xl font-bold text-lg shadow-xl shadow-orange-500/25 hover:-translate-y-1 transition-all">
                Get Started Free
            </button>
            <button class="px-10 py-5 border-2 border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl font-bold text-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-all">
                {{ __('welcome.cta_secondary') }}
            </button>
        </div>

        <div class="w-full max-w-5xl aspect-video rounded-3xl border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white/50 dark:bg-black/50 backdrop-blur-xl shadow-2xl relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-tr from-orange-500/10 to-transparent opacity-50"></div>
            <div class="p-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A] flex gap-2">
                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                <div class="w-3 h-3 rounded-full bg-green-400"></div>
            </div>
            <div class="flex items-center justify-center h-full text-[#706f6c] italic">
                NexusCRM Interface Preview
            </div>
        </div>
    </main>

    <footer class="w-full py-12 border-t border-[#e3e3e0] dark:border-[#3E3E3A] text-center bg-white/30 dark:bg-black/30 backdrop-blur-sm">
        <p class="text-sm font-medium text-[#706f6c]">© {{ date('Y') }} NexusCRM. {{ __('welcome.rights_reserved') }}</p>
    </footer>
</body>
</html>