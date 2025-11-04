<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use App\Models\Unidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UnidadController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Unidad::query()
            ->with('institucion')
            ->withCount('agentes');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('ciudad', 'like', "%{$search}%")
                  ->orWhere('departamento', 'like', "%{$search}%")
                  ->orWhereHas('institucion', function ($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('institucion_id')) {
            $query->where('institucion_id', $request->institucion_id);
        }

        $unidades = $query->orderBy('nombre')->paginate(15);

        return Inertia::render('Unidades/Index', [
            'unidades' => $unidades,
            'instituciones' => Institucion::where('activo', true)->orderBy('nombre')->get(),
            'filters' => $request->only(['search', 'institucion_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Unidades/Create', [
            'instituciones' => Institucion::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'institucion_id' => 'required|exists:instituciones,id',
            'nombre' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        Unidad::create($validated);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad creada exitosamente.');
    }

    public function show(Unidad $unidad): Response
    {
        $unidad->load(['institucion', 'agentes' => function ($query) {
            $query->with('cargo')->orderBy('nombres');
        }]);

        return Inertia::render('Unidades/Show', [
            'unidad' => $unidad,
        ]);
    }

    public function edit(Unidad $unidad): Response
    {
        return Inertia::render('Unidades/Edit', [
            'unidad' => $unidad,
            'instituciones' => Institucion::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Unidad $unidad): RedirectResponse
    {
        $validated = $request->validate([
            'institucion_id' => 'required|exists:instituciones,id',
            'nombre' => 'required|string|max:255',
            'ciudad' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        $unidad->update($validated);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad actualizada exitosamente.');
    }

    public function destroy(Unidad $unidad): RedirectResponse
    {
        $unidad->delete();

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad eliminada exitosamente.');
    }
}
