<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\CalendarDay;
use Illuminate\Support\Facades\Log;

class CalendarUpdateTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Configuración inicial para las pruebas
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear un usuario admin para las pruebas
        $admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
        
        $this->actingAs($admin);
    }
    
    /**
     * Prueba que el endpoint calendar-data devuelve un array JSON válido
     */
    public function test_calendar_data_endpoint_returns_valid_array()
    {
        // Crear algunos días de calendario para la prueba
        $this->createTestCalendarDays();
        
        // Hacer la solicitud al endpoint
        $response = $this->getJson('/admin/appointments/calendar-data');
        
        // Verificar respuesta exitosa
        $response->assertStatus(200);
        
        // Verificar que devuelve un array
        $this->assertIsArray($response->json());
        
        // Verificar que el contenido tiene la estructura esperada - ajustada a la estructura real
        $response->assertJsonStructure([
            '*' => [
                'title',
                'start',
                'backgroundColor',
                'borderColor',
                'textColor'
            ]
        ]);
        
        // Verificar que tenemos el número esperado de elementos
        $this->assertCount(5, $response->json());
    }
    
    /**
     * Prueba que los slots de disponibilidad se manejan correctamente
     */
    public function test_available_slots_are_handled_correctly()
    {
        // Crear un día de calendario con slots disponibles
        $testDate = now()->addDays(1)->format('Y-m-d');
        $slots = ['09:00', '10:00', '11:00', '12:00'];
        
        $calendarDay = CalendarDay::create([
            'date' => $testDate,
            'availability_status' => 'green',
            'booked_slots' => 0,
            'total_slots' => count($slots),
            'manual_override' => false,
            'available_slots' => json_encode($slots)
        ]);
        
        // Solicitar los slots disponibles utilizando el endpoint correcto con parámetro de fecha
        $response = $this->getJson("/admin/appointments/available-slots?date={$testDate}");
        
        // Verificar respuesta exitosa
        $response->assertStatus(200);
        
        // Verificar que devuelve una estructura correcta
        $response->assertJsonStructure([
            'success',
            'slots',
            'status',
            'timestamp'
        ]);
        
        // Verificar que los datos son correctos
        $response->assertJson([
            'success' => true,
            'status' => 'green'
        ]);
        
        // Verificar que los slots existen y son iguales a los esperados
        $responseData = $response->json();
        $this->assertEquals($slots, $responseData['slots']);
    }
    
    /**
     * Prueba que se pueden actualizar el estado de disponibilidad de un día
     */
    public function test_calendar_day_availability_can_be_updated()
    {
        // Crear un día de calendario
        $testDate = now()->addDays(2)->format('Y-m-d');
        
        // Primero creamos el día
        $calendarDay = CalendarDay::create([
            'date' => $testDate,
            'availability_status' => 'green',
            'booked_slots' => 0,
            'total_slots' => 5,
            'manual_override' => false,
            'available_slots' => json_encode(['09:00', '10:00', '11:00', '12:00', '13:00'])
        ]);
        
        // Ahora actualizamos el estado a "no disponible"
        // Ajustamos los datos para que coincidan con lo que espera el controlador
        $response = $this->withoutExceptionHandling() // Para ver el error exacto si ocurre
                         ->postJson('/admin/appointments/save-availability', [
            'date' => $testDate,                 // La fecha en formato Y-m-d
            'status' => 'unavailable',           // El controlador espera 'available', 'unavailable' o 'holiday'
            'manual_override' => true,           // Agregar manual_override explícitamente
            'max_appointments' => 10,            // Asegurarnos de que tiene un valor razonable
            'slots' => []                        // Slots vacíos para día no disponible
        ]);
        
        // Verificar respuesta exitosa
        $response->assertStatus(200);
        
        // Verificar que devuelve una estructura correcta
        $response->assertJsonStructure([
            'success',
            'message',
            'calendarData'
        ]);
        
        // Verificar que los datos se han actualizado en la base de datos
        $updatedDay = CalendarDay::where('date', $testDate)->first();
        $this->assertEquals('red', $updatedDay->availability_status);
        $this->assertTrue((bool) $updatedDay->manual_override);
        
        // Verificar que los slots disponibles están vacíos o es un array vacío
        $availableSlots = $updatedDay->available_slots;
        $this->assertTrue(
            empty($availableSlots) || 
            $availableSlots === '[]' || 
            $availableSlots === 'null' || 
            json_decode($availableSlots) === []
        );
    }
    
    /**
     * Prueba que el controlador maneja correctamente un error en los datos
     */
    public function test_controller_handles_errors_gracefully()
    {
        // Intentar guardar con datos inválidos
        $response = $this->postJson('/admin/appointments/save-availability', [
            'date' => 'fecha-invalida',
            'status' => 'status-que-no-existe',
            'slots' => 'esto-deberia-ser-un-array'
        ]);
        
        // Verificar respuesta de error
        $response->assertStatus(422);
    }
    
    /**
     * Ayudante para crear datos de prueba para el calendario
     */
    private function createTestCalendarDays()
    {
        $baseDate = now();
        
        // Crear 5 días con diferentes estados
        $statuses = ['green', 'yellow', 'red', 'orange', 'black'];
        
        for ($i = 0; $i < 5; $i++) {
            $date = $baseDate->copy()->addDays($i)->format('Y-m-d');
            $status = $statuses[$i];
            
            // Calcular número de slots basado en el estado
            $totalSlots = 0;
            $availableSlots = [];
            
            if ($status === 'green') {
                $totalSlots = 8;
                $availableSlots = ['09:00', '10:00', '11:00', '12:00', '15:00', '16:00', '17:00', '18:00'];
            } elseif ($status === 'yellow') {
                $totalSlots = 3;
                $availableSlots = ['09:00', '12:00', '17:00'];
            } elseif ($status === 'orange') {
                $totalSlots = 1;
                $availableSlots = ['10:00'];
            }
            
            CalendarDay::create([
                'date' => $date,
                'availability_status' => $status,
                'booked_slots' => 0,
                'total_slots' => $totalSlots,
                'manual_override' => false,
                'available_slots' => json_encode($availableSlots)
            ]);
        }
    }
}
