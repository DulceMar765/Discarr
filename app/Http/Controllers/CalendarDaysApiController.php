<?php

namespace App\Http\Controllers;

use App\Models\CalendarDay;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalendarDaysApiController extends Controller
{
    /**
     * Obtener los datos del calendario para mostrar en la vista
     * Asegura que siempre devuelve un array de datos
     */
    public function calendarData()
    {
        try {
            Log::info('Solicitando datos del calendario');
            
            // Obtener todos los días del calendario con sus citas
            $calendarDays = CalendarDay::all();
            
            // Preparar array para los eventos del calendario
            $events = [];
            
            foreach ($calendarDays as $day) {
                // Calcular color según estado
                $color = '#28a745'; // verde por defecto
                
                switch ($day->availability_status) {
                    case 'red':
                        $color = '#dc3545'; // rojo
                        break;
                    case 'yellow':
                        $color = '#ffc107'; // amarillo
                        break;
                    case 'orange':
                        $color = '#fd7e14'; // naranja
                        break;
                    case 'black':
                        $color = '#343a40'; // negro para festivos
                        break;
                }
                
                // Preparar título según estado
                $title = "Disponible";
                if ($day->availability_status == 'red') {
                    $title = "No Disponible";
                } elseif ($day->availability_status == 'black') {
                    $title = "Festivo / Sin Servicio";
                } elseif ($day->availability_status == 'yellow') {
                    $title = "Poca Disponibilidad";
                } elseif ($day->availability_status == 'orange') {
                    $title = "Muy Poca Disponibilidad";
                }
                
                // Añadir evento al array
                $events[] = [
                    'id' => $day->id,
                    'title' => $title,
                    'start' => $day->date,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => $day->availability_status == 'yellow' ? '#000' : '#fff',
                    'extendedProps' => [
                        'status' => $day->availability_status,
                        'bookedSlots' => $day->booked_slots,
                        'totalSlots' => $day->total_slots,
                        'manualOverride' => $day->manual_override ? true : false
                    ]
                ];
            }
            
            // IMPORTANTE: Asegurar que devolvemos un array, no un objeto
            Log::info('Datos del calendario generados: ' . count($events) . ' eventos');
            
            return response()->json($events);
            
        } catch (\Exception $e) {
            Log::error('Error en calendarData: ' . $e->getMessage());
            // En caso de error, devolver un array vacío en lugar de un objeto
            return response()->json([]);
        }
    }
}
