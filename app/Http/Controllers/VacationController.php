<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Vacation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\UpdateVacationRequest;

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
    public function store(StoreVacationRequest $request)
{
    try {
        // Agrega esta línea para ver los datos antes de crear la vacación
        dd($request->validated()); 

        // Crear la vacación usando los datos validados
        Vacation::create($request->validated());

        // Responder si es AJAX
        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('vacations.index')
            ]);
        }

        return redirect()->route('vacations.index')
            ->with('success', 'Vacación registrada exitosamente.');
    } catch (\Exception $e) {
        // Captura cualquier excepción y muestra el mensaje
        dd($e->getMessage()); // Esto te ayudará a ver el error exacto
    }
}


    /**
     * Show the form for editing the specified vacation.
     */
    public function edit(Vacation $vacation)
    {
        $employees = Employee::all();

        if (request()->ajax()) {
            return view('admin.vacations.edit', compact('vacation', 'employees'))->render();
        }

        return view('admin.dashboard');
    }

    /**
     * Update the specified vacation in storage.
     */
    public function update(UpdateVacationRequest $request, Vacation $vacation)
    {
        try {
            // Actualizar la vacación usando los datos validados
            $vacation->update($request->validated());

            // Si es una petición AJAX, devolver una respuesta JSON
            if ($request->ajax()) {
                return response()->json([
                    'redirect' => route('vacations.index')
                ]);
            }

            // Si no es AJAX, redirigir de manera tradicional
            return redirect()->route('vacations.index')
                ->with('success', 'Vacación actualizada exitosamente.');
        } catch (\Exception $e) {
            // Si ocurre un error, devolver una respuesta de error
            if ($request->ajax()) {
                return response()->json(['error' => 'Hubo un error al actualizar la vacación.'], 500);
            }

            return back()->with('error', 'Hubo un error al actualizar la vacación.');
        }
    }

    /**
     * Remove the specified vacation from storage.
     */
    public function destroy(Request $request, Vacation $vacation)
    {
        try {
            // Eliminar la vacación
            $vacation->delete();

            // Si es una petición AJAX, devolver respuesta JSON
            if ($request->ajax()) {
                return response()->json(['message' => 'Vacación eliminada']);
            }

            // Si no es AJAX, redirigir de manera tradicional
            return redirect()->route('vacations.index')
                ->with('success', 'Vacación eliminada');
        } catch (\Exception $e) {
            // Si ocurre un error al eliminar, manejarlo
            if ($request->ajax()) {
                return response()->json(['error' => 'Hubo un error al eliminar la vacación.'], 500);
            }

            return back()->with('error', 'Hubo un error al eliminar la vacación.');
        }
    }
}

