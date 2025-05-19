<?php
// Script mejorado para reparar problemas del calendario de reservaciones

// Cargar el entorno de Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CalendarDay;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

echo "=== INICIANDO REPARACIÓN DEL SISTEMA DE RESERVACIONES ===\n\n";

// Función para obtener los slots disponibles de forma segura
function getAvailableSlots($day) {
    $slots = [];
    
    if (is_string($day->available_slots)) {
        try {
            $decodedSlots = json_decode($day->available_slots, true);
            if (is_array($decodedSlots)) {
                $slots = $decodedSlots;
            }
        } catch (\Exception $e) {
            // Si hay un error, devolver array vacío
        }
    } elseif (is_array($day->available_slots)) {
        $slots = $day->available_slots;
    }
    
    return $slots;
}

// Función para calcular estado basado en disponibilidad
function calculateStatus($booked, $total, $slots) {
    if (empty($slots)) {
        return 'red'; // Sin slots disponibles
    }
    
    if ($booked == 0) {
        return 'green'; // Disponible
    } elseif ($booked < $total * 0.5) {
        return 'yellow'; // Parcialmente ocupado
    } elseif ($booked < $total) {
        return 'orange'; // Casi lleno
    } else {
        return 'red'; // Lleno
    }
}

// Generar horarios predeterminados (9:00 AM a 4:00 PM)
function getDefaultTimeSlots() {
    return ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
}

// Recorrer todos los días del calendario
$totalDays = CalendarDay::count();
echo "Procesando $totalDays días del calendario...\n\n";

$updated = 0;
$errorCount = 0;

CalendarDay::chunk(10, function ($days) use (&$updated, &$errorCount) {
    foreach ($days as $day) {
        try {
            echo "Procesando día {$day->date}...\n";
            
            // 1. Actualizar booked_slots con el conteo real de citas confirmadas
            $bookedCount = Appointment::where('calendar_day_id', $day->id)
                                    ->where('status', 'confirmed')
                                    ->count();
            
            if ($day->booked_slots != $bookedCount) {
                echo "  - Actualizando booked_slots de {$day->booked_slots} a {$bookedCount}\n";
                $day->booked_slots = $bookedCount;
            }
            
            // 2. Asegurar que available_slots sea un JSON válido
            $availableSlots = getAvailableSlots($day);
            echo "  - Slots disponibles: " . json_encode($availableSlots) . "\n";
            
            // 3. Para días con pocos o ningún slot disponible, regenerar si no están en override manual
            if (!$day->manual_override && count($availableSlots) < 3 && $day->availability_status != 'red' && $day->availability_status != 'black') {
                $defaultSlots = getDefaultTimeSlots();
                
                // Quitar los horarios ya reservados
                $bookedSlots = Appointment::where('calendar_day_id', $day->id)
                                        ->where('status', 'confirmed')
                                        ->pluck('time_slot')
                                        ->toArray();
                
                $availableSlots = array_values(array_diff($defaultSlots, $bookedSlots));
                echo "  - Regenerando slots disponibles: " . json_encode($availableSlots) . "\n";
            }
            
            // 4. Guardar los slots disponibles en formato JSON
            $day->available_slots = json_encode($availableSlots);
            
            // 5. Recalcular estado si no tiene override manual
            if (!$day->manual_override) {
                $newStatus = calculateStatus($day->booked_slots, $day->total_slots, $availableSlots);
                
                if ($day->availability_status != $newStatus) {
                    echo "  - Cambiando estado de '{$day->availability_status}' a '{$newStatus}'\n";
                    $day->availability_status = $newStatus;
                    $updated++;
                }
            } else {
                echo "  - Día con override manual, manteniendo estado: {$day->availability_status}\n";
            }
            
            // 6. Guardar los cambios
            $day->save();
            
        } catch (\Exception $e) {
            echo "  - ERROR al procesar día {$day->date}: " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }
});

echo "\n=== REPARACIÓN COMPLETADA ===\n";
echo "- {$updated} días actualizados\n";
echo "- {$errorCount} errores encontrados\n";
echo "\nPor favor, accede nuevamente al calendario para verificar los cambios.\n";
echo "Nota: Puede ser necesario limpiar la caché del navegador.\n";
