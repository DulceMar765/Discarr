<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCheckpoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectCheckpointController extends Controller
{
    /**
     * Guardar un nuevo checkpoint para un proyecto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $projectId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $projectId)
    {
        // Verificar permisos de administrador
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        // Buscar el proyecto
        $project = Project::findOrFail($projectId);

        // Validar los datos
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'required|in:pendiente,en_progreso,completado,retrasado',
            'completion_percentage' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Crear el checkpoint
        $checkpoint = new ProjectCheckpoint([
            'project_id' => $projectId,
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'status' => $request->status,
            'completion_percentage' => $request->completion_percentage,
        ]);

        $checkpoint->save();

        return redirect()->back()->with('success', 'Checkpoint creado correctamente.');
    }

    /**
     * Obtener un checkpoint para editar.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Verificar permisos
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        $checkpoint = ProjectCheckpoint::findOrFail($id);

        return response()->json([
            'success' => true,
            'checkpoint' => $checkpoint
        ]);
    }

    /**
     * Actualizar un checkpoint existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Verificar permisos
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        $checkpoint = ProjectCheckpoint::findOrFail($id);

        // Validar los datos
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'status' => 'required|in:pendiente,en_progreso,completado,retrasado',
            'completion_percentage' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Los datos proporcionados no son válidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Actualizar el checkpoint
        $checkpoint->title = $request->title;
        $checkpoint->description = $request->description;
        $checkpoint->date = $request->date;
        $checkpoint->status = $request->status;
        $checkpoint->completion_percentage = $request->completion_percentage;
        $checkpoint->save();

        return response()->json([
            'success' => true,
            'message' => 'Checkpoint actualizado correctamente.'
        ]);
    }

    /**
     * Eliminar un checkpoint.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Verificar permisos
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.'
            ], 403);
        }

        $checkpoint = ProjectCheckpoint::findOrFail($id);
        $checkpoint->delete();

        return response()->json([
            'success' => true,
            'message' => 'Checkpoint eliminado correctamente.'
        ]);
    }
}
