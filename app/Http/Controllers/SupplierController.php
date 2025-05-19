<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::all();

        if (request()->ajax()) {
            return view('admin.supplier.index', compact('suppliers'))->render();
        }

        // En la carga normal podrías cargar el dashboard u otra vista padre
        return view('admin.supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Muestra el formulario para crear un proveedor
        return view('admin.Supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validar datos
    $request->validate([
        'name' => 'required|string|max:255|unique:suppliers,name',
        'contact_name' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:suppliers,email',
        'phone_number' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:1000',
        'website' => 'nullable|url|max:255',
        'priority' => 'required|in:High,Medium,Low',
        'reliability_score' => 'nullable|integer|min:0|max:100',
    ]);

    // Crear el proveedor
    Supplier::create($request->all());

    // Si la petición es AJAX, devuelve la vista parcial con los proveedores actualizados
    if ($request->ajax()) {
        $suppliers = Supplier::all();
        $html = view('admin.supplier.index', compact('suppliers'))->render();

        return response()->json([
            'message' => 'Proveedor agregado exitosamente.',
            'html' => $html,
            'redirect' => route('supplier.index') // <-- ESTA LÍNEA ES CLAVE
        ]);
    }

    // Si no es AJAX, redireccionamiento normal
    return redirect()->route('supplier.index')->with('success', 'Proveedor agregado exitosamente.');
}



    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        // Muestra los detalles de un proveedor
        return view('admin.Suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        // Muestra el formulario de edición del proveedor
        return view('admin.Supplier.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
{
    // Valida los datos del formulario
    $request->validate([
        'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
        'contact_name' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
        'phone_number' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:1000',
        'website' => 'nullable|url|max:255',
        'priority' => 'required|in:High,Medium,Low',
        'reliability_score' => 'nullable|integer|min:0|max:100',
    ]);

    // Actualiza el proveedor
    $supplier->update($request->all());

    // Si es una petición AJAX, devuelve redirección como JSON
    if ($request->ajax()) {
        return response()->json([
            'message' => 'Proveedor actualizado exitosamente.',
            'redirect' => route('supplier.index')
        ]);
    }

    // Si no es AJAX, redirección normal
    return redirect()->route('supplier.index')->with('success', 'Proveedor actualizado exitosamente.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
{
    $supplier->delete();

    if (request()->ajax()) {
        $suppliers = Supplier::all();
        $html = view('admin.supplier.index', compact('suppliers'))->render();

        return response()->json([
            'message' => 'Proveedor eliminado correctamente.',
            'html' => $html
        ]);
    }

    return redirect()->route('supplier.index')->with('success', 'Proveedor eliminado exitosamente.');
}
}
