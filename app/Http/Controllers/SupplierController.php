<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
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
    return view('admin.dashboard', compact('suppliers'));
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
            // Valida los datos del formulario
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
    
            // Crea el proveedor
            Supplier::create($request->all());
    
            return redirect()->route('Suppliers.index')->with('success', 'Proveedor agregado exitosamente.');
        }
    
        /**
         * Display the specified resource.
         */
        public function show(Supplier $Supplier)
        {
            // Muestra los detalles de un proveedor
            return view('Suppliers.show', compact('Supplier'));
        }
    
        /**
         * Show the form for editing the specified resource.
         */
        public function edit(Supplier $Supplier)
        {
            // Muestra el formulario de edición del proveedor
            return view('admin.Supplier.edit', compact('Supplier'));
        }
    
        /**
         * Update the specified resource in storage.
         */
        public function update(Request $request, Supplier $Supplier)
        {
            // Valida los datos del formulario
            $request->validate([
                'name' => 'required|string|max:255|unique:Suppliers,name,' . $Supplier->id,
                'contact_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:Suppliers,email,' . $Supplier->id,
                'phone_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:1000',
                'website' => 'nullable|url|max:255',
                'priority' => 'required|in:High,Medium,Low',
                'reliability_score' => 'nullable|integer|min:0|max:100',
            ]);
    
            // Actualiza el proveedor
            $Supplier->update($request->all());
    
            return redirect()->route('Suppliers.index')->with('success', 'Proveedor actualizado exitosamente.');
        }
    
        /**
         * Remove the specified resource from storage.
         */
        public function destroy(Supplier $Supplier)
        {
            // Elimina el proveedor
            $Supplier->delete();
    
            return redirect()->route('Suppliers.index')->with('success', 'Proveedor eliminado exitosamente.');
        }
    }