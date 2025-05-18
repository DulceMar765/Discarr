<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    use AuthorizesRequests; // Usamos el trait para autorización

    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $employees = Employee::all()->map(function ($employee) {
        $employee->on_vacation = $employee->isOnVacation(); // Aquí añadimos el atributo dinámico
        return $employee;
    });

    if (request()->ajax()) {
        return view('admin.employee.index', compact('employees'))->render();
    }

    return view('admin.employee.index', compact('employees'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Employee::class);
        return view('admin.employee.create'); // O tu vista principal por defecto
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $this->authorize('create', Employee::class); // Verifica si el usuario puede crear empleados

    // Validación
    $validated = $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:employees,email',
        'phone'     => 'required|string|max:20',
        'position'  => 'required|string|max:255',
        'salary'    => 'required|numeric|min:0',
        'hire_date' => 'required|date',
        'address'   => 'nullable|string',
        'status'    => 'nullable|boolean',
    ]);

    // Crear el nuevo empleado
    $employee = Employee::create($validated);

    // Si la solicitud es AJAX, devolver un JSON con la redirección
    if ($request->ajax()) {
        return response()->json([
            'redirect' => route('employee.index'),  // Redirigir al índice de empleados
        ]);
    }

    // Si no es AJAX, proceder con la redirección normal
    return redirect()->route('employee.index')
        ->with('success', 'Empleado creado exitosamente.'); 
}

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('admin.employee.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $this->authorize('update', $employee); // Verifica si el usuario puede editar este empleado
        return view('admin.employee.edit', compact('employee'));
        
    }

    /**
     * Update the specified resource in storage.
     */public function update(Request $request, $id)
    {
    $employee = Employee::findOrFail($id);
    $this->authorize('update', $employee);

    $validated = $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:employees,email,' . $employee->id,
        'phone'     => 'required|string|max:20',
        'position'  => 'required|string|max:255',
        'salary'    => 'required|numeric|min:0',
        'hire_date' => 'required|date',
        'address'   => 'nullable|string',
        'status'    => 'required|boolean',
    ]);

    $employee->update($validated);

    if ($request->ajax()) {
        return response()->json([
            'redirect' => route('employee.index'),
            'message'  => 'Empleado actualizado exitosamente.'
        ]);
    }

    return redirect()->route('employee.index')
        ->with('success', 'Empleado actualizado exitosamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $this->authorize('delete', $employee);
        $employee->delete();
    
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
    
        return redirect()->route('employee.index')->with('success', 'Empleado eliminado exitosamente.');
    }
    
 }
