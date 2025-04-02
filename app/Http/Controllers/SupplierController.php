<?php

namespace App\Http\Controllers;

use App\Models\supplier;
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
            // Obtiene todos los proveedores y los pasa a la vista
            $suppliers = Supplier::all();
            return view('admin.supplier.index', compact('suppliers'));
        }
    
        /**
         * Show the form for creating a new resource.
         */
        public function create()
        {
            // Muestra el formulario para crear un proveedor
            return view('admin.supplier.create');
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
    
            return redirect()->route('suppliers.index')->with('success', 'Proveedor agregado exitosamente.');
        }
    
        /**
         * Display the specified resource.
         */
        public function show(Supplier $supplier)
        {
            // Muestra los detalles de un proveedor
            return view('suppliers.show', compact('supplier'));
        }
    
        /**
         * Show the form for editing the specified resource.
         */
        public function edit(Supplier $supplier)
        {
            // Muestra el formulario de edición del proveedor
            return view('admin.supplier.edit', compact('supplier'));
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
    
            return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado exitosamente.');
        }
    
        /**
         * Remove the specified resource from storage.
         */
        public function destroy(Supplier $supplier)
        {
            // Elimina el proveedor
            $supplier->delete();
    
            return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado exitosamente.');
        }
    }
    