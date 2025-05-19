<?php
// Script de prueba para verificar el estado del calendario y la consistencia de datos

// Cargar el entorno de Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CalendarDay;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n=== DIAGNÓSTICO DEL SISTEMA DE RESERVACIONES ===\n\n";

// 1. Verificar estructura de la tabla
echo "1. ESTRUCTURA DE LA TABLA CALENDAR_DAYS:\n";
$columns = Schema::getColumnListing('calendar_days');
echo "Columnas: " . implode(', ', $columns) . "\n\n";

// Verificar si existe la columna available_slots
if (in_array('available_slots', $columns)) {
    echo "✅ Columna 'available_slots' existe en la tabla.\n";
} else {
    echo "❌ ERROR: Columna 'available_slots' NO existe en la tabla. Esto podría causar errores.\n";
}

// 2. Verificar días en el calendario
echo "\n2. DATOS DEL CALENDARIO:\n";
$calendarDays = CalendarDay::take(5)->get();
echo "Primeros 5 registros:\n";

foreach ($calendarDays as $day) {
    echo "- ID: {$day->id}, Fecha: {$day->date}, Estado: {$day->availability_status}\n";
    
    // Verificar si hay inconsistencia entre booked_slots y citas reales
    $actualBookedCount = Appointment::where('calendar_day_id', $day->id)
                                  ->where('status', 'confirmed')
                                  ->count();
    
    if ($day->booked_slots != $actualBookedCount) {
        echo "  ❌ INCONSISTENCIA: booked_slots = {$day->booked_slots}, conteo real = {$actualBookedCount}\n";
    }
    
    // Verificar contenido de available_slots si existe la columna
    if (property_exists($day, 'available_slots') || isset($day->available_slots)) {
        $slotsValue = $day->available_slots;
        echo "  - Available Slots: " . (is_array($slotsValue) ? json_encode($slotsValue) : $slotsValue) . "\n";
    } else {
        echo "  ⚠️ No se puede acceder a 'available_slots' para este día.\n";
    }
}

// 3. Verificar estados de disponibilidad
echo "\n3. DISTRIBUCIÓN DE ESTADOS DE DISPONIBILIDAD:\n";
$statuses = DB::table('calendar_days')
             ->select('availability_status', DB::raw('count(*) as total'))
             ->groupBy('availability_status')
             ->get();

foreach ($statuses as $status) {
    echo "- Estado '{$status->availability_status}': {$status->total} días\n";
}

// 4. Verificar si hay días con estado de override manual
echo "\n4. DÍAS CON OVERRIDE MANUAL:\n";
$manualOverrideDays = CalendarDay::where('manual_override', true)->take(5)->get();

if ($manualOverrideDays->isEmpty()) {
    echo "No hay días con override manual configurado.\n";
} else {
    foreach ($manualOverrideDays as $day) {
        echo "- ID: {$day->id}, Fecha: {$day->date}, Estado: {$day->availability_status}\n";
    }
}

// 5. Verificar integridad referencial
echo "\n5. VERIFICACIÓN DE INTEGRIDAD REFERENCIAL:\n";
$orphanedAppointments = Appointment::whereNotIn('calendar_day_id', function($query) {
    $query->select('id')->from('calendar_days');
})->count();

if ($orphanedAppointments > 0) {
    echo "❌ ERROR: Hay {$orphanedAppointments} citas huérfanas (sin día de calendario asociado).\n";
} else {
    echo "✅ No hay citas huérfanas.\n";
}

// 6. Verificar scripts y dependencias
echo "\n6. VERIFICACIÓN DE DEPENDENCIAS JS:\n";
$indexFile = file_get_contents(__DIR__.'/resources/views/appointments/create.blade.php');
if (strpos($indexFile, 'fullcalendar') !== false) {
    echo "✅ Librería FullCalendar está incluida en la vista.\n";
} else {
    echo "❌ ERROR: No se encontró la librería FullCalendar en la vista.\n";
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";

// Posible solución para la migración faltante
echo "\n=== SOLUCIÓN PROPUESTA ===\n";
echo "Si la columna 'available_slots' no existe, es necesario crear una nueva migración:\n";
echo "php artisan make:migration add_available_slots_to_calendar_days --table=calendar_days\n";
echo "Y completar la migración con:\n";
echo "\$table->json('available_slots')->nullable();\n\n";
echo "Y ejecutar: php artisan migrate\n";
