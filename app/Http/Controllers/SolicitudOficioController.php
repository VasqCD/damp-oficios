<?php

namespace App\Http\Controllers;

use App\Models\Agente;
use App\Models\Delito;
use App\Models\Institucion;
use App\Models\SolicitudOficio;
use App\Models\Unidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SolicitudOficioController extends Controller
{
    public function index(Request $request): Response
    {
        $query = SolicitudOficio::query()
            ->with(['institucion', 'unidad', 'agenteSolicitante', 'delito', 'usuarioRegistro'])
            ->withCount('personasSolicitadas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_oficio_entrante', 'like', "%{$search}%")
                  ->orWhereHas('institucion', function ($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Solicitudes/Index', [
            'solicitudes' => $solicitudes,
            'filters' => $request->only(['search', 'estado']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Solicitudes/Create', [
            'instituciones' => Institucion::where('activo', true)->orderBy('nombre')->get(),
            'delitos' => Delito::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'numero_oficio_entrante' => 'required|string|unique:solicitudes_oficios,numero_oficio_entrante',
            'fecha_recepcion' => 'required|date',
            'institucion_id' => 'required|exists:instituciones,id',
            'unidad_id' => 'required|exists:unidades,id',
            'agente_solicitante_id' => 'required|exists:agentes,id',
            'delito_id' => 'nullable|exists:delitos,id',
            'ofendido' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'personas' => 'required|array|min:1',
            'personas.*.nombres' => 'required|string|max:255',
            'personas.*.apellidos' => 'required|string|max:255',
            'personas.*.dni' => 'required|string|max:20',
            'personas.*.fecha_nacimiento' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $solicitud = SolicitudOficio::create([
                'numero_oficio_entrante' => $validated['numero_oficio_entrante'],
                'fecha_recepcion' => $validated['fecha_recepcion'],
                'institucion_id' => $validated['institucion_id'],
                'unidad_id' => $validated['unidad_id'],
                'agente_solicitante_id' => $validated['agente_solicitante_id'],
                'delito_id' => $validated['delito_id'],
                'ofendido' => $validated['ofendido'],
                'observaciones' => $validated['observaciones'],
                'estado' => 'pendiente',
                'usuario_registro_id' => $request->user()->id,
            ]);

            foreach ($validated['personas'] as $persona) {
                $solicitud->personasSolicitadas()->create($persona);
            }
        });

        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud creada exitosamente.');
    }

    public function show(SolicitudOficio $solicitud): Response
    {
        $solicitud->load([
            'institucion',
            'unidad',
            'agenteSolicitante.cargo',
            'delito',
            'usuarioRegistro',
            'personasSolicitadas',
            'respuestaOficio',
        ]);

        return Inertia::render('Solicitudes/Show', [
            'solicitud' => $solicitud,
        ]);
    }

    public function edit(SolicitudOficio $solicitud): Response
    {
        if ($solicitud->estado === 'respondida') {
            return redirect()->route('solicitudes.show', $solicitud)
                ->with('error', 'No se puede editar una solicitud que ya ha sido respondida.');
        }

        $solicitud->load('personasSolicitadas');

        return Inertia::render('Solicitudes/Edit', [
            'solicitud' => $solicitud,
            'instituciones' => Institucion::where('activo', true)->orderBy('nombre')->get(),
            'delitos' => Delito::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, SolicitudOficio $solicitud): RedirectResponse
    {
        if ($solicitud->estado === 'respondida') {
            return redirect()->route('solicitudes.show', $solicitud)
                ->with('error', 'No se puede editar una solicitud que ya ha sido respondida.');
        }

        $validated = $request->validate([
            'numero_oficio_entrante' => 'required|string|unique:solicitudes_oficios,numero_oficio_entrante,' . $solicitud->id,
            'fecha_recepcion' => 'required|date',
            'institucion_id' => 'required|exists:instituciones,id',
            'unidad_id' => 'required|exists:unidades,id',
            'agente_solicitante_id' => 'required|exists:agentes,id',
            'delito_id' => 'nullable|exists:delitos,id',
            'ofendido' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'personas' => 'required|array|min:1',
            'personas.*.id' => 'nullable|exists:personas_solicitadas,id',
            'personas.*.nombres' => 'required|string|max:255',
            'personas.*.apellidos' => 'required|string|max:255',
            'personas.*.dni' => 'required|string|max:20',
            'personas.*.fecha_nacimiento' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $solicitud) {
            $solicitud->update([
                'numero_oficio_entrante' => $validated['numero_oficio_entrante'],
                'fecha_recepcion' => $validated['fecha_recepcion'],
                'institucion_id' => $validated['institucion_id'],
                'unidad_id' => $validated['unidad_id'],
                'agente_solicitante_id' => $validated['agente_solicitante_id'],
                'delito_id' => $validated['delito_id'],
                'ofendido' => $validated['ofendido'],
                'observaciones' => $validated['observaciones'],
            ]);

            $personasIds = collect($validated['personas'])->pluck('id')->filter();
            $solicitud->personasSolicitadas()->whereNotIn('id', $personasIds)->delete();

            foreach ($validated['personas'] as $persona) {
                if (isset($persona['id'])) {
                    $solicitud->personasSolicitadas()->where('id', $persona['id'])->update($persona);
                } else {
                    $solicitud->personasSolicitadas()->create($persona);
                }
            }
        });

        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud actualizada exitosamente.');
    }

    public function destroy(SolicitudOficio $solicitud): RedirectResponse
    {
        if ($solicitud->estado === 'respondida') {
            return redirect()->route('solicitudes.index')
                ->with('error', 'No se puede eliminar una solicitud que ya ha sido respondida.');
        }

        $solicitud->delete();

        return redirect()->route('solicitudes.index')
            ->with('success', 'Solicitud eliminada exitosamente.');
    }

    public function getUnidadesByInstitucion(Institucion $institucion)
    {
        return response()->json([
            'unidades' => $institucion->unidades()->where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function getAgentesByUnidad(Unidad $unidad)
    {
        return response()->json([
            'agentes' => $unidad->agentes()->with('cargo')->where('activo', true)->orderBy('nombres')->get(),
        ]);
    }
}
