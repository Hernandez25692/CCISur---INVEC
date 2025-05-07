<x-app-layout>
    <div class="py-10 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Nueva Asignación</h2>

        <form action="{{ route('asignaciones.store') }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <!-- Tipo -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo de Bien</label>
                <select name="tipo" id="tipo" required onchange="cargarOpciones()"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccionar --</option>
                    <option value="mobiliario">Mobiliario</option>
                    <option value="dispositivo">Dispositivo</option>
                </select>
            </div>

            <!-- Selección de item -->
            <div>
                <label for="id_referencia" class="block text-sm font-medium text-gray-700">Elemento a asignar</label>
                <select name="id_referencia" id="id_referencia" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Seleccione un tipo primero</option>
                </select>
            </div>
            <div class="mt-4">
                <label class="block text-sm text-gray-700 font-medium">Etiqueta de Inventario</label>
                <p id="etiquetaMostrar" class="text-blue-700 font-semibold mt-1">Seleccione un elemento...</p>
            </div>

            <!-- Colaborador -->
            <div>
                <label for="colaborador" class="block text-sm font-medium text-gray-700">Nombre del Colaborador</label>
                <input type="text" name="colaborador" id="colaborador" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Área -->
            <div>
                <label for="area" class="block text-sm font-medium text-gray-700">Área o Departamento</label>
                <input type="text" name="area" id="area" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Observaciones -->
            <div>
                <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                <textarea name="observaciones" id="observaciones" rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- Fecha -->
            <div>
                <label for="fecha_entrega" class="block text-sm font-medium text-gray-700">Fecha de Entrega</label>
                <input type="date" name="fecha_entrega" id="fecha_entrega" value="{{ date('Y-m-d') }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Entregado por -->
            <div>
                <label for="entregado_por" class="block text-sm font-medium text-gray-700">Entregado por</label>
                <input type="text" name="entregado_por" id="entregado_por" value="{{ Auth::user()->name }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('asignaciones.index') }}" class="text-sm text-gray-500 hover:underline">← Cancelar</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Guardar</button>
            </div>
        </form>
    </div>

    <!-- Script dinámico -->
    <script>
        function cargarOpciones() {
            const tipo = document.getElementById('tipo').value;
            const select = document.getElementById('id_referencia');
            select.innerHTML = '<option value="">Cargando...</option>';

            fetch('/api/obtener-items/' + tipo)
                .then(res => res.json())
                .then(data => {
                    select.innerHTML = '<option value="">-- Seleccionar --</option>';
                    data.forEach(item => {
                        select.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                    });
                });
        }
    </script>

    @push('scripts')
        <script>
            let dataItems = [];

            // Cuando se cambia el tipo
            document.getElementById('tipo').addEventListener('change', function() {
                const tipo = this.value;
                const select = document.getElementById('id_referencia');
                const etiqueta = document.getElementById('etiquetaMostrar');

                fetch(`/api/obtener-items/${tipo}`)
                    .then(res => res.json())
                    .then(data => {
                        dataItems = data;
                        select.innerHTML = '<option value="">-- Seleccione --</option>';
                        data.forEach(item => {
                            select.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
                        });
                        etiqueta.innerText = 'Seleccione un elemento...';
                    });
            });

            // Cuando se selecciona un ítem
            document.getElementById('id_referencia').addEventListener('change', function() {
                const id = parseInt(this.value);
                const item = dataItems.find(el => el.id === id);
                const etiqueta = document.getElementById('etiquetaMostrar');

                etiqueta.innerText = item && item.etiqueta ? item.etiqueta : 'Sin etiqueta';
            });
        </script>
    @endpush

</x-app-layout>
