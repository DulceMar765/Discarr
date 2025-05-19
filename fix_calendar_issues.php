<?php
// Script para reparar problemas del calendario de reservaciones

// Cargar el entorno de Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CalendarDay;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n=== REPARACIÓN DEL SISTEMA DE RESERVACIONES ===\n\n";

// 1. Verificar y reparar problemas de acceso a available_slots
echo "1. REPARANDO ESTRUCTURA Y DATOS DE AVAILABLE_SLOTS:\n";

// Primero comprobamos si la columna está definida correctamente
if (Schema::hasColumn('calendar_days', 'available_slots')) {
    echo "✅ La columna 'available_slots' existe.\n";
    
    // Verificar el tipo de la columna y corregirlo si es necesario
    try {
        DB::statement('ALTER TABLE calendar_days MODIFY available_slots JSON NULL');
        echo "✅ Se ha asegurado que la columna available_slots es de tipo JSON.\n";
    } catch (Exception $e) {
        echo "⚠️ No se pudo modificar la columna: " . $e->getMessage() . "\n";
    }
    
    // Reparar registros que tengan NULL o formatos incorrectos en available_slots
    $emptyDays = CalendarDay::whereNull('available_slots')->orWhere('available_slots', '')->get();
    echo "Encontrados {$emptyDays->count()} días con available_slots NULL o vacío.\n";
    
    foreach ($emptyDays as $day) {
        // Asignar un array vacío en formato JSON
        $day->available_slots = json_encode([]);
        $day->save();
    }
    echo "✅ Se han reparado los días con valores NULL o vacíos.\n";
} else {
    echo "❌ La columna 'available_slots' no existe. Ejecute la migración sugerida.\n";
}

// 2. Recalcular estados de disponibilidad para todos los días
echo "\n2. RECALCULANDO ESTADOS DE DISPONIBILIDAD:\n";

// Función para calcular estado basado en slots ocupados/total
function calculateAvailabilityStatus($bookedSlots, $totalSlots, $availableSlots) {
    // Si no hay slots disponibles pero debería haberlos (no es fin de semana/festivo)
    if (empty($availableSlots) || count($availableSlots) == 0) {
        return 'red'; // Sin disponibilidad
    }
    
    if ($bookedSlots == 0) {
        return 'green'; // Totalmente disponible
    } elseif ($bookedSlots < $totalSlots * 0.5) {
        return 'yellow'; // Parcialmente disponible
    } elseif ($bookedSlots < $totalSlots) {
        return 'orange'; // Casi lleno
    } else {
        return 'red'; // Lleno
    }
}

$calendarDays = CalendarDay::all();
$updated = 0;
$unchanged = 0;

foreach ($calendarDays as $day) {
    // Si hay override manual, no cambiar
    if ($day->manual_override) {
        echo "- ID: {$day->id}, Fecha: {$day->date} - Override manual (no se modifica).\n";
        $unchanged++;
        continue;
    }
    
    // Contar reservaciones confirmadas
    $bookedCount = Appointment::where('calendar_day_id', $day->id)
                            ->where('status', 'confirmed')
                            ->count();
                            
    // Actualizar booked_slots con valor real
    if ($day->booked_slots != $bookedCount) {
        $day->booked_slots = $bookedCount;
        echo "  ✓ Actualizado booked_slots de {$day->date} a {$bookedCount}.\n";
    }
    
    // Obtener slots disponibles
    $availableSlots = json_decode($day->available_slots ?? '[]', true);
    
    // Calcular nuevo estado
    $newStatus = calculateAvailabilityStatus($day->booked_slots, $day->total_slots, $availableSlots);
    
    // Si el estado es diferente, actualizar
    if ($day->availability_status != $newStatus) {
        echo "- ID: {$day->id}, Fecha: {$day->date}: Cambiando estado de '{$day->availability_status}' a '{$newStatus}'.\n";
        $day->availability_status = $newStatus;
        $updated++;
    } else {
        $unchanged++;
    }
    
    // Guardar cambios
    $day->save();
}

echo "✅ Recálculo completo: {$updated} días actualizados, {$unchanged} sin cambios.\n";

// 3. Verificar días con override manual
echo "\n3. VERIFICANDO DÍAS CON OVERRIDE MANUAL:\n";
$manualDays = CalendarDay::where('manual_override', true)->get();

foreach ($manualDays as $day) {
    echo "- ID: {$day->id}, Fecha: {$day->date}, Estado actual: {$day->availability_status}\n";
    
    // Verificar si el día tiene un estado que no es green/yellow/orange/red/black
    if (!in_array($day->availability_status, ['green', 'yellow', 'orange', 'red', 'black'])) {
        echo "  ⚠️ Estado inválido. Corrigiendo a 'green'.\n";
        $day->availability_status = 'green';
        $day->save();
    }
}

// 4. Forzar regeneración de horarios disponibles donde sea necesario
echo "\n4. REGENERANDO HORARIOS DISPONIBLES PREDETERMINADOS:\n";

// Función para generar horarios por defecto
function generateDefaultTimeSlots() {
    return [
        '09:00', '10:00', '11:00', '12:00', 
        '13:00', '14:00', '15:00', '16:00'
    ];
}

$daysWithoutSlots = CalendarDay::whereRaw("JSON_LENGTH(IFNULL(available_slots, '[]')) = 0")
                              ->where('availability_status', '!=', 'red')
                              ->where('availability_status', '!=', 'black')
                              ->where('manual_override', false)
                              ->get();

echo "Encontrados {$daysWithoutSlots->count()} días sin slots disponibles que deberían tenerlos.\n";

foreach ($daysWithoutSlots as $day) {
    // Regenerar horarios predeterminados
    $defaultSlots = generateDefaultTimeSlots();
    
    // Obtener reservaciones confirmadas para este día
    $bookedSlots = Appointment::where('calendar_day_id', $day->id)
                            ->where('status', 'confirmed')
                            ->pluck('time_slot')
                            ->toArray();
    
    // Quitar los horarios ya reservados
    $availableSlots = array_values(array_diff($defaultSlots, $bookedSlots));
    
    // Guardar los slots disponibles
    $day->available_slots = json_encode($availableSlots);
    $day->save();
    
    echo "- ID: {$day->id}, Fecha: {$day->date}: Regenerados " . count($availableSlots) . " slots disponibles.\n";
}

echo "\n=== REPARACIÓN COMPLETADA ===\n";
echo "Por favor, accede nuevamente al calendario para verificar los cambios.\n";
echo "Si persisten los problemas, asegúrate de limpiar la caché del navegador.\n";
