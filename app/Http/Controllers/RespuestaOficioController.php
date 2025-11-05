<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionSistema;
use App\Models\PersonaRegistrada;
use App\Models\RespuestaOficio;
use App\Models\SolicitudOficio;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RespuestaOficioController extends Controller
{
    public function index(Request $request): Response
    {
        $query = RespuestaOficio::query()
            ->with(['solicitudOficio.institucion', 'analista', 'jefeRegional']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_oficio_respuesta', 'like', "%{$search}%")
                  ->orWhereHas('solicitudOficio', function ($q) use ($search) {
                      $q->where('numero_oficio_entrante', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $respuestas = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Respuestas/Index', [
            'respuestas' => $respuestas,
            'filters' => $request->only(['search', 'estado']),
        ]);
    }

    public function create(Request $request, ?SolicitudOficio $solicitud = null): Response
    {
        if ($solicitud) {
            $solicitud->load([
                'institucion',
                'unidad',
                'agenteSolicitante.cargo',
                'delito',
                'personasSolicitadas',
            ]);

            if ($solicitud->estado === 'respondida') {
                return redirect()->route('solicitudes.show', $solicitud)
                    ->with('error', 'Esta solicitud ya ha sido respondida.');
            }
        }

        $jefesRegionales = User::orderBy('name')->get();

        return Inertia::render('Respuestas/Create', [
            'solicitud' => $solicitud,
            'jefesRegionales' => $jefesRegionales,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'solicitud_oficio_id' => 'required|exists:solicitudes_oficios,id|unique:respuestas_oficios,solicitud_oficio_id',
            'fecha_respuesta' => 'required|date',
            'jefe_regional_id' => 'required|exists:users,id',
            'contenido_respuesta' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $anio = now()->year;
            $correlativo = $this->obtenerSiguienteCorrelativo($anio);

            $respuesta = RespuestaOficio::create([
                'solicitud_oficio_id' => $validated['solicitud_oficio_id'],
                'numero_oficio_respuesta' => "RE-{$correlativo}-{$anio}",
                'correlativo' => $correlativo,
                'anio' => $anio,
                'fecha_respuesta' => $validated['fecha_respuesta'],
                'analista_id' => $request->user()->id,
                'jefe_regional_id' => $validated['jefe_regional_id'],
                'contenido_respuesta' => $validated['contenido_respuesta'],
                'estado' => 'borrador',
            ]);

            $solicitud = SolicitudOficio::findOrFail($validated['solicitud_oficio_id']);
            $solicitud->update(['estado' => 'en_proceso']);

            foreach ($solicitud->personasSolicitadas as $personaSolicitada) {
                $personaRegistrada = PersonaRegistrada::where('dni', $personaSolicitada->dni)->first();

                $respuesta->resultadosConsulta()->create([
                    'persona_solicitada_id' => $personaSolicitada->id,
                    'persona_registrada_id' => $personaRegistrada?->id,
                    'encontrada' => $personaRegistrada !== null,
                    'detalles' => $personaRegistrada ? [
                        'grupo_delictivo' => $personaRegistrada->grupo_delictivo,
                        'estructura_criminal' => $personaRegistrada->estructura_criminal,
                        'observaciones' => $personaRegistrada->observaciones,
                    ] : null,
                ]);
            }

            ConfiguracionSistema::establecerValor(
                "correlativo_{$anio}",
                $correlativo,
                "Correlativo actual para el año {$anio}"
            );
        });

        return redirect()->route('respuestas.index')
            ->with('success', 'Respuesta creada exitosamente.');
    }

    public function show(RespuestaOficio $respuesta): Response
    {
        $respuesta->load([
            'solicitudOficio.institucion',
            'solicitudOficio.unidad',
            'solicitudOficio.agenteSolicitante.cargo',
            'solicitudOficio.delito',
            'analista',
            'jefeRegional',
            'resultadosConsulta.personaSolicitada',
            'resultadosConsulta.personaRegistrada',
        ]);

        return Inertia::render('Respuestas/Show', [
            'respuesta' => $respuesta,
        ]);
    }

    public function edit(RespuestaOficio $respuesta): Response
    {
        if ($respuesta->estado !== 'borrador') {
            return redirect()->route('respuestas.show', $respuesta)
                ->with('error', 'Solo se pueden editar respuestas en borrador.');
        }

        $respuesta->load([
            'solicitudOficio.institucion',
            'solicitudOficio.unidad',
            'solicitudOficio.agenteSolicitante.cargo',
            'solicitudOficio.personasSolicitadas',
            'resultadosConsulta.personaSolicitada',
            'resultadosConsulta.personaRegistrada',
        ]);

        $jefesRegionales = User::orderBy('name')->get();

        return Inertia::render('Respuestas/Edit', [
            'respuesta' => $respuesta,
            'jefesRegionales' => $jefesRegionales,
        ]);
    }

    public function update(Request $request, RespuestaOficio $respuesta): RedirectResponse
    {
        if ($respuesta->estado !== 'borrador') {
            return redirect()->route('respuestas.show', $respuesta)
                ->with('error', 'Solo se pueden editar respuestas en borrador.');
        }

        $validated = $request->validate([
            'fecha_respuesta' => 'required|date',
            'jefe_regional_id' => 'required|exists:users,id',
            'contenido_respuesta' => 'nullable|string',
            'estado' => 'required|in:borrador,firmada,enviada',
        ]);

        $respuesta->update($validated);

        if ($validated['estado'] === 'firmada' || $validated['estado'] === 'enviada') {
            $respuesta->solicitudOficio->update(['estado' => 'respondida']);
        }

        return redirect()->route('respuestas.index')
            ->with('success', 'Respuesta actualizada exitosamente.');
    }

    public function destroy(RespuestaOficio $respuesta): RedirectResponse
    {
        if ($respuesta->estado !== 'borrador') {
            return redirect()->route('respuestas.index')
                ->with('error', 'Solo se pueden eliminar respuestas en borrador.');
        }

        DB::transaction(function () use ($respuesta) {
            $respuesta->solicitudOficio->update(['estado' => 'pendiente']);
            $respuesta->delete();
        });

        return redirect()->route('respuestas.index')
            ->with('success', 'Respuesta eliminada exitosamente.');
    }

    public function generarPdf(RespuestaOficio $respuesta)
    {
        $respuesta->load([
            'solicitudOficio.institucion',
            'solicitudOficio.unidad',
            'solicitudOficio.agenteSolicitante.cargo',
            'solicitudOficio.delito',
            'analista',
            'jefeRegional',
            'resultadosConsulta.personaSolicitada',
            'resultadosConsulta.personaRegistrada',
        ]);

        $pdf = Pdf::loadView('pdf.respuesta-oficio', [
            'respuesta' => $respuesta,
        ]);

        $pdf->setPaper('letter', 'portrait');

        $filename = str_replace(['/', '\\'], '_', $respuesta->numero_oficio_respuesta) . '.pdf';

        return $pdf->stream($filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function consultarPersonas(Request $request)
    {
        $validated = $request->validate([
            'personas_solicitadas' => 'required|array',
            'personas_solicitadas.*.id' => 'required|integer',
            'personas_solicitadas.*.dni' => 'required|string',
            'personas_solicitadas.*.nombres' => 'required|string',
            'personas_solicitadas.*.apellidos' => 'required|string',
        ]);

        $resultados = [];

        foreach ($validated['personas_solicitadas'] as $personaSolicitada) {
            $personaRegistrada = PersonaRegistrada::where('dni', $personaSolicitada['dni'])->first();

            $resultado = [
                'persona_solicitada_id' => $personaSolicitada['id'],
                'nombres' => $personaSolicitada['nombres'],
                'apellidos' => $personaSolicitada['apellidos'],
                'dni' => $personaSolicitada['dni'],
                'encontrada' => $personaRegistrada !== null,
            ];

            if ($personaRegistrada) {
                $resultado['grupo_delictivo'] = $personaRegistrada->grupo_delictivo;
                $resultado['estructura_criminal'] = $personaRegistrada->estructura_criminal;
                $resultado['observaciones'] = $personaRegistrada->observaciones;
            }

            $resultados[] = $resultado;
        }

        return response()->json([
            'resultados' => $resultados,
        ]);
    }

    private function obtenerSiguienteCorrelativo(int $anio): int
    {
        $correlativoActual = ConfiguracionSistema::obtenerValor("correlativo_{$anio}", 0);

        return (int) $correlativoActual + 1;
    }
}
