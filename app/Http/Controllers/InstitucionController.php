<?php

namespace App\Http\Controllers;

use App\Models\Institucion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InstitucionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Institucion::query()
            ->withCount('unidades');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('nombre_completo', 'like', "%{$search}%");
            });
        }

        $instituciones = $query->orderBy('nombre')->paginate(10);

        return Inertia::render('Instituciones/Index', [
            'instituciones' => $instituciones,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Instituciones/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:instituciones,nombre',
            'nombre_completo' => 'required|string|max:255',
            'activo' => 'boolean',
        ]);

        Institucion::create($validated);

        return redirect()->route('instituciones.index')
            ->with('success', 'Institución creada exitosamente.');
    }

    public function show(Institucion $institucion): Response
    {
        $institucion->load(['unidades' => function ($query) {
            $query->orderBy('nombre');
        }]);

        return Inertia::render('Instituciones/Show', [
            'institucion' => $institucion,
        ]);
    }

    public function edit(Institucion $institucion): Response
    {
        return Inertia::render('Instituciones/Edit', [
            'institucion' => $institucion,
        ]);
    }

    public function update(Request $request, Institucion $institucion): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:instituciones,nombre,' . $institucion->id,
            'nombre_completo' => 'required|string|max:255',
            'activo' => 'boolean',
        ]);

        $institucion->update($validated);

        return redirect()->route('instituciones.index')
            ->with('success', 'Institución actualizada exitosamente.');
    }

    public function destroy(Institucion $institucion): RedirectResponse
    {
        $institucion->delete();

        return redirect()->route('instituciones.index')
            ->with('success', 'Institución eliminada exitosamente.');
    }
}
