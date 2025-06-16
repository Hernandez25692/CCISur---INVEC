<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Empleado</th>
            <th>Tipo</th>
            <th>Elemento</th>
            <th>Área</th>
            <th>Fecha Asignación</th>
            <th>Estado</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        @php $i = 1; @endphp
        @foreach ($asignaciones as $a)
            @php
                $ref =
                    $a->tipo === 'mobiliario'
                        ? \App\Models\Mobiliario::find($a->id_referencia)
                        : \App\Models\Dispositivo::find($a->id_referencia);
            @endphp
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $a->empleado->nombre_completo ?? 'N/A' }}</td>
                <td>{{ $a->tipo }}</td>
                <td>{{ $ref->nombre ?? 'N/A' }}</td>
                <td>{{ $a->area }}</td>
                <td>{{ $a->fecha_entrega }}</td>
                <td>{{ $a->devolucion ? 'Devuelto' : 'Activo' }}</td>
                <td>{{ $a->observaciones ?: '---' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
