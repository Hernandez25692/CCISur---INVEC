<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-green-50 py-10 px-4">
        <div class="max-w-screen-xl mx-auto space-y-10">

            <!-- Encabezado -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-green-700 flex items-center justify-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Reporte de Bienes Disponibles
                </h2>
                <p class="text-gray-600">Listado completo de mobiliario y dispositivos electrónicos disponibles</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-10">
                <!-- Resumen -->
                <div class="lg:w-1/4 w-full">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-4 space-y-4">
                        <h3 class="font-semibold text-lg text-gray-800 mb-2">Resumen General</h3>
                        <div class="bg-green-50 p-4 rounded-lg shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Mobiliario Disponible</p>
                                <p class="text-xl font-bold text-green-700">{{ $mobiliarios->total() }}</p>
                            </div>
                            <i class="fa-solid fa-couch text-green-500 text-2xl"></i>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Dispositivos Disponibles</p>
                                <p class="text-xl font-bold text-blue-700">{{ $dispositivos->total() }}</p>
                            </div>
                            <i class="fa-solid fa-desktop text-blue-500 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="lg:w-3/4 w-full space-y-10">

                    <!-- Mobiliario -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                                <i class="fa-solid fa-chair"></i> Mobiliario Disponible
                            </h3>
                            <form method="GET" class="w-full md:w-1/2">
                                <input type="text" name="tipo_mobiliario" placeholder="Buscar por tipo..."
                                       value="{{ request('tipo_mobiliario') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm text-sm px-3 py-2 focus:ring-green-500 focus:border-green-500" />
                            </form>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Ubicación</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Estado</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($mobiliarios as $m)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $m->id }}</td>
                                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $m->nombre }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $m->tipo }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $m->ubicacion }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $m->estado }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-gray-500 py-6">No hay mobiliario disponible.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4">
                            {{ $mobiliarios->appends(request()->except('page'))->links() }}
                        </div>
                    </div>

                    <!-- Dispositivos -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                                <i class="fa-solid fa-laptop"></i> Dispositivos Electrónicos
                            </h3>
                            <form method="GET" class="w-full md:w-1/2">
                                <input type="text" name="tipo_dispositivo" placeholder="Buscar por tipo..."
                                       value="{{ request('tipo_dispositivo') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm text-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" />
                            </form>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Marca</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Ubicación</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Estado</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($dispositivos as $d)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ $d->id }}</td>
                                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $d->nombre }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $d->tipo }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $d->marca }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $d->ubicacion }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $d->estado }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-gray-500 py-6">No hay dispositivos disponibles.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4">
                            {{ $dispositivos->appends(request()->except('page'))->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
