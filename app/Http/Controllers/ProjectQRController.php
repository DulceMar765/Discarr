<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectStatusMail;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Color\Color;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class ProjectQRController extends Controller
{
    /**
     * Generar un QR para un proyecto específico
     */
    public function generateQR($projectId)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        $project = Project::findOrFail($projectId);
        
        // Si el proyecto no tiene token, generamos uno
        if (!$project->token) {
            $project->token = Project::generateUniqueToken();
            $project->save();
        }
        
        // URL a la que redirigirá el QR (página de estado del proyecto)
        $url = route('project.status', ['token' => $project->token]);
        
        // Generar el QR usando BaconQrCode (no requiere Imagick)
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($url);
        
        // Guardar el QR en un archivo temporal
        $filename = 'project_qr_' . $project->id . '.svg';
        Storage::disk('public')->put('qrcodes/' . $filename, $qrCode);
        
        // Devolver la URL del QR generado
        return response()->json([
            'success' => true,
            'qr_url' => Storage::disk('public')->url('qrcodes/' . $filename),
            'project_status_url' => $url
        ]);
    }
    
    /**
     * Mostrar el estado de un proyecto a partir de su token (accesible desde el QR)
     */
    public function showProjectStatus($token)
    {
        $project = Project::where('token', $token)->firstOrFail();
        
        // Obtener datos relevantes del proyecto
        $diasTrabajados = DB::table('project_employees')
            ->where('project_id', $project->id)
            ->distinct('date')
            ->count('date');
            
        $horasTotales = DB::table('project_employees')
            ->where('project_id', $project->id)
            ->sum('hours');
            
        $materiales = DB::table('material_projects')
            ->where('project_id', $project->id)
            ->join('materials', 'material_projects.material_id', '=', 'materials.id')
            ->select('materials.name', 'materials.price', 'material_projects.quantity')
            ->get();
            
        $costoMateriales = $materiales->sum(function($item) {
            return $item->quantity * ($item->price ?? 0);
        });
        
        // Calcular progreso del proyecto
        $progreso = 0;
        if ($project->start_date && $project->end_date) {
            $diasTotales = $project->start_date->diffInDays($project->end_date);
            if ($diasTotales > 0) {
                $progreso = min(100, round(($diasTrabajados / $diasTotales) * 100));
            }
        }
        
        return view('projects.status', compact(
            'project', 'diasTrabajados', 'horasTotales', 'materiales', 
            'costoMateriales', 'progreso'
        ));
    }
    
    /**
     * Descargar el QR de un proyecto
     */
    public function downloadQR($projectId)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        $project = Project::findOrFail($projectId);
        
        // Si el proyecto no tiene token, generamos uno
        if (!$project->token) {
            $project->token = Project::generateUniqueToken();
            $project->save();
        }
        
        // URL a la que redirigirá el QR
        $url = route('project.status', ['token' => $project->token]);
        
        // Generar el QR usando BaconQrCode (no requiere Imagick)
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($url);
        
        // Devolver el QR como descarga
        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="project_' . $project->id . '_qr.svg"');
    }
    
    /**
     * Manejar solicitudes de actualización por correo electrónico
     */
    public function requestUpdate(Request $request, $token)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        $project = Project::where('token', $token)->firstOrFail();
        
        // Obtener datos relevantes del proyecto
        $diasTrabajados = DB::table('project_employees')
            ->where('project_id', $project->id)
            ->distinct('date')
            ->count('date');
            
        $horasTotales = DB::table('project_employees')
            ->where('project_id', $project->id)
            ->sum('hours');
            
        $materiales = DB::table('material_projects')
            ->where('project_id', $project->id)
            ->join('materials', 'material_projects.material_id', '=', 'materials.id')
            ->select('materials.name', 'materials.price', 'material_projects.quantity')
            ->get();
            
        $costoMateriales = $materiales->sum(function($item) {
            return $item->quantity * ($item->price ?? 0);
        });
        
        // Calcular progreso del proyecto
        $progreso = 0;
        if ($project->start_date && $project->end_date) {
            $diasTotales = $project->start_date->diffInDays($project->end_date);
            if ($diasTotales > 0) {
                $progreso = min(100, round(($diasTrabajados / $diasTotales) * 100));
            }
        }
        
        // URL para acceder al estado del proyecto
        $url = route('project.status', ['token' => $project->token]);
        
        try {
            // Enviar correo con el estado del proyecto
            Mail::to($request->email)->send(new ProjectStatusMail($project, [
                'title' => 'Estado del Proyecto',
                'message' => 'Has solicitado información actualizada sobre el proyecto. Aquí tienes los detalles:',
                'additional_info' => 'Si tienes alguna pregunta, por favor contacta directamente con nuestro equipo.',
                'attach_qr' => true
            ]));
            
            return redirect()->back()->with('success', 'Se ha enviado un correo con la información actualizada del proyecto.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo enviar el correo. Por favor, inténtalo de nuevo más tarde.');
        }
    }
    
    /**
     * Regenerar el token de un proyecto y devolver el nuevo QR
     */
    public function regenerateToken($projectId)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        $project = Project::findOrFail($projectId);
        
        // Generar un nuevo token único
        $project->token = Project::generateUniqueToken();
        $project->save();
        
        // URL a la que redirigirá el QR
        $url = route('project.status', ['token' => $project->token]);
        
        // Generar el QR usando BaconQrCode (no requiere Imagick)
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($url);
        
        // Guardar el QR en un archivo temporal
        $filename = 'project_qr_' . $project->id . '.svg';
        Storage::disk('public')->put('qrcodes/' . $filename, $qrCode);
        
        // Devolver la respuesta JSON
        return response()->json([
            'success' => true,
            'token' => $project->token,
            'qr_url' => Storage::disk('public')->url('qrcodes/' . $filename),
            'project_status_url' => $url
        ]);
    }
}
