<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Empleado</th>
            <th>Tipo</th>
            <th>Elemento</th>
            <th>Etiqueta</th>
            <th>Área</th>
            <th>Fecha</th>
            <th>Entregado Por</th>
            <th>Observaciones</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($asignaciones as $a)
            @php
                $ref = $a->tipo === 'mobiliario'
                    ? \App\Models\Mobiliario::find($a->id_referencia)
                    : \App\Models\Dispositivo::find($a->id_referencia);
            @endphp
            <tr>
                <td>{{ $a->id }}</td>
                <td>{{ $a->empleado->nombre_completo ?? 'N/A' }}</td>
                <td>{{ ucfirst($a->tipo) }}</td>
                <td>{{ $ref->nombre ?? 'N/A' }}</td>
                <td>{{ $ref->etiqueta ?? '---' }}</td>
                <td>{{ $a->area }}</td>
                <td>{{ $a->fecha_entrega }}</td>
                <td>{{ $a->entregado_por }}</td>
                <td>{{ $a->observaciones }}</td>
                <td>{{ $a->devolucion ? 'Devuelto' : 'Activo' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
