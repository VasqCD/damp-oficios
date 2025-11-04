<?php

namespace App\Http\Controllers;

use App\Models\PersonaRegistrada;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PersonaRegistradaController extends Controller
{
    public function index(Request $request): Response
    {
        $query = PersonaRegistrada::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombres', 'like', "%{$search}%")
                  ->orWhere('apellidos', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%")
                  ->orWhere('grupo_delictivo', 'like', "%{$search}%")
                  ->orWhere('estructura_criminal', 'like', "%{$search}%");
            });
        }

        if ($request->filled('grupo_delictivo')) {
            $query->where('grupo_delictivo', 'like', "%{$request->grupo_delictivo}%");
        }

        $personas = $query->orderBy('apellidos')->orderBy('nombres')->paginate(15);

        return Inertia::render('PersonasRegistradas/Index', [
            'personas' => $personas,
            'filters' => $request->only(['search', 'grupo_delictivo']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('PersonasRegistradas/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:personas_registradas,dni',
            'fecha_nacimiento' => 'nullable|date',
            'grupo_delictivo' => 'nullable|string|max:255',
            'estructura_criminal' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'activo' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('personas', 'public');
        }

        PersonaRegistrada::create($validated);

        return redirect()->route('personas-registradas.index')
            ->with('success', 'Persona registrada exitosamente.');
    }

    public function show(PersonaRegistrada $personasRegistrada): Response
    {
        $personasRegistrada->load(['resultadosConsulta' => function ($query) {
            $query->with(['respuestaOficio.solicitudOficio.institucion'])
                  ->orderBy('created_at', 'desc')
                  ->limit(10);
        }]);

        return Inertia::render('PersonasRegistradas/Show', [
            'persona' => $personasRegistrada,
        ]);
    }

    public function edit(PersonaRegistrada $personasRegistrada): Response
    {
        return Inertia::render('PersonasRegistradas/Edit', [
            'persona' => $personasRegistrada,
        ]);
    }

    public function update(Request $request, PersonaRegistrada $personasRegistrada): RedirectResponse
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'dni' => 'required|string|max:20|unique:personas_registradas,dni,' . $personasRegistrada->id,
            'fecha_nacimiento' => 'nullable|date',
            'grupo_delictivo' => 'nullable|string|max:255',
            'estructura_criminal' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'activo' => 'boolean',
        ]);

        if ($request->hasFile('foto')) {
            if ($personasRegistrada->foto) {
                \Storage::disk('public')->delete($personasRegistrada->foto);
            }
            $validated['foto'] = $request->file('foto')->store('personas', 'public');
        }

        $personasRegistrada->update($validated);

        return redirect()->route('personas-registradas.index')
            ->with('success', 'Persona actualizada exitosamente.');
    }

    public function destroy(PersonaRegistrada $personasRegistrada): RedirectResponse
    {
        if ($personasRegistrada->foto) {
            \Storage::disk('public')->delete($personasRegistrada->foto);
        }

        $personasRegistrada->delete();

        return redirect()->route('personas-registradas.index')
            ->with('success', 'Persona eliminada exitosamente.');
    }
}
