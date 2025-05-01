<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class ProjectStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El proyecto al que se refiere el correo
     */
    protected $project;

    /**
     * Opciones adicionales para el correo
     */
    protected $options;

    /**
     * Create a new message instance.
     */
    public function __construct(Project $project, array $options = [])
    {
        $this->project = $project;
        $this->options = array_merge([
            'title' => 'Estado del Proyecto',
            'message' => 'Aquí tienes la información actualizada del proyecto.',
            'company_name' => 'Discarr',
            'additional_info' => '',
            'attach_qr' => true, // Adjuntar el QR como archivo
            'attach_pdf' => false, // Adjuntar un PDF con detalles del proyecto
        ], $options);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->options['title'] . ': ' . $this->project->name,
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Obtener datos relevantes del proyecto
        $diasTrabajados = 0;
        $horasTotales = 0;
        $costoMateriales = 0;
        $progreso = 0;

        // Calcular estos valores si existen las relaciones
        if (method_exists($this->project, 'projectEmployees')) {
            $diasTrabajados = $this->project->projectEmployees()->distinct('date')->count('date');
            $horasTotales = $this->project->projectEmployees()->sum('hours');
        }

        if (method_exists($this->project, 'materialProjects')) {
            $materiales = $this->project->materialProjects;
            $costoMateriales = $materiales->sum(function($item) {
                return $item->quantity * ($item->material->price ?? 0);
            });
        }

        // Calcular progreso
        if ($this->project->start_date && $this->project->end_date) {
            $diasTotales = $this->project->start_date->diffInDays($this->project->end_date);
            if ($diasTotales > 0) {
                $progreso = min(100, round(($diasTrabajados / $diasTotales) * 100));
            }
        }

        // Generar la URL del QR
        $url = route('project.status', ['token' => $this->project->token]);
        
        // Crear el contenido del correo en texto plano
        $textContent = "";
        $textContent .= "{$this->options['title']}: {$this->project->name}\n\n";
        $textContent .= "{$this->options['message']}\n\n";
        $textContent .= "Detalles del Proyecto:\n";
        $textContent .= "-------------------\n";
        $textContent .= "Nombre: {$this->project->name}\n";
        $textContent .= "Descripción: {$this->project->description}\n";
        $textContent .= "Estado: {$this->project->status}\n";
        $textContent .= "Progreso: {$progreso}%\n";
        $textContent .= "Días trabajados: {$diasTrabajados}\n";
        $textContent .= "Horas totales: {$horasTotales}\n";
        $textContent .= "Costo de materiales: $" . number_format($costoMateriales, 2) . "\n\n";
        $textContent .= "Para ver el estado completo del proyecto, escanea el código QR adjunto o visita:\n";
        $textContent .= "{$url}\n\n";
        $textContent .= "Atentamente,\n{$this->options['company_name']}\n";
        
        // Generar el QR y adjuntarlo
        if ($this->options['attach_qr']) {
            $qrCode = $this->generateQR($url);
            $filename = 'project_qr_' . $this->project->id . '.svg';
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            $filePath = $tempPath . '/' . $filename;
            file_put_contents($filePath, $qrCode);
            
            $this->attach($filePath, [
                'as' => $filename,
                'mime' => 'image/svg+xml',
            ]);
        }
        
        // Adjuntar PDF si es necesario
        if ($this->options['attach_pdf']) {
            $pdfPath = $this->generateProjectPDF();
            if ($pdfPath) {
                $this->attach($pdfPath, [
                    'as' => 'proyecto_' . $this->project->id . '.pdf',
                    'mime' => 'application/pdf',
                ]);
            }
        }
        
        return $this->subject($this->options['title'] . ': ' . $this->project->name)
                    ->text('emails.project-status-plain', ['content' => $textContent]);
    }
    
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            text: 'emails.project-status-plain',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Si se debe adjuntar el QR
        if ($this->options['attach_qr']) {
            // Generar el QR
            $url = route('project.status', ['token' => $this->project->token]);
            $qrCode = $this->generateQR($url);

            // Guardar el QR temporalmente
            $filename = 'project_qr_' . $this->project->id . '.svg';
            $tempPath = storage_path('app/temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            $filePath = $tempPath . '/' . $filename;
            file_put_contents($filePath, $qrCode);

            // Adjuntar el QR
            $attachments[] = Attachment::fromPath($filePath)
                ->as($filename)
                ->withMime('image/svg+xml');
        }

        // Si se debe adjuntar un PDF con detalles del proyecto
        if ($this->options['attach_pdf']) {
            $pdfPath = $this->generateProjectPDF();
            if ($pdfPath) {
                $attachments[] = Attachment::fromPath($pdfPath)
                    ->as('proyecto_' . $this->project->id . '.pdf')
                    ->withMime('application/pdf');
            }
        }

        return $attachments;
    }

    /**
     * Generar un código QR para la URL del proyecto
     */
    protected function generateQR($url)
    {
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        return $writer->writeString($url);
    }

    /**
     * Generar un PDF con detalles del proyecto
     */
    protected function generateProjectPDF()
    {
        // Aquí se implementaría la generación del PDF con detalles del proyecto
        // Por ahora, retornamos null para indicar que no se ha implementado
        return null;
    }
}
