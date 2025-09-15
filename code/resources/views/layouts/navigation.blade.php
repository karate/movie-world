<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}">
                    <h1 class="text-4xl m-4 font-extrabold tracking-tight text-blue-700 drop-shadow-lg select-none">Movie World</h1>
                </a>
                @auth
                <a href="{{ route('profile.movies') }}" class="hidden sm:inline-block bg-gray-100 hover:bg-gray-200 font-semibold px-4 py-2 rounded transition">My Movies (edit)</a>
                @endauth
            </div>

            @if (!Auth::check())
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline me-4 m-4">{{ __('Login') }}</a> or 
                    <a href="{{ route('register') }}" class="text-sm text-gray-700 underline m-4">{{ __('Sign Up') }}</a>
                </div>
            @else
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <span class="mr-2">Welcome back</span>
                    <a href="{{ route('home', ['user' => Auth::user()->id]) }}" class="text-sm text-blue-700 underline me-4">{{ Auth::user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-700 underline">{{ __('Log Out') }}</button>
                    </form>
                </div>
            @endif

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 {{ request()->routeIs('home') ? 'font-bold' : '' }}">All Movies</a>
            @auth
            <a href="{{ route('profile.movies') }}" class="block px-4 py-2 text-sm text-blue-700 {{ request()->routeIs('profile.movies') ? 'font-bold' : '' }}">My Movies</a>
            @endauth
        </div>
    <div class="pt-4 pb-1 border-t border-gray-200">
            @if (Auth::check())
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700">{{ __('Log Out') }}</button>
                    </form>
                </div>
            @else
                <div class="px-4">
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700">{{ __('Login') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-gray-700 mb-2">{{ __('Register') }}</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</nav>
