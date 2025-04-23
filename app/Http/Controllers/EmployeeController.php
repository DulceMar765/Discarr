<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::all();
        if (request()->ajax()) {
            return view('admin.employee.index', compact('employees'))->withoutComponent();
        }
        return view('admin.employee.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.employee.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:categories,email',
            'phone'     => 'required|string|max:20',
            'position'  => 'required|string|max:255',
            'salary'    => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'address'   => 'nullable|string',
            'status'    => 'nullable|boolean',
        ]);

        // Crear la categoría
        Category::create($validated);

        // Redirigir con éxito
        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return view('employee.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('employee.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:employees,email,' . $employee->id,
            'phone'     => 'required|string|max:20',
            'position'  => 'required|string|max:255',
            'salary'    => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'address'   => 'nullable|string',
            'status'    => 'boolean'
        ]);

        $employee->update($validated);

        return redirect()->route('employee.index')
            ->with('success', 'Empleado actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employee.index')
            ->with('success', 'Empleado eliminado exitosamente.');
    }
}
