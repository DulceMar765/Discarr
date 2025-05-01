<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TestProjectQR extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:project-qr {--save : Guardar el QR generado en el directorio storage/app/public/qrcodes}'
                          . ' {--project_id= : ID del proyecto para generar el QR. Si no se especifica, se creará un proyecto de prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la generación de códigos QR para proyectos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando prueba de generación de QR para proyectos...');
        
        // Verificar si existe el directorio de almacenamiento para QR
        $qrDirectory = storage_path('app/public/qrcodes');
        if (!file_exists($qrDirectory)) {
            mkdir($qrDirectory, 0755, true);
            $this->info('Directorio para QR creado: ' . $qrDirectory);
        }
        
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
                'name' => 'Proyecto de Prueba QR ' . now()->format('Y-m-d H:i:s'),
                'description' => 'Proyecto creado para probar la generación de códigos QR',
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
        
        // URL a la que redirigirá el QR
        $url = route('project.status', ['token' => $project->token]);
        $this->info('URL del estado del proyecto: ' . $url);
        
        // Generar el QR usando BaconQrCode (no requiere Imagick)
        $this->info('Generando QR...');
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($url);
        
        // Guardar el QR si se especificó la opción
        if ($this->option('save')) {
            $filename = 'project_qr_' . $project->id . '.svg';
            Storage::disk('public')->put('qrcodes/' . $filename, $qrCode);
            $qrPath = Storage::disk('public')->path('qrcodes/' . $filename);
            $this->info('QR guardado en: ' . $qrPath);
            $this->info('URL pública: ' . Storage::disk('public')->url('qrcodes/' . $filename));
        }
        
        $this->info('Tamaño del QR generado: ' . strlen($qrCode) . ' bytes');
        $this->info('Prueba completada exitosamente.');
        
        return 0;
    }
}
