<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtiene todos los clientes y los pasa a la vista
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Muestra el formulario para crear un cliente
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valida los datos del formulario
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone_number' => 'nullable|string|max:20',
            'date_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'priority' => 'required|in:High,Medium,Low',
        ]);

        // Crea el cliente
        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Cliente agregado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        // Muestra los detalles de un cliente
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        // Muestra el formulario de edición del cliente
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        // Valida los datos del formulario
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone_number' => 'nullable|string|max:20',
            'date_birth' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'priority' => 'required|in:High,Medium,Low',
        ]);

        // Actualiza el cliente
        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Elimina el cliente
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Cliente eliminado exitosamente.');
    }
}
