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
                <!-- If LOGGED IN, show Dashboard and Logout -->
                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 px-3 font-semibold">Dashboard</a>
                <a href="{{ route('reflection') }}" class="text-indigo-600 hover:text-indigo-800 px-3 font-semibold">Reflections</a>
                <a href="{{ route('goals') }}" class="text-indigo-600 hover:text-indigo-800 px-3 font-semibold">Goals</a>
                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 px-3 font-semibold">Analytics</a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-150 shadow-md">
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