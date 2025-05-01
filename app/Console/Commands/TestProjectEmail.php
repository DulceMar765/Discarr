<?php

namespace App\Console\Commands;

use App\Mail\ProjectStatusMail;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestProjectEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:project-email
                            {--email= : Correo electrónico al que enviar la prueba}
                            {--project_id= : ID del proyecto para enviar información. Si no se especifica, se creará un proyecto de prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el envío de correos electrónicos con información de proyectos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando prueba de envío de correos electrónicos...');
        
        // Obtener o solicitar el correo electrónico de destino
        $email = $this->option('email');
        if (!$email) {
            $email = $this->ask('Ingrese el correo electrónico de destino:');
        }
        
        $this->info('Se enviará un correo de prueba a: ' . $email);
        
        // Obtener o crear un proyecto
        $projectId = $this->option('project_id');
        if ($projectId) {
            $project = Project::find($projectId);
            if (!$project) {
                $this->error('No se encontró el proyecto con ID: ' . $projectId);
                return 1;
            }
            $this->info('Usando proyecto existente: ' . $project->name . ' (ID: ' . $project->id . ')');
        } else {
            // Crear un proyecto de prueba
            $project = Project::create([
                'name' => 'Proyecto de Prueba Email ' . now()->format('Y-m-d H:i:s'),
                'description' => 'Proyecto creado para probar el envío de correos electrónicos',
                'status' => 'pendiente',
                'token' => Project::generateUniqueToken()
            ]);
            $this->info('Proyecto de prueba creado: ' . $project->name . ' (ID: ' . $project->id . ')');
        }
        
        // Verificar que el proyecto tenga un token
        if (!$project->token) {
            $project->token = Project::generateUniqueToken();
            $project->save();
            $this->info('Token generado para el proyecto: ' . $project->token);
        } else {
            $this->info('Token existente del proyecto: ' . $project->token);
        }
        
        // Opciones para el correo
        $options = [
            'title' => 'Prueba de Correo - Estado del Proyecto',
            'message' => 'Este es un correo de prueba con información del proyecto ' . $project->name . '.',
            'company_name' => 'Discarr',
            'attach_qr' => true,
        ];
        
        // Enviar el correo
        $this->info('Enviando correo electrónico...');
        try {
            Mail::to($email)
                ->send(new ProjectStatusMail($project, $options));
            
            $this->info('Correo enviado exitosamente a ' . $email);
            $this->info('Prueba completada.');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error al enviar el correo: ' . $e->getMessage());
            return 1;
        }
    }
}
