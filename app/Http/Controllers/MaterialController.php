<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialProject;
use App\Models\Category;
use App\Models\Supplier;
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
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('admin.material.create', compact('categories', 'suppliers'));
    }

    // Guardar material nuevo
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'stock' => 'required|numeric|min:0',
        'unit' => 'required|string|max:50',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
        'supplier_id' => 'required|exists:suppliers,id',
    ]);

    $material = Material::create($validated);

    // Opcional: Puedes devolver un HTML actualizado para la lista o sólo un mensaje
    // Por ejemplo, si tienes un partial blade para la lista de materiales:
    // $html = view('admin.material.partials.list', ['materials' => Material::all()])->render();

    return response()->json([
        'message' => 'Material creado correctamente.',
        // 'html' => $html,
    ]);
}


    // Mostrar detalle de material (incluye uso por proyecto)
    public function show(Material $material)
    {
        $usos = MaterialProject::where('material_id', $material->id)->with('project')->get();
        return view('admin.material.show', compact('material', 'usos'));
    }

    // Formulario de edición
    public function edit(Material $material)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('admin.material.edit', compact('material', 'categories', 'suppliers'));
    }

    // Actualizar material
public function update(Request $request, Material $material)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'stock' => 'required|numeric|min:0',
        'unit' => 'required|string|max:50',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
        'supplier_id' => 'required|exists:suppliers,id',
    ]);

    $material->update($request->all());

    if ($request->ajax()) {
        return response()->json([
            'message' => 'Material actualizado correctamente.',
            'redirect' => route('admin.material.index')  // ← muy importante que esté bien nombrada
        ]);
    }

    return redirect()->route('admin.material.index')->with('success', 'Material actualizado correctamente.');
}


    // Eliminar material
  public function destroy(Request $request, $id)
{
    $material = Material::findOrFail($id);
    $material->delete();

    if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Material eliminado correctamente.']);
    }

    return redirect()->route('admin.material.index')->with('success', 'Material eliminado correctamente.');
}




}
