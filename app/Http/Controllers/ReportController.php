<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Material;
use App\Models\Employee;
use App\Models\Customer;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // Mostrar vista principal de reportes
    public function index()
    {
        // Puedes pasar datos estadísticos generales para mostrar gráficas
        $proyectos = Project::count();
        $materiales = Material::count();
        $empleados = Employee::count();
        $clientes = Customer::count();
        return view('reports.index', compact('proyectos','materiales','empleados','clientes'));
    }

    // Exportar reporte general a Excel
    public function exportExcel(Request $request)
    {
        // Ejemplo: exportar todos los proyectos
        $data = Project::all();
        return Excel::download(new \App\Exports\ProjectsExport($data), 'proyectos.xlsx');
    }

    // Exportar reporte general a PDF
    public function exportPdf(Request $request)
    {
        $data = Project::all();
        $pdf = Pdf::loadView('reports.pdf', compact('data'));
        return $pdf->download('reporte_proyectos.pdf');
    }

    // Endpoint para datos de gráficas (AJAX)
    public function chartData(Request $request)
    {
        $type = $request->input('type', 'proyectos');
        switch($type) {
            case 'materiales':
                $data = Material::select('name', 'quantity')->get();
                break;
            case 'empleados':
                $data = Employee::select('name', 'salary')->get();
                break;
            case 'clientes':
                $data = Customer::select('first_name', 'priority')->get();
                break;
            default:
                $data = Project::select('id', 'created_at')->get();
        }
        return response()->json($data);
    }
}
