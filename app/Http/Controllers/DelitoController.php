<?php

namespace App\Http\Controllers;

use App\Models\Delito;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DelitoController extends Controller
{
    public function index(): Response
    {
        $delitos = Delito::query()
            ->withCount('solicitudesOficios')
            ->orderBy('nombre')
            ->paginate(15);

        return Inertia::render('Delitos/Index', [
            'delitos' => $delitos,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Delitos/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:delitos,nombre',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        Delito::create($validated);

        return redirect()->route('delitos.index')
            ->with('success', 'Delito creado exitosamente.');
    }

    public function show(Delito $delito): Response
    {
        $delito->load(['solicitudesOficios' => function ($query) {
            $query->with(['institucion', 'unidad'])->orderBy('created_at', 'desc')->limit(10);
        }]);

        return Inertia::render('Delitos/Show', [
            'delito' => $delito,
        ]);
    }

    public function edit(Delito $delito): Response
    {
        return Inertia::render('Delitos/Edit', [
            'delito' => $delito,
        ]);
    }

    public function update(Request $request, Delito $delito): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:delitos,nombre,' . $delito->id,
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $delito->update($validated);

        return redirect()->route('delitos.index')
            ->with('success', 'Delito actualizado exitosamente.');
    }

    public function destroy(Delito $delito): RedirectResponse
    {
        $delito->delete();

        return redirect()->route('delitos.index')
            ->with('success', 'Delito eliminado exitosamente.');
    }
}
