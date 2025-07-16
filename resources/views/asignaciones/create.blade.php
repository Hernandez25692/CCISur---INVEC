<x-app-layout>
    <div class="py-10 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Nueva Asignación</h2>

        {{-- Mostrar mensaje de error si el bien ya está asignado --}}
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('asignaciones.store') }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            {{-- Tipo --}}
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo de Bien</label>
                <select name="tipo" id="tipo" required onchange="cargarOpciones()"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccionar --</option>
                    <option value="mobiliario" {{ isset($tipo) && $tipo == 'mobiliario' ? 'selected' : '' }}>Mobiliario
                    </option>
                    <option value="dispositivo" {{ isset($tipo) && $tipo == 'dispositivo' ? 'selected' : '' }}>
                        Dispositivo</option>
                </select>

            </div>

            {{-- Elemento --}}
            <div>
                <label for="id_referencia" class="block text-sm font-medium text-gray-700">Elemento a asignar</label>
                <select name="id_referencia" id="id_referencia" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Seleccione un tipo primero</option>
                </select>

                @if (isset($tipo) && isset($id_referencia))
                    <script>
                        window.addEventListener('DOMContentLoaded', () => {
                            const tipo = '{{ $tipo }}';
                            const idRef = '{{ $id_referencia }}';
                            document.getElementById('tipo').value = tipo;

                            fetch(`/api/obtener-items/${tipo}?id={{ $id_referencia ?? '' }}`)

                                .then(res => res.json())
                                .then(data => {
                                    const select = document.getElementById('id_referencia');
                                    select.innerHTML = '<option value="">-- Seleccione --</option>';
                                    data.forEach(item => {
                                        const selected = item.id == idRef ? 'selected' : '';
                                        select.innerHTML +=
                                            `<option value="${item.id}" ${selected}>${item.nombre}</option>`;
                                    });

                                    const item = data.find(i => i.id == idRef);
                                    document.getElementById('etiquetaMostrar').innerText = item?.etiqueta || 'Sin etiqueta';
                                });
                        });
                    </script>
                @endif

            </div>

            {{-- Etiqueta --}}
            <div class="mt-4">
                <label class="block text-sm text-gray-700 font-medium">Etiqueta de Inventario</label>
                <p id="etiquetaMostrar" class="text-blue-700 font-semibold mt-1">Seleccione un elemento...</p>
            </div>

            {{-- Colaborador --}}
            <div>
                <label for="empleado_id" class="block text-sm font-medium text-gray-700">Colaborador</label>
                <select name="empleado_id" id="empleado_id" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccionar empleado --</option>
                    @foreach ($empleados as $empleado)
                        <option value="{{ $empleado->id }}" data-gerencia="{{ $empleado->gerencia }}">
                            {{ $empleado->codigo }} - {{ $empleado->nombre_completo }}
                        </option>
                    @endforeach
                </select>

            </div>


            {{-- Área --}}
            <div>
                <label for="area" class="block text-sm font-medium text-gray-700">Área o Departamento</label>
                <input type="text" name="area" id="area" required readonly
                    class="mt-1 block w-full border-gray-300 bg-gray-100 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">

            </div>

            {{-- Observaciones --}}
            <div>
                <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                <textarea name="observaciones" id="observaciones" rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            {{-- Fecha --}}
            <div>
                <label for="fecha_entrega" class="block text-sm font-medium text-gray-700">Fecha de Entrega</label>
                <input type="date" name="fecha_entrega" id="fecha_entrega" value="{{ date('Y-m-d') }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            {{-- Entregado por --}}
            <div>
                <label for="entregado_por" class="block text-sm font-medium text-gray-700">Entregado por</label>
                <input type="text" name="entregado_por" id="entregado_por" value="{{ Auth::user()->name }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('asignaciones.index') }}" class="text-sm text-gray-500 hover:underline">←
                    Cancelar</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Guardar</button>
            </div>
        </form>
    </div>

    {{-- Script dinámico --}}
    <script>
        document.getElementById('empleado_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const gerencia = selectedOption.getAttribute('data-gerencia') || '';
            document.getElementById('area').value = gerencia;
        });
    </script>


    <script>
        let dataItems = [];

        const tipoInput = document.getElementById('tipo');
        const selectElemento = document.getElementById('id_referencia');
        const etiqueta = document.getElementById('etiquetaMostrar');

        tipoInput.addEventListener('change', function() {
            const tipo = this.value;
            etiqueta.innerText = 'Seleccione un elemento...';
            selectElemento.innerHTML = '<option value="">-- Cargando --</option>';

            fetch(`/api/obtener-items/${tipo}`)
                .then(res => res.json())
                .then(data => {
                    dataItems = data;
                    selectElemento.innerHTML = '<option value="">-- Seleccione --</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.text = item.nombre;
                        selectElemento.appendChild(option);
                    });

                    // Preseleccionar si venía en la URL
                    const idRef = "{{ $id_referencia ?? '' }}";
                    if (tipo === "{{ $tipo ?? '' }}" && idRef !== '') {
                        selectElemento.value = idRef;

                        const preItem = data.find(i => i.id == idRef);
                        etiqueta.innerText = preItem?.etiqueta || 'Sin etiqueta';
                    }
                });
        });

        selectElemento.addEventListener('change', function() {
            const item = dataItems.find(el => el.id == this.value);
            etiqueta.innerText = item && item.etiqueta ? item.etiqueta : 'Sin etiqueta';
        });

        // Si ya venían valores cargados, disparar evento automáticamente
        window.addEventListener('DOMContentLoaded', function() {
            const tipo = "{{ $tipo ?? '' }}";
            if (tipo) {
                tipoInput.value = tipo;
                tipoInput.dispatchEvent(new Event('change'));
            }
        });
    </script>

</x-app-layout>
