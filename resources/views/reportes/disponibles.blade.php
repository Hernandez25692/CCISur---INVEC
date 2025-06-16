<x-app-layout>
    <div class="py-10 max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold text-green-700 mb-6">Reporte de Bienes Disponibles</h2>
        <!-- Métricas rápidas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-blue-100 border-l-4 border-blue-500 shadow p-5 rounded flex flex-col items-center">
                <div class="text-blue-700 font-semibold text-sm mb-2">Total Mobiliario</div>
                <div class="text-3xl font-bold text-blue-800">{{ \App\Models\Mobiliario::count() }}</div>
            </div>
            <div class="bg-green-100 border-l-4 border-green-500 shadow p-5 rounded flex flex-col items-center">
                <div class="text-green-700 font-semibold text-sm mb-2">Total Dispositivos</div>
                <div class="text-3xl font-bold text-green-800">{{ \App\Models\Dispositivo::count() }}</div>
            </div>
            <div class="bg-amber-100 border-l-4 border-amber-500 shadow p-5 rounded flex flex-col items-center">
                <div class="text-amber-700 font-semibold text-sm mb-2">Total Asignaciones</div>
                <div class="text-3xl font-bold text-amber-800">{{ \App\Models\Asignacion::count() }}</div>
            </div>
            <div class="bg-gradient-to-r from-indigo-500 to-blue-400 border-l-4 border-purple-700 shadow p-5 rounded flex flex-col items-center">
                <div class="text-gray-100 font-bold text-sm mb-2 flex items-center justify-center">
                    <i class="fas fa-layer-group mr-2"></i> Total General
                </div>
                <div class="text-4xl font-extrabold text-white drop-shadow text-center">
                    {{ \App\Models\Mobiliario::count() + \App\Models\Dispositivo::count() }}
                </div>
            </div>
        </div>
        <!-- Mobiliario disponible -->
        <div class="mb-10">
            <h3 class="text-xl font-semibold text-gray-800 mb-3">Mobiliario</h3>
            <div class="bg-white shadow rounded p-4">
                <table class="w-full table-auto text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-700">
                            <th class="p-2">#</th>
                            <th class="p-2">Nombre</th>
                            <th class="p-2">Tipo</th>
                            <th class="p-2">Ubicación</th>
                            <th class="p-2">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mobiliarios as $m)
                            <tr class="border-b">
                                <td class="p-2">{{ $m->id }}</td>
                                <td class="p-2">{{ $m->nombre }}</td>
                                <td class="p-2">{{ $m->tipo }}</td>
                                <td class="p-2">{{ $m->ubicacion }}</td>
                                <td class="p-2">{{ ucfirst($m->estado) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-gray-500">Todo el mobiliario está
                                    asignado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dispositivos disponibles -->
        <div>
            <h3 class="text-xl font-semibold text-gray-800 mb-3">Dispositivos Electrónicos</h3>
            <div class="bg-white shadow rounded p-4">
                <table class="w-full table-auto text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-left text-gray-700">
                            <th class="p-2">#</th>
                            <th class="p-2">Nombre</th>
                            <th class="p-2">Tipo</th>
                            <th class="p-2">Marca</th>
                            <th class="p-2">Ubicación</th>
                            <th class="p-2">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dispositivos as $d)
                            <tr class="border-b">
                                <td class="p-2">{{ $d->id }}</td>
                                <td class="p-2">{{ $d->nombre }}</td>
                                <td class="p-2">{{ $d->tipo }}</td>
                                <td class="p-2">{{ $d->marca }}</td>
                                <td class="p-2">{{ $d->ubicacion }}</td>
                                <td class="p-2">{{ ucfirst($d->estado) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500">Todos los dispositivos están
                                    asignados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
