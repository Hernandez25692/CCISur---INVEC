<x-app-layout>
    <div class="py-6 space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Encabezado principal --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-brand-navy">Inicio</a>
                    <span>/</span>
                    <span class="text-brand-navy font-semibold">Asignaciones</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-navy tracking-tight">Gestión de Asignaciones</h1>
                <p class="text-sm text-gray-500 mt-0.5">Control de bienes institucionales actualmente en uso y custodia</p>
            </div>
            <a href="{{ route('asignaciones.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-navy text-white rounded-lg font-semibold text-xs uppercase tracking-wider shadow-sm hover:bg-brand-navy-800 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                <i class="fas fa-plus text-xs text-brand-gold" aria-hidden="true"></i>
                Nueva Asignación
            </a>
        </div>

        {{-- Tarjetas de métricas rápidas (Inventario activo) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Activas en uso --}}
            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Activas en Uso</p>
                        <p class="text-2xl font-bold text-brand-navy mt-1">{{ number_format($totalActivas) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-hand-holding text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-emerald-700 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Bienes bajo custodia</span>
                </div>
            </div>

            {{-- Dispositivos Activos --}}
            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Dispositivos Activos</p>
                        <p class="text-2xl font-bold text-brand-navy mt-1">{{ number_format($totalDispositivos) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-laptop text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Equipos de computación y red</p>
            </div>

            {{-- Mobiliario Activo --}}
            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Mobiliario Activo</p>
                        <p class="text-2xl font-bold text-brand-navy mt-1">{{ number_format($totalMobiliario) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fas fa-chair text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Muebles y estaciones de trabajo</p>
            </div>

            {{-- Custodios / Colaboradores --}}
            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Colaboradores con Bienes</p>
                        <p class="text-2xl font-bold text-brand-navy mt-1">{{ number_format($totalCustodios) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                        <i class="fas fa-users text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Personal con actas activas</p>
            </div>
        </div>

        {{-- Formulario de búsqueda y filtros --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form method="GET" action="{{ route('asignaciones.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                {{-- Buscador general --}}
                <div class="sm:col-span-2 lg:col-span-4">
                    <label for="buscar" class="block text-xs font-semibold text-gray-600 mb-1">Buscar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs" aria-hidden="true"></i>
                        </div>
                        <input type="text" name="buscar" id="buscar"
                            value="{{ $buscar }}"
                            placeholder="Colaborador, DNI, etiqueta o bien..."
                            class="block w-full pl-9 pr-3 py-2 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs placeholder-gray-400">
                    </div>
                </div>

                {{-- Tipo de Bien --}}
                <div class="lg:col-span-2">
                    <label for="tipo" class="block text-xs font-semibold text-gray-600 mb-1">Tipo de Bien</label>
                    <select name="tipo" id="tipo"
                        class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                        <option value="">Todos los tipos</option>
                        <option value="dispositivo" {{ $tipo === 'dispositivo' ? 'selected' : '' }}>Dispositivos</option>
                        <option value="mobiliario" {{ $tipo === 'mobiliario' ? 'selected' : '' }}>Mobiliario</option>
                    </select>
                </div>

                {{-- Fecha Inicio --}}
                <div class="lg:col-span-2">
                    <label for="fecha_inicio" class="block text-xs font-semibold text-gray-600 mb-1">Desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}"
                        class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                </div>

                {{-- Fecha Fin --}}
                <div class="lg:col-span-2">
                    <label for="fecha_fin" class="block text-xs font-semibold text-gray-600 mb-1">Hasta</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}"
                        class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                </div>

                {{-- Botones de acción --}}
                <div class="sm:col-span-2 lg:col-span-2 flex items-center gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-brand-navy text-white text-xs font-semibold rounded-lg shadow-xs hover:bg-brand-navy-800 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                        <i class="fas fa-filter text-[11px]" aria-hidden="true"></i>
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['buscar', 'tipo', 'fecha_inicio', 'fecha_fin']))
                        <a href="{{ route('asignaciones.index') }}"
                            class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-gray-600 hover:text-brand-navy hover:bg-gray-50 text-xs font-semibold rounded-lg shadow-xs transition"
                            title="Limpiar filtros">
                            <i class="fas fa-undo text-xs" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabla de asignaciones activas --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 text-center w-12">#</th>
                            <th scope="col" class="px-4 py-3.5">Destinatario</th>
                            <th scope="col" class="px-4 py-3.5">Tipo</th>
                            <th scope="col" class="px-4 py-3.5">Bien Asignado</th>
                            <th scope="col" class="px-4 py-3.5">Etiqueta</th>
                            <th scope="col" class="px-4 py-3.5">Área / Ubicación</th>
                            <th scope="col" class="px-4 py-3.5">Fecha Entrega</th>
                            <th scope="col" class="px-4 py-3.5 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($asignaciones as $item)
                            @php
                                $referencia = $item->tipo === 'mobiliario' ? $item->mobiliario : $item->dispositivo;
                            @endphp
                            <tr class="hover:bg-brand-navy-50/30 transition">
                                {{-- ID --}}
                                <td class="px-4 py-3 text-center font-mono text-gray-500 font-medium">
                                    {{ $item->id }}
                                </td>

                                {{-- Destinatario --}}
                                <td class="px-4 py-3">
                                    @if ($item->es_area_comun)
                                        <div class="flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-lg bg-amber-50 text-brand-gold-700 flex items-center justify-center shrink-0">
                                                <i class="fas fa-building text-xs" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <p class="font-bold text-gray-900">Área Común</p>
                                                <p class="text-[11px] text-gray-500">{{ $item->area }}</p>
                                            </div>
                                        </div>
                                    @elseif ($item->empleado)
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($item->empleado->nombre_completo, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('empleados.historial', $item->empleado_id) }}"
                                                    class="font-semibold text-brand-navy hover:underline">
                                                    {{ $item->empleado->nombre_completo }}
                                                </a>
                                                <p class="text-[11px] text-gray-500 font-mono">
                                                    {{ $item->empleado->codigo }} &bull; {{ $item->empleado->identidad_formateada ?? $item->empleado->identidad ?? 'Sin DNI' }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">No asignado</span>
                                    @endif
                                </td>

                                {{-- Tipo --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($item->tipo === 'dispositivo')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                            <i class="fas fa-laptop text-[10px]" aria-hidden="true"></i>
                                            Dispositivo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                            <i class="fas fa-chair text-[10px]" aria-hidden="true"></i>
                                            Mobiliario
                                        </span>
                                    @endif
                                </td>

                                {{-- Elemento --}}
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $referencia->nombre ?? 'N/A' }}</div>
                                    @if ($item->tipo === 'dispositivo' && !empty($referencia?->n_serie))
                                        <div class="text-[11px] text-gray-500 font-mono">S/N: {{ $referencia->n_serie }}</div>
                                    @endif
                                </td>

                                {{-- Etiqueta --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-mono font-semibold text-brand-navy bg-gray-100 px-2 py-0.5 rounded border border-gray-200">
                                        {{ $referencia->etiqueta ?? '---' }}
                                    </span>
                                </td>

                                {{-- Área --}}
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $item->area }}
                                </td>

                                {{-- Fecha --}}
                                <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                    {{ $item->fecha_entrega?->format('d/m/Y') ?? '---' }}
                                </td>

                                {{-- Acciones --}}
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        {{-- Botón rápido: Devolver Bien --}}
                                        <a href="{{ route('devoluciones.create', ['asignacion_id' => $item->id]) }}"
                                            class="inline-flex items-center justify-center px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-300 rounded hover:bg-emerald-600 hover:text-white text-xs font-semibold shadow-2xs transition"
                                            title="Registrar Devolución de este bien">
                                            <i class="fas fa-undo text-xs mr-1" aria-hidden="true"></i>
                                            Devolver
                                        </a>

                                        {{-- Ver Acta --}}
                                        <a href="{{ route('asignaciones.show', $item->id) }}"
                                            class="w-7 h-7 rounded inline-flex items-center justify-center text-gray-500 hover:text-brand-navy hover:bg-gray-100 transition"
                                            title="Ver acta de entrega">
                                            <i class="fas fa-eye text-xs" aria-hidden="true"></i>
                                        </a>

                                        {{-- Descargar PDF --}}
                                        <a href="{{ route('asignaciones.pdf', $item->id) }}"
                                            target="_blank"
                                            class="w-7 h-7 rounded inline-flex items-center justify-center text-purple-600 hover:text-purple-900 hover:bg-purple-50 transition"
                                            title="Descargar Acta en PDF">
                                            <i class="fas fa-file-pdf text-xs" aria-hidden="true"></i>
                                        </a>

                                        {{-- Anular Asignación con contraseña (data-attributes seguros contra XSS) --}}
                                        <button type="button"
                                            class="w-7 h-7 rounded inline-flex items-center justify-center text-red-600 hover:text-red-900 hover:bg-red-50 transition btn-anular"
                                            title="Anular asignación por error"
                                            data-id="{{ $item->id }}"
                                            data-nombre="{{ $referencia->nombre ?? 'Bien' }}"
                                            data-etiqueta="{{ $referencia->etiqueta ?? 'Sin etiqueta' }}">
                                            <i class="fas fa-trash-alt text-xs" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                        <i class="fas fa-inbox text-xl" aria-hidden="true"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">No se encontraron asignaciones activas</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $buscar !== '' ? 'Pruebe ajustando los términos de búsqueda o filtros.' : 'No hay asignaciones activas registradas en este momento.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($asignaciones->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $asignaciones->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Formulario oculto para anular asignación con contraseña --}}
    <form id="form-anular-asignacion" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="password" id="input-password-anulacion">
    </form>

    {{-- Script seguro contra XSS utilizando data attributes y escape nativo --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str || '';
                return div.innerHTML;
            }

            document.querySelectorAll('.btn-anular').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const nombre = escapeHtml(button.dataset.nombre);
                    const etiqueta = escapeHtml(button.dataset.etiqueta);

                    Swal.fire({
                        title: '¿Anular esta Asignación?',
                        html: `
                            <p class="text-sm text-gray-600 mb-3">
                                Esta acción debe realizarse <strong>únicamente en caso de equivocación</strong> al registrar.
                                El bien <strong>${nombre}</strong> (${etiqueta}) será liberado inmediatamente y quedará disponible para ser asignado.
                            </p>
                            <div class="text-left mt-3">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    Ingrese su contraseña de usuario para autorizar:
                                </label>
                            </div>
                        `,
                        input: 'password',
                        inputPlaceholder: 'Su contraseña actual de sesión...',
                        inputAttributes: {
                            autocapitalize: 'off',
                            autocorrect: 'off',
                            required: 'true'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Sí, Anular y Liberar Bien',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#DC2626',
                        cancelButtonColor: '#6B7280',
                        focusConfirm: false,
                        preConfirm: (password) => {
                            if (!password) {
                                Swal.showValidationMessage('Debe ingresar su contraseña para proceder.');
                            }
                            return password;
                        },
                        customClass: {
                            popup: 'rounded-xl text-left',
                            confirmButton: 'rounded-lg text-xs font-semibold px-4 py-2',
                            cancelButton: 'rounded-lg text-xs font-semibold px-4 py-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            const form = document.getElementById('form-anular-asignacion');
                            form.action = `/asignaciones/${id}`;
                            document.getElementById('input-password-anulacion').value = result.value;
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
