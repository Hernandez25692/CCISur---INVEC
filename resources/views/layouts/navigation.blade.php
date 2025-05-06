<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo + Links -->
            <div class="flex items-center space-x-6">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <img src="{{ asset('Logo/logo_menu.png') }}" alt="INVEC" class="h-10 w-auto">
                    <span class="text-lg font-bold text-blue-700">INVEC</span>
                </a>

                <!-- Enlaces principales -->
                <div class="hidden md:flex space-x-4 ml-6">
                    <a href="{{ route('dashboard') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium {{ request()->routeIs('dashboard') ? 'underline' : '' }}">Dashboard</a>
                    <a href="{{ route('mobiliario.index') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium {{ request()->routeIs('mobiliario.*') ? 'underline' : '' }}">
                        Mobiliario
                    </a>
                    <a href="{{ route('dispositivos.index') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium {{ request()->routeIs('dispositivos.*') ? 'underline' : '' }}">
                        Dispositivos
                    </a>
                    <a href="{{ route('asignaciones.index') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium {{ request()->routeIs('asignaciones.*') ? 'underline' : '' }}">
                        Asignaciones
                    </a>
                    <a href="{{ route('reportes.asignados') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium {{ request()->routeIs('reportes.asignados') ? 'underline' : '' }}">
                        Reporte Asignados
                    </a>
                    <a href="{{ route('reportes.disponibles') }}"
                        class="text-gray-700 hover:text-blue-700 font-medium {{ request()->routeIs('reportes.disponibles') ? 'underline' : '' }}">
                        Reporte Disponibles
                    </a>
                </div>
            </div>

            <!-- Usuario -->
            <div class="hidden md:flex items-center space-x-4">
                <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-red-500 hover:text-red-700 font-medium" type="submit">
                        Cerrar Sesión
                    </button>
                </form>
            </div>

            <!-- Botón menú hamburguesa -->
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open }" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open }" class="hidden" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menú responsivo -->
    <div x-show="open" class="md:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('dashboard') ? 'bg-blue-100 font-semibold' : '' }}">Dashboard</a>
            <a href="{{ route('mobiliario.index') }}"
                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('mobiliario.*') ? 'bg-blue-100 font-semibold' : '' }}">Mobiliario</a>
            <a href="{{ route('dispositivos.index') }}"
                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('dispositivos.*') ? 'bg-blue-100 font-semibold' : '' }}">Dispositivos</a>
            <a href="{{ route('asignaciones.index') }}"
                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('asignaciones.*') ? 'bg-blue-100 font-semibold' : '' }}">Asignaciones</a>
            <a href="{{ route('reportes.asignados') }}"
                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('reportes.asignados') ? 'bg-blue-100 font-semibold' : '' }}">Reporte
                Asignados</a>
            <a href="{{ route('reportes.disponibles') }}"
                class="block px-4 py-2 text-gray-700 hover:bg-blue-50 {{ request()->routeIs('reportes.disponibles') ? 'bg-blue-100 font-semibold' : '' }}">Reporte
                Disponibles</a>
        </div>

        <div class="border-t border-gray-200">
            <div class="px-4 py-2 text-sm text-gray-600">
                {{ Auth::user()->name }}<br>
                {{ Auth::user()->email }}
            </div>
            <form method="POST" action="{{ route('logout') }}" class="px-4 py-2">
                @csrf
                <button type="submit" class="w-full text-left text-red-500 hover:text-red-700 text-sm">Cerrar
                    Sesión</button>
            </form>
        </div>
    </div>
</nav>
