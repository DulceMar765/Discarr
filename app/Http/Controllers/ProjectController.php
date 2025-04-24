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

class ProjectController extends Controller
{
    // Listado de proyectos
    public function index()
    {
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }

    // Vista detalle de proyecto
    public function show(Project $project)
    {
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

        return view('projects.show', compact(
            'project', 'diasTrabajados', 'horasTotales', 'costoMateriales', 'empleados', 'materiales', 'horasPorEmpleado', 'dias'
        ));
    }

    // Exportar a CSV
    public function exportCsv(Project $project)
    {
        $rows = ProjectEmployee::where('project_id', $project->id)->with('employee')->get();
        $csv = "Empleado,Fecha,Horas\n";
        foreach ($rows as $row) {
            $csv .= $row->employee->name . "," . $row->date . "," . $row->hours . "\n";
        }
        $filename = 'proyecto_' . $project->id . '_horas.csv';
        Storage::disk('local')->put($filename, $csv);
        return response()->download(storage_path('app/'.$filename));
    }
}
