{{ $title ?? 'Estado del Proyecto' }}: {{ $project->name }}

{{ $message ?? 'Aquí tienes la información actualizada del proyecto.' }}

=== DETALLES DEL PROYECTO ===

Nombre: {{ $project->name }}
Descripción: {{ $project->description ?? 'No disponible' }}
Estado: {{ ucfirst(str_replace('_', ' ', $project->status ?? 'pendiente')) }}
Progreso: {{ $progreso ?? 0 }}%

=== FECHAS ===

Fecha de inicio: {{ $project->start_date ? $project->start_date->format('d/m/Y') : 'No definida' }}
Fecha estimada de finalización: {{ $project->end_date ? $project->end_date->format('d/m/Y') : 'No definida' }}
Días trabajados: {{ $diasTrabajados ?? 0 }}

=== RECURSOS ===

Horas totales: {{ $horasTotales ?? 0 }}
Costo de materiales: ${{ number_format($costoMateriales ?? 0, 2) }}
Presupuesto: ${{ number_format($project->budget ?? 0, 2) }}

=== ACCESO AL PROYECTO ===

Puedes ver el estado actualizado de tu proyecto en cualquier momento visitando:
{{ $url ?? route('project.status', ['token' => $project->token]) }}

{{ $additional_info ?? '' }}

--
Este correo fue enviado por {{ $company_name ?? 'Discarr' }}.
Por favor no respondas a este correo, es un envío automático.
