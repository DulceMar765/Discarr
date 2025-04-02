<?php

namespace App\Http\Controllers;

use App\Models\Categorie; 
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Devuelve una vista con la lista de todas las categorías
        $categories = Categorie::all();
        return view('admin.categorie.index', compact('categories'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Muestra el formulario para crear una nueva categoría
        return view('admin.categorie.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Valida los datos del formulario
        $request->validate([
            'name' => 'required|unique:categories,name|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Crea la categoría
        Categorie::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Redirige a la lista de categorías con un mensaje de éxito
        return redirect()->route('categorie.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorie $categorie)
    {
        // Muestra los detalles de la categoría
        return view('categorie.show', compact('categorie'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorie $categorie)
    {
        // Muestra el formulario para editar una categoría
        return view('categorie.edit', compact('categorie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categorie $categorie)
    {
        // Valida los datos del formulario
        $request->validate([
            'name' => 'required|unique:categories,name,' . $categorie->id . '|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Actualiza la categoría
        $categorie->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Redirige a la lista de categorías con un mensaje de éxito
        return redirect()->route('categorie.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categorie $categorie)
    {
        // Elimina la categoría
        $categorie->delete();

        // Redirige a la lista de categorías con un mensaje de éxito
        return redirect()->route('categorie.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}
