<table>
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Nombre</th>
            <th>Etiqueta</th>
            <th>Ubicación</th>
            <th>Estado</th>
            <th>Disponibilidad</th>
            <th>Fecha Registro</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mobiliarios as $m)
            <tr>
                <td>Mobiliario</td>
                <td>{{ $m->nombre }}</td>
                <td>{{ $m->etiqueta }}</td>
                <td>{{ $m->ubicacion }}</td>
                <td>{{ $m->estado }}</td>
                <td>{{ $m->disponibilidad }}</td>
                <td>{{ $m->fecha_registro }}</td>
            </tr>
        @endforeach
        @foreach($dispositivos as $d)
            <tr>
                <td>Dispositivo</td>
                <td>{{ $d->nombre }}</td>
                <td>{{ $d->etiqueta }}</td>
                <td>{{ $d->ubicacion }}</td>
                <td>{{ $d->estado }}</td>
                <td>{{ $d->disponibilidad }}</td>
                <td>{{ $d->fecha_registro }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
