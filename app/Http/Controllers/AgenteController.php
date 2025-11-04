<?php

namespace App\Http\Controllers;

use App\Models\Agente;
use App\Models\Cargo;
use App\Models\Institucion;
use App\Models\Unidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AgenteController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Agente::query()
            ->with(['cargo', 'unidad.institucion']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhereHas('cargo', function ($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('unidad_id')) {
            $query->where('unidad_id', $request->unidad_id);
        }

        if ($request->filled('cargo_id')) {
            $query->where('cargo_id', $request->cargo_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $agentes = $query->orderBy('apellidos')->orderBy('nombres')->paginate(15);

        return Inertia::render('Agentes/Index', [
            'agentes' => $agentes,
            'cargos' => Cargo::where('activo', true)->orderBy('nombre')->get(),
            'instituciones' => Institucion::where('activo', true)->orderBy('nombre')->get(),
            'filters' => $request->only(['search', 'unidad_id', 'cargo_id', 'tipo']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Agentes/Create', [
            'cargos' => Cargo::where('activo', true)->orderBy('nivel_jerarquico', 'desc')->orderBy('nombre')->get(),
            'instituciones' => Institucion::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'cargo_id' => 'required|exists:cargos,id',
            'unidad_id' => 'required|exists:unidades,id',
            'tipo' => 'required|in:solicitante,firmante',
            'activo' => 'boolean',
        ]);

        Agente::create($validated);

        return redirect()->route('agentes.index')
            ->with('success', 'Agente creado exitosamente.');
    }

    public function show(Agente $agente): Response
    {
        $agente->load(['cargo', 'unidad.institucion', 'solicitudesOficios' => function ($query) {
            $query->with(['institucion', 'delito'])->orderBy('created_at', 'desc')->limit(10);
        }]);

        return Inertia::render('Agentes/Show', [
            'agente' => $agente,
        ]);
    }

    public function edit(Agente $agente): Response
    {
        $agente->load('unidad.institucion');

        return Inertia::render('Agentes/Edit', [
            'agente' => $agente,
            'cargos' => Cargo::where('activo', true)->orderBy('nivel_jerarquico', 'desc')->orderBy('nombre')->get(),
            'instituciones' => Institucion::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Agente $agente): RedirectResponse
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'cargo_id' => 'required|exists:cargos,id',
            'unidad_id' => 'required|exists:unidades,id',
            'tipo' => 'required|in:solicitante,firmante',
            'activo' => 'boolean',
        ]);

        $agente->update($validated);

        return redirect()->route('agentes.index')
            ->with('success', 'Agente actualizado exitosamente.');
    }

    public function destroy(Agente $agente): RedirectResponse
    {
        $agente->delete();

        return redirect()->route('agentes.index')
            ->with('success', 'Agente eliminado exitosamente.');
    }
}
