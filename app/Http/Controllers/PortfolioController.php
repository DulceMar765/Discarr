<?php
namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Importar el trait

class PortfolioController extends Controller
{
    use AuthorizesRequests; // Usar el trait para habilitar el método authorize

    // Mostrar el portafolio (para usuarios y admins)
    public function index()
    {
        $portfolioItems = Portfolio::all();
        return view('portafolio.index', compact('portfolioItems'));
    }

    // Mostrar el formulario para subir una nueva imagen (solo admins)
    public function create()
    {
        $this->authorize('create', Portfolio::class); // Verifica que el usuario sea admin
        return view('portafolio.create');
    }

    // Guardar una nueva imagen en el portafolio (solo admins)
    public function store(Request $request)
    {
        $this->authorize('create', Portfolio::class); // Verifica que el usuario sea admin

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $imagePath = $request->file('image')->store('portfolio', 'public');

        Portfolio::create([
            'title' => $request->title,
            'image_path' => $imagePath,
            'description' => $request->description,
        ]);

        return redirect()->route('portfolio.index')->with('success', 'Imagen subida correctamente.');
    }

    // Mostrar el formulario para editar una imagen del portafolio
    public function edit(Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio); // Verifica que el usuario tenga permiso para editar
        return view('portafolio.edit', compact('portfolio'));
    }

    // Actualizar una imagen del portafolio
    public function update(Request $request, Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio); // Verifica que el usuario sea admin

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior
            Storage::disk('public')->delete($portfolio->image_path);

            // Subir la nueva imagen
            $portfolio->image_path = $request->file('image')->store('portfolio', 'public');
        }

        $portfolio->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('portfolio.index')->with('success', 'Imagen actualizada correctamente.');
    }

    // Eliminar una imagen del portafolio (solo admins)
    public function destroy(Portfolio $portfolio)
    {
        $this->authorize('delete', $portfolio); // Verifica que el usuario sea admin

        Storage::disk('public')->delete($portfolio->image_path);
        $portfolio->delete();

        return redirect()->route('portfolio.index')->with('success', 'Imagen eliminada correctamente.');
    }
}
