    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC] dark:bg-[#0a0a0a] grid-bg" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
        
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-[#161615] p-8 rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-xl">
            
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-black dark:bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <span class="text-white dark:text-black font-bold text-2xl">C</span>
                </div>
                <h2 class="mt-6 text-3xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                    {{ __('welcome.login') }}
                </h2>
                <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    {{ __('admins.login_subtitle') }}
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">
                            {{ __('admins.email') }}
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="appearance-none block w-full px-3 py-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50 dark:bg-[#0a0a0a] dark:text-white sm:text-sm transition-all"
                            placeholder="name@company.com"
                            value="{{ old('email') }}">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                {{ __('admins.password') }}
                            </label>
                            {{-- @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-orange-600 hover:text-orange-500">
                                    {{ __('admins.forgot_password') }}
                                </a>
                            @endif --}}
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                            class="appearance-none block w-full px-3 py-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-xl placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50 dark:bg-[#0a0a0a] dark:text-white sm:text-sm transition-all"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" 
                            class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-[#e3e3e0] dark:border-[#3E3E3A] rounded bg-gray-50 dark:bg-[#0a0a0a]">
                        <label for="remember_me" class="{{ $isArabic ? 'mr-2' : 'ml-2' }} block text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ __('admins.remember_me') }}
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-black dark:bg-white dark:text-black hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all transform hover:scale-[1.02]">
                        {{ __('admins.login_btn') }}
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center border-t border-[#e3e3e0] dark:border-[#3E3E3A] pt-6">
                <a href="{{ url('locale/' . ($isArabic ? 'en' : 'ar')) }}" class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] hover:text-orange-500 transition-colors">
                    {{ $isArabic ? 'Switch to English' : 'تغيير للغة العربية' }}
                </a>
            </div>
        </div>
    </div>

<style>
    .grid-bg {
        background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.05) 1px, transparent 0);
        background-size: 24px 24px;
    }
    .dark .grid-bg {
        background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
    }
</style>