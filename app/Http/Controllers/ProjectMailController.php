<?php

namespace App\Http\Controllers;

use App\Mail\ProjectStatusMail;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProjectMailController extends Controller
{
    /**
     * Enviar un correo con el estado de un proyecto
     */
    public function sendProjectStatus(Request $request, $projectId)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'title' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'attach_qr' => 'nullable|boolean',
            'attach_pdf' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Obtener el proyecto
        $project = Project::findOrFail($projectId);

        // Preparar las opciones para el correo
        $options = [
            'title' => $request->input('title', 'Estado del Proyecto'),
            'message' => $request->input('message', 'Aquí tienes la información actualizada del proyecto.'),
            'company_name' => 'Discarr',
            'attach_qr' => $request->input('attach_qr', true),
            'attach_pdf' => $request->input('attach_pdf', false),
        ];

        // Enviar el correo
        try {
            Mail::to($request->input('email'))
                ->send(new ProjectStatusMail($project, $options));

            return response()->json([
                'success' => true,
                'message' => 'Correo enviado exitosamente a ' . $request->input('email')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar formulario para enviar correo (opcional)
     */
    public function showSendForm($projectId)
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
        return view('admin.projects.send-email', compact('project'));
    }

    /**
     * Enviar correo a múltiples destinatarios
     */
    public function sendBulkEmails(Request $request, $projectId)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'emails' => 'required|array',
            'emails.*' => 'required|email',
            'title' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'attach_qr' => 'nullable|boolean',
            'attach_pdf' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Obtener el proyecto
        $project = Project::findOrFail($projectId);

        // Preparar las opciones para el correo
        $options = [
            'title' => $request->input('title', 'Estado del Proyecto'),
            'message' => $request->input('message', 'Aquí tienes la información actualizada del proyecto.'),
            'company_name' => 'Discarr',
            'attach_qr' => $request->input('attach_qr', true),
            'attach_pdf' => $request->input('attach_pdf', false),
        ];

        // Enviar los correos
        $successCount = 0;
        $failedEmails = [];

        foreach ($request->input('emails') as $email) {
            try {
                Mail::to($email)
                    ->send(new ProjectStatusMail($project, $options));
                $successCount++;
            } catch (\Exception $e) {
                $failedEmails[] = [
                    'email' => $email,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Correos enviados: {$successCount} de " . count($request->input('emails')),
            'failed' => $failedEmails
        ]);
    }
}