<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    // Mostrar todas las categorías
    public function index()
    {
        $categories = Category::all();

        if (request()->ajax()) {
            return view('admin.categories.index', compact('categories'))->render();
        }

        return view('admin.categories.index', compact('categories'));
    }

    // Mostrar el formulario de creación (solo admins)
    public function create()
    {
        $this->authorize('create', Category::class);
        return view('admin.categories.create');
    }

    // Guardar una nueva categoría
    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        // Crear la nueva categoría
        $category = Category::create($validated);
    
        // Si la solicitud es AJAX, devolver un JSON con la redirección
        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('categories.index')  // Redirigir al índice de categorías
            ]);
        }
    
        // Si no es AJAX, proceder con la redirección normal
        return redirect()->route('categories.index')->with('success', 'Categoría creada exitosamente');
    }

    // Mostrar una categoría específica (si lo necesitas)
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    // Mostrar el formulario de edición
    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        return view('admin.categories.edit', compact('category'));
    }

    // Actualizar una categoría
    public function update(Request $request, $id)
    {
        // Validación de los datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Obtener la categoría a actualizar
        $category = Category::findOrFail($id);
        $category->update($validated);

        // Si la solicitud es AJAX, devolver la URL de redirección
        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('categories.index'),  // URL para redirigir al índice de categorías
            ]);
        }

        // Si no es AJAX, proceder con la redirección normal
        return redirect()->route('categories.index');
    }

    // Eliminar una categoría
    public function destroy(Request $request, Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();

        // Si la solicitud es AJAX, devolver un JSON con la información de éxito
        if ($request->ajax()) {
            return response()->json([
                'success' => true,  // Confirmar la eliminación exitosa
            ]);
        }

        // Si no es AJAX, proceder con la redirección normal
        return redirect()->route('categories.index')->with('success', 'Categoría eliminada correctamente.');
    }

}
