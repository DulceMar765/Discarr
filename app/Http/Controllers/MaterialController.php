<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialProject;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    // Listado de materiales y stock actual
    public function index()
    {
        $materials = Material::all();
        return view('admin.material.index', compact('materials'));
    }

    // Formulario de alta de material
    public function create()
    {
        return view('admin.material.create');
    }

    // Guardar material nuevo
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);
        Material::create($request->all());
        return redirect()->route('materials.index')->with('success', 'Material creado correctamente.');
    }

    // Mostrar detalle de material (incluye uso por proyecto)
    public function show(Material $material)
    {
        // Consulta del uso por proyecto
        $usos = MaterialProject::where('material_id', $material->id)->with('project')->get();
        return view('admin.material.show', compact('material', 'usos'));
    }

    // Formulario de edición
    public function edit(Material $material)
    {
        return view('admin.material.edit', compact('material'));
    }

    // Actualizar material
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);
        $material->update($request->all());
        return redirect()->route('materials.index')->with('success', 'Material actualizado correctamente.');
    }

    // Eliminar material
    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Material eliminado.');
    }
}
