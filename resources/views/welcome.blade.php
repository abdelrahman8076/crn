<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('welcome.title') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .grid-bg {
            background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.05) 1px, transparent 0);
            background-size: 24px 24px;
        }
        .dark .grid-bg {
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
        }
    </style>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen grid-bg font-sans transition-colors duration-300">
    
    <header class="w-full max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-black dark:bg-white rounded-lg flex items-center justify-center">
                <span class="text-white dark:text-black font-bold text-xl">C</span>
            </div>
            <span class="font-bold text-lg tracking-tight">NexusCRM</span>
        </div>

        <nav class="flex items-center gap-6">
            <div class="relative group">
                <button class="flex items-center gap-1 font-medium hover:text-orange-500 transition-colors uppercase text-xs tracking-widest">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    {{ app()->getLocale() }}
                </button>
                <div class="absolute {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} mt-2 w-28 bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                    <a href="{{ url('locale/en') }}" class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-[#1b1b18] {{ app()->getLocale() == 'en' ? 'font-bold text-orange-500' : '' }}">English</a>
                    <a href="{{ url('locale/ar') }}" class="block px-4 py-2 text-sm hover:bg-gray-50 dark:hover:bg-[#1b1b18] {{ app()->getLocale() == 'ar' ? 'font-bold text-orange-500' : '' }}">العربية</a>
                </div>
            </div>

            @auth
                <a href="{{ url('/dashboard') }}" class="px-5 py-2 bg-black text-white dark:bg-white dark:text-black rounded-full font-medium transition-transform hover:scale-105">
                    {{ __('welcome.dashboard') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="font-medium hover:text-[#706f6c] transition-colors">
                    {{ __('welcome.login') }}
                </a>
                <a href="{{ route('register') }}" class="px-5 py-2 bg-black text-white dark:bg-white dark:text-black rounded-full font-medium transition-transform hover:scale-105">
                    {{ __('welcome.get_started') }}
                </a>
            @endauth
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-6 pt-16 pb-24 flex flex-col items-center text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 text-xs font-bold mb-8 border border-orange-100 dark:border-orange-800">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
            </span>
            {{ __('welcome.new_update') }}
        </div>

        <h1 class="text-5xl lg:text-7xl font-bold tracking-tighter mb-6 max-w-4xl leading-tight">
            {{ __('welcome.hero_title') }} 
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-rose-500">
                {{ __('welcome.hero_accent') }}
            </span>
        </h1>

        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] max-w-2xl mb-10 leading-relaxed">
            {{ __('welcome.hero_description') }}
        </p>

        <div class="flex flex-col sm:flex-row gap-4 mb-20">
            <a href="{{ route('register') }}" class="px-8 py-4 bg-black text-white dark:bg-white dark:text-black rounded-xl font-semibold text-lg shadow-xl hover:opacity-90 transition-all">
                {{ __('welcome.cta_primary') }}
            </a>
            <button class="px-8 py-4 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-xl font-semibold text-lg hover:bg-gray-50 dark:hover:bg-[#161615] transition-all">
                {{ __('welcome.cta_secondary') }}
            </button>
        </div>

        <div class="w-full max-w-5xl aspect-video bg-white dark:bg-[#161615] rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-2xl overflow-hidden relative p-6">
            <div class="w-full h-full bg-gray-50 dark:bg-[#0a0a0a] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 lg:p-8">
                
                <div class="flex justify-between items-center mb-8">
                    <div class="text-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}">
                        <h3 class="text-lg font-bold">{{ __('welcome.mockup_target_title') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('welcome.mockup_target_subtitle') }}</p>
                    </div>
                    <div class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-bold">
                        {{ __('welcome.mockup_status') }}
                    </div>
                </div>

                <div class="mb-10">
                    <div class="flex justify-between mb-2 text-sm font-medium">
                        <span>{{ __('welcome.mockup_collected') }}: $75,000</span>
                        <span class="text-orange-500 font-bold">75%</span>
                    </div>
                    <div class="w-full h-4 bg-gray-200 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-orange-500 to-rose-500 w-[75%] rounded-full shadow-[0_0_15px_rgba(245,48,3,0.3)]"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-[10px] text-gray-400 uppercase tracking-widest">
                        <span>$0</span>
                        <span>{{ __('welcome.mockup_goal') }}: $100,000</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php
                        $dummyTeam = [
                            ['name' => app()->getLocale() == 'ar' ? 'أحمد علي' : 'Alex Rivera', 'prog' => 'w-[85%]', 'color' => 'bg-emerald-500'],
                            ['name' => app()->getLocale() == 'ar' ? 'سارة محمود' : 'Sarah Chen', 'prog' => 'w-[60%]', 'color' => 'bg-orange-500'],
                            ['name' => app()->getLocale() == 'ar' ? 'محمد عبدالله' : 'M. Abdullah', 'prog' => 'w-[45%]', 'color' => 'bg-blue-500'],
                        ];
                    @endphp
                    @foreach($dummyTeam as $member)
                    <div class="p-4 bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-xl shadow-sm">
                        <p class="text-xs font-bold mb-3 text-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}">{{ $member['name'] }}</p>
                        <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full">
                            <div class="h-full {{ $member['color'] }} {{ $member['prog'] }} rounded-full"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>

    <footer class="w-full py-12 border-t border-[#e3e3e0] dark:border-[#3E3E3A] text-center">
        <p class="text-sm text-[#706f6c]">© {{ date('Y') }} NexusCRM. {{ __('welcome.rights_reserved') }}</p>
    </footer>
</body>
</html>