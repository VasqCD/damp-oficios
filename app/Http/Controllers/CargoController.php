<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CargoController extends Controller
{
    public function index(): Response
    {
        $cargos = Cargo::query()
            ->withCount('agentes')
            ->orderBy('nivel_jerarquico', 'desc')
            ->orderBy('nombre')
            ->paginate(15);

        return Inertia::render('Cargos/Index', [
            'cargos' => $cargos,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Cargos/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cargos,nombre',
            'nivel_jerarquico' => 'nullable|integer|min:1|max:100',
            'activo' => 'boolean',
        ]);

        Cargo::create($validated);

        return redirect()->route('cargos.index')
            ->with('success', 'Cargo creado exitosamente.');
    }

    public function show(Cargo $cargo): Response
    {
        $cargo->load(['agentes' => function ($query) {
            $query->with('unidad')->orderBy('nombres');
        }]);

        return Inertia::render('Cargos/Show', [
            'cargo' => $cargo,
        ]);
    }

    public function edit(Cargo $cargo): Response
    {
        return Inertia::render('Cargos/Edit', [
            'cargo' => $cargo,
        ]);
    }

    public function update(Request $request, Cargo $cargo): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cargos,nombre,' . $cargo->id,
            'nivel_jerarquico' => 'nullable|integer|min:1|max:100',
            'activo' => 'boolean',
        ]);

        $cargo->update($validated);

        return redirect()->route('cargos.index')
            ->with('success', 'Cargo actualizado exitosamente.');
    }

    public function destroy(Cargo $cargo): RedirectResponse
    {
        $cargo->delete();

        return redirect()->route('cargos.index')
            ->with('success', 'Cargo eliminado exitosamente.');
    }
}
