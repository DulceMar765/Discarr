<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Vacation;
use Illuminate\Http\Request;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vacations = Vacation::with('employee')->get();
        return view('admin.vacations.index', compact('vacations'));
    }

    /**
     * Show the form for creating a new vacation.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('admin.vacations.create', compact('employees'));
    }

    /**
     * Store a newly created vacation in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'reason' => 'nullable|string|max:255',
                'status' => 'required|in:pendiente,aprobado,rechazado',
            ]);

            $vacation = Vacation::create($validated);

            // Recalcular estado de vacaciones del empleado
            $this->updateEmployeeVacationStatus($vacation->employee_id);

            return response()->json([
                'message' => 'Vacación registrada exitosamente.',
                'redirect' => route('vacations.index'),
                'employee_on_vacation' => $vacation->status === 'aprobado',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified vacation.
     */
    public function edit(Vacation $vacation)
    {
        $employees = Employee::all();
        return view('admin.vacations.edit', compact('vacation', 'employees'));
    }

    /**
     * Update the specified vacation in storage.
     */
    public function update(Request $request, Vacation $vacation)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'status' => 'required|in:pendiente,aprobado,rechazado',
        ]);

        $vacation->update($validated);

        // Recalcular estado del empleado
        $this->updateEmployeeVacationStatus($vacation->employee_id);

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Vacación actualizada correctamente.',
                'redirect' => route('vacations.index'),
            ]);
        }

        return redirect()->route('vacations.index')->with('success', 'Vacación actualizada correctamente.');
    }

    /**
     * Remove the specified vacation from storage.
     */
    public function destroy(Request $request, Vacation $vacation)
    {
        try {
            $employeeId = $vacation->employee_id;
            $vacation->delete();

            // Recalcular estado del empleado
            $this->updateEmployeeVacationStatus($employeeId);

            if ($request->ajax()) {
                return response()->json(['message' => 'Vacación eliminada']);
            }

            return redirect()->route('vacations.index')
                ->with('success', 'Vacación eliminada');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Hubo un error al eliminar la vacación.'], 500);
            }

            return back()->with('error', 'Hubo un error al eliminar la vacación.');
        }
    }

    /**
     * Método privado para actualizar el campo on_vacation del empleado
     */
    private function updateEmployeeVacationStatus($employeeId)
    {
        $employee = Employee::find($employeeId);

        if ($employee) {
            $isCurrentlyOnVacation = Vacation::where('employee_id', $employee->id)
                ->where('status', 'aprobado')
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->exists();

            $employee->on_vacation = $isCurrentlyOnVacation;
            $employee->save();
        }
    }
}
