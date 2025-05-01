<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectQRTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        // Configurar el almacenamiento para las pruebas
        Storage::fake('public');
    }

    /**
     * Prueba que se puede generar un QR para un proyecto
     */
    public function test_can_generate_qr_for_project()
    {
        // Crear un usuario admin
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Crear un proyecto
        $project = Project::create([
            'name' => 'Proyecto de Prueba',
            'description' => 'Descripción del proyecto de prueba',
            'status' => 'pendiente',
            'token' => Project::generateUniqueToken()
        ]);

        // Autenticar al usuario
        $this->actingAs($user);

        // Hacer la solicitud para generar el QR
        $response = $this->get(route('project.qr.generate', ['projectId' => $project->id]));

        // Verificar que la respuesta es exitosa y contiene los datos esperados
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verificar que la URL del QR está presente en la respuesta
        $responseData = $response->json();
        $this->assertArrayHasKey('qr_url', $responseData);
        $this->assertArrayHasKey('project_status_url', $responseData);
    }

    /**
     * Prueba que se puede descargar un QR para un proyecto
     */
    public function test_can_download_qr_for_project()
    {
        // Crear un usuario admin
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Crear un proyecto
        $project = Project::create([
            'name' => 'Proyecto de Prueba',
            'description' => 'Descripción del proyecto de prueba',
            'status' => 'pendiente',
            'token' => Project::generateUniqueToken()
        ]);

        // Autenticar al usuario
        $this->actingAs($user);

        // Hacer la solicitud para descargar el QR
        $response = $this->get(route('project.qr.download', ['projectId' => $project->id]));

        // Verificar que la respuesta es exitosa y es una imagen PNG
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $response->assertHeader('Content-Disposition', 'attachment; filename="project_' . $project->id . '_qr.png"');
    }

    /**
     * Prueba que se puede ver el estado de un proyecto a través del token (QR)
     */
    public function test_can_view_project_status_via_token()
    {
        // Crear un proyecto
        $project = Project::create([
            'name' => 'Proyecto de Prueba',
            'description' => 'Descripción del proyecto de prueba',
            'status' => 'pendiente',
            'token' => Project::generateUniqueToken()
        ]);

        // Hacer la solicitud para ver el estado del proyecto a través del token
        $response = $this->get(route('project.status', ['token' => $project->token]));

        // Verificar que la respuesta es exitosa y contiene el nombre del proyecto
        $response->assertStatus(200);
        $response->assertSee($project->name);
        $response->assertSee($project->description);
    }

    /**
     * Prueba que se puede regenerar el token de un proyecto
     */
    public function test_can_regenerate_project_token()
    {
        // Crear un usuario admin
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Crear un proyecto
        $project = Project::create([
            'name' => 'Proyecto de Prueba',
            'description' => 'Descripción del proyecto de prueba',
            'status' => 'pendiente',
            'token' => Project::generateUniqueToken()
        ]);

        // Guardar el token original
        $originalToken = $project->token;

        // Autenticar al usuario
        $this->actingAs($user);

        // Hacer la solicitud para regenerar el token
        $response = $this->post(route('project.regenerate-token', ['projectId' => $project->id]));

        // Verificar que la respuesta es exitosa y contiene los datos esperados
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verificar que el token ha cambiado
        $responseData = $response->json();
        $this->assertArrayHasKey('token', $responseData);
        $this->assertNotEquals($originalToken, $responseData['token']);

        // Verificar que el token se ha actualizado en la base de datos
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'token' => $responseData['token']
        ]);
    }
}
