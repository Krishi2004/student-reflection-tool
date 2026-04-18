<header class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <!-- Logo/App Name -->
        <a href="{{ route('welcome') }}">@include('components.application-logo')</a>

        <nav class="flex space-x-4 items-center">
            <!-- Public Links -->
            <!--<a href="#" class="text-gray-600 hover:text-indigo-600 px-3">About</a>
            <a href="#" class="text-gray-600 hover:text-indigo-600 px-3">Features</a>

            {{-- Logic to switch between Logged In/Out --}}-->
            @auth
                <a href="{{ route('dashboard') }}"
                    class="px-3 py-2 font-semibold border-b-2 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                    Dashboard
                </a>

                <a href="{{ route('reflection') }}"
                    class="px-3 py-2 font-semibold border-b-2 transition-colors duration-200 {{ request()->routeIs('reflection*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                    Reflections
                </a>

                <a href="{{ route('goals') }}"
                    class="px-3 py-2 font-semibold border-b-2 transition-colors duration-200 {{ request()->routeIs('goals*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                    Goals
                </a>

                <a href="{{ route('analytics') }}"
                    class="px-3 py-2 font-semibold border-b-2 transition-colors duration-200 {{ request()->routeIs('analytics*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                    Analytics
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-150 shadow-md">
                        Logout
                    </button>
                </form>
            @else
                <!-- If LOGGED OUT, show Login and Sign Up -->
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 px-3">Login</a>
                <a href="{{ route('register') }}" class="text-gray-600 hover:text-indigo-600 px-3">Sign Up</a>
            @endauth
        </nav>
    </div>
</header>