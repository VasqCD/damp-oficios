<?php

namespace App\Http\Controllers;

use App\Models\PersonaRegistrada;
use App\Models\RespuestaOficio;
use App\Models\SolicitudOficio;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $estadisticas = [
            'solicitudes_pendientes' => SolicitudOficio::where('estado', 'pendiente')->count(),
            'solicitudes_en_proceso' => SolicitudOficio::where('estado', 'en_proceso')->count(),
            'solicitudes_respondidas' => SolicitudOficio::where('estado', 'respondida')->count(),
            'total_solicitudes' => SolicitudOficio::count(),
            'total_respuestas' => RespuestaOficio::count(),
            'total_personas_registradas' => PersonaRegistrada::where('activo', true)->count(),
            'solicitudes_mes_actual' => SolicitudOficio::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'respuestas_mes_actual' => RespuestaOficio::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $solicitudesRecientes = SolicitudOficio::with(['institucion', 'unidad', 'delito'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $respuestasRecientes = RespuestaOficio::with(['solicitudOficio.institucion', 'analista'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'estadisticas' => $estadisticas,
            'solicitudesRecientes' => $solicitudesRecientes,
            'respuestasRecientes' => $respuestasRecientes,
        ]);
    }
}
