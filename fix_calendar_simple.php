<?php
// Script simplificado para reparar problemas del calendario de reservaciones

// Cargar el entorno de Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CalendarDay;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== REPARACIÓN DEL SISTEMA DE RESERVACIONES ===\n\n";

// Verificar y actualizar la columna available_slots
if (Schema::hasColumn('calendar_days', 'available_slots')) {
    echo "La columna 'available_slots' existe.\n";
    
    // Corregir registros con valores nulos
    $emptyDays = CalendarDay::whereNull('available_slots')->orWhere('available_slots', '')->get();
    echo "Encontrados {$emptyDays->count()} días con available_slots NULL o vacío.\n";
    
    foreach ($emptyDays as $day) {
        $day->available_slots = json_encode([]);
        $day->save();
    }
} 

// Recalcular estados de disponibilidad
echo "\nRecalculando estados de disponibilidad...\n";

// Función para calcular el estado
function calculateStatus($booked, $total, $slots) {
    if (empty($slots) || count($slots) == 0) {
        return 'red';
    }
    
    if ($booked == 0) {
        return 'green';
    } elseif ($booked < $total * 0.5) {
        return 'yellow';
    } elseif ($booked < $total) {
        return 'orange';
    } else {
        return 'red';
    }
}

// Actualizar todos los días del calendario
$updated = 0;
$calendarDays = CalendarDay::all();

foreach ($calendarDays as $day) {
    // Omitir días con override manual
    if ($day->manual_override) {
        continue;
    }
    
    // Contar reservaciones confirmadas
    $bookedCount = Appointment::where('calendar_day_id', $day->id)
                             ->where('status', 'confirmed')
                             ->count();
    
    // Actualizar booked_slots
    if ($day->booked_slots != $bookedCount) {
        $day->booked_slots = $bookedCount;
    }
    
    // Obtener slots disponibles
    $availableSlots = json_decode($day->available_slots ?? '[]', true);
    
    // Calcular nuevo estado
    $newStatus = calculateStatus($day->booked_slots, $day->total_slots, $availableSlots);
    
    // Actualizar si es diferente
    if ($day->availability_status != $newStatus) {
        echo "Día {$day->date}: Cambiando estado de '{$day->availability_status}' a '{$newStatus}'.\n";
        $day->availability_status = $newStatus;
        $updated++;
    }
    
    // Generar horarios predeterminados si no tiene
    if (empty($availableSlots) && !in_array($newStatus, ['red', 'black'])) {
        $defaultSlots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
        
        // Quitar los horarios ya reservados
        $bookedSlots = Appointment::where('calendar_day_id', $day->id)
                                ->where('status', 'confirmed')
                                ->pluck('time_slot')
                                ->toArray();
        
        $availableSlots = array_values(array_diff($defaultSlots, $bookedSlots));
        $day->available_slots = json_encode($availableSlots);
        echo "Día {$day->date}: Regenerados " . count($availableSlots) . " slots disponibles.\n";
    }
    
    // Guardar cambios
    $day->save();
}

echo "\nActualización completada: {$updated} días actualizados.\n";
echo "=== REPARACIÓN COMPLETADA ===\n";
echo "Por favor, accede nuevamente al calendario para verificar los cambios.\n";
