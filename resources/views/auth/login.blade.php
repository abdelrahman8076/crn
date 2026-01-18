@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

<script src="https://cdn.tailwindcss.com"></script>

<script>
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
        .input-focus {
            @apply focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all;
        }
    }
</style>

<div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC] dark:bg-[#0a0a0a] grid-bg font-sans" 
     dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
    
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-[#161615] p-8 rounded-3xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-2xl shadow-black/5 animate-in fade-in zoom-in duration-300">
        
        <div class="text-center">
            <div class="mx-auto h-14 w-14 bg-black dark:bg-white rounded-2xl flex items-center justify-center shadow-xl shadow-orange-500/10 group hover:scale-110 transition-transform duration-300">
                <span class="text-white dark:text-black font-black text-2xl">C</span>
            </div>
            <h2 class="mt-6 text-3xl font-black tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ __('welcome.login') }}
            </h2>
            <p class="mt-2 text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
                {{ __('admins.login_subtitle') }}
            </p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-widest text-[#1b1b18] dark:text-[#EDEDEC] mb-2 px-1">
                        {{ __('admins.email') }}
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                        class="appearance-none block w-full px-4 py-3.5 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl placeholder-gray-400 input-focus bg-gray-50/50 dark:bg-[#0a0a0a] dark:text-white sm:text-sm"
                        placeholder="name@company.com"
                        value="{{ old('email') }}">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 px-1" />
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2 px-1">
                        <label for="password" class="block text-xs font-bold uppercase tracking-widest text-[#1b1b18] dark:text-[#EDEDEC]">
                            {{ __('admins.password') }}
                        </label>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                        class="appearance-none block w-full px-4 py-3.5 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-2xl placeholder-gray-400 input-focus bg-gray-50/50 dark:bg-[#0a0a0a] dark:text-white sm:text-sm"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 px-1" />
                </div>
            </div>


            <div>
                <button type="submit" 
                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-black rounded-2xl text-white dark:text-black bg-black dark:bg-white hover:opacity-90 active:scale-[0.98] transition-all shadow-xl shadow-black/5">
                    {{ __('admins.login_btn') }}
                </button>
            </div>
        </form>

        <div class="mt-8 text-center border-t border-[#e3e3e0] dark:border-[#3E3E3A] pt-8">
            <a href="{{ url('locale/' . ($isArabic ? 'en' : 'ar')) }}" 
               class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-[#706f6c] dark:text-[#A1A09A] hover:text-orange-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                {{ $isArabic ? 'English Version' : 'النسخة العربية' }}
            </a>
        </div>
    </div>
</div>