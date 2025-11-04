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
            // Solicitudes
            'total_solicitudes' => SolicitudOficio::count(),
            'solicitudes_pendientes' => SolicitudOficio::where('estado', 'pendiente')->count(),
            'solicitudes_en_proceso' => SolicitudOficio::where('estado', 'en_proceso')->count(),
            'solicitudes_respondidas' => SolicitudOficio::where('estado', 'respondida')->count(),

            // Respuestas
            'total_respuestas' => RespuestaOficio::count(),
            'respuestas_borrador' => RespuestaOficio::where('estado', 'borrador')->count(),
            'respuestas_firmadas' => RespuestaOficio::where('estado', 'firmado')->count(),
            'respuestas_enviadas' => RespuestaOficio::where('estado', 'enviado')->count(),

            // Información general
            'total_personas_registradas' => PersonaRegistrada::where('activo', true)->count(),
            'instituciones_activas' => \App\Models\Institucion::where('activo', true)->count(),
        ];

        return Inertia::render('Dashboard', [
            'estadisticas' => $estadisticas,
        ]);
    }
}
