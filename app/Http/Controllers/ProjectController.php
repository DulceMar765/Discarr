<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Employee;
use App\Models\Material;
use App\Models\ProjectEmployee;
use App\Models\MaterialProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // Listado de proyectos
    public function index()
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        $projects = Project::all(); // Obtén todos los proyectos
        return view('admin.projects.index', compact('projects'));
    }

    // Crear un nuevo proyecto
    public function create()
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        return view('admin.projects.create');
    }

    // Guardar un nuevo proyecto
    public function store(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string|in:pendiente,en_progreso,completado',
            'budget' => 'nullable|numeric|min:0'
        ]);

        $project = new Project($request->all());
        $project->token = Project::generateUniqueToken(); // Generar token único
        $project->save();

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Proyecto creado correctamente');
    }

    // Editar un proyecto
    public function edit(Project $project)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        return view('admin.projects.edit', compact('project'));
    }

    // Actualizar un proyecto
    public function update(Request $request, Project $project)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string|in:pendiente,en_progreso,completado',
            'budget' => 'nullable|numeric|min:0'
        ]);

        $project->update($request->all());

        // Si el proyecto no tiene token, generamos uno
        if (!$project->token) {
            $project->token = Project::generateUniqueToken();
            $project->save();
        }

        return redirect()->route('projects.show', $project->id)
            ->with('success', 'Proyecto actualizado correctamente');
    }

    // Vista detalle de proyecto
    public function show(Project $project)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        // Barra de progreso automatizada: porcentaje de días trabajados sobre duración estimada (si existe)
        $diasTrabajados = ProjectEmployee::where('project_id', $project->id)->distinct('date')->count('date');

        // Horas totales invertidas
        $horasTotales = ProjectEmployee::where('project_id', $project->id)->sum('hours');

        // Costos del proyecto (solo materiales usados)
        $materiales = MaterialProject::where('project_id', $project->id)->with('material')->get();
        $costoMateriales = $materiales->sum(function($item){
            return $item->quantity * ($item->material->price ?? 0);
        });

        // Empleados asignados
        $empleados = Employee::whereIn('id', ProjectEmployee::where('project_id', $project->id)->pluck('employee_id'))->get();

        // Lista de materiales usados
        // (ya obtenido en $materiales)

        // Registro de horas por empleado
        $horasPorEmpleado = ProjectEmployee::where('project_id', $project->id)
            ->select('employee_id', DB::raw('SUM(hours) as total_hours'))
            ->groupBy('employee_id')
            ->with('employee')
            ->get();

        // Días específicos en los que se ha trabajado
        $dias = ProjectEmployee::where('project_id', $project->id)->pluck('date')->unique();

        return view('admin.projects.show', compact(
            'project', 'diasTrabajados', 'horasTotales', 'costoMateriales', 'empleados', 'materiales', 'horasPorEmpleado', 'dias'
        ));
    }

    // Exportar a CSV
    public function exportCsv(Project $project)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario es administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        
        $rows = ProjectEmployee::where('project_id', $project->id)->with('employee')->get();
        $csv = "Empleado,Fecha,Horas\n";
        foreach ($rows as $row) {
            $csv .= $row->employee->name . "," . $row->date . "," . $row->hours . "\n";
        }
        $filename = 'proyecto_' . $project->id . '_horas.csv';
        Storage::disk('local')->put($filename, $csv);
        return response()->download(storage_path('app/'.$filename));
    }

    public function status(Project $project)
    {
        // Este método muestra el estado público del proyecto, no requiere autenticación
        $diasTrabajados = ProjectEmployee::where('project_id', $project->id)->distinct('date')->count('date');
    $horasTotales = ProjectEmployee::where('project_id', $project->id)->sum('hours');
    $materiales = MaterialProject::where('project_id', $project->id)->with('material')->get();
    $costoMateriales = $materiales->sum(function($item) {
        return $item->quantity * ($item->material->price ?? 0);
    });

    $progreso = 0;
    if ($project->start_date && $project->end_date) {
        $diasTotales = $project->start_date->diffInDays($project->end_date);
        if ($diasTotales > 0) {
            $progreso = min(100, round(($diasTrabajados / $diasTotales) * 100));
        }
    }

    return view('projects.status', compact(
        'project', 'diasTrabajados', 'horasTotales', 'materiales', 'costoMateriales', 'progreso'
    ));
}

}
