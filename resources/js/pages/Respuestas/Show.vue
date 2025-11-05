<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ArrowLeft, Pencil, FileText, CheckCircle2, XCircle } from 'lucide-vue-next';

interface Cargo {
    nombre: string;
}

interface Institucion {
    id: number;
    nombre: string;
}

interface Unidad {
    id: number;
    nombre: string;
}

interface Delito {
    id: number;
    nombre: string;
}

interface AgenteSolicitante {
    id: number;
    nombres: string;
    apellidos: string;
    cargo: Cargo;
}

interface SolicitudOficio {
    id: number;
    numero_oficio_entrante: string | null;
    fecha_recepcion: string;
    institucion: Institucion;
    unidad: Unidad | null;
    agente_solicitante: AgenteSolicitante;
    delito: Delito | null;
    ofendido: string | null;
    observaciones: string | null;
}

interface Usuario {
    id: number;
    name: string;
    email: string;
}

interface PersonaSolicitada {
    id: number;
    nombres: string;
    apellidos: string;
    dni: string;
    fecha_nacimiento: string | null;
}

interface PersonaRegistrada {
    id: number;
    nombres: string;
    apellidos: string;
    dni: string;
    grupo_delictivo: string | null;
    estructura_criminal: string | null;
    observaciones: string | null;
    foto: string | null;
}

interface ResultadoConsulta {
    id: number;
    persona_solicitada: PersonaSolicitada;
    persona_registrada: PersonaRegistrada | null;
    encontrada: boolean;
    detalles: {
        grupo_delictivo?: string;
        estructura_criminal?: string;
        observaciones?: string;
    } | null;
}

interface RespuestaOficio {
    id: number;
    numero_oficio_respuesta: string;
    correlativo: number;
    anio: number;
    fecha_respuesta: string;
    estado: string;
    contenido_respuesta: string | null;
    solicitud_oficio: SolicitudOficio;
    analista: Usuario;
    jefe_regional: Usuario;
    resultados_consulta: ResultadoConsulta[];
    created_at: string;
    updated_at: string;
}

interface Props {
    respuesta: RespuestaOficio;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Respuestas',
        href: '/respuestas',
    },
    {
        title: 'Detalle de Respuesta',
    },
];

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function getEstadoBadge(estado: string) {
    const badges = {
        borrador: 'secondary',
        firmada: 'default',
        enviada: 'outline',
    };
    return badges[estado as keyof typeof badges] || 'secondary';
}

function getEstadoLabel(estado: string) {
    const labels = {
        borrador: 'Borrador',
        firmada: 'Firmada',
        enviada: 'Enviada',
    };
    return labels[estado as keyof typeof labels] || estado;
}

const personasEncontradas = props.respuesta.resultados_consulta.filter((r) => r.encontrada).length;
const personasNoEncontradas = props.respuesta.resultados_consulta.filter((r) => !r.encontrada).length;
</script>

<template>
    <Head title="Detalle de Respuesta" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold tracking-tight">
                            {{ respuesta.numero_oficio_respuesta }}
                        </h2>
                        <Badge :variant="getEstadoBadge(respuesta.estado)">
                            {{ getEstadoLabel(respuesta.estado) }}
                        </Badge>
                    </div>
                    <p class="text-muted-foreground">Respuesta de Oficio</p>
                </div>
                <div class="flex gap-2">
                    <Link
                        v-if="respuesta.estado === 'borrador'"
                        :href="`/respuestas/${respuesta.id}/edit`"
                    >
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <a :href="`/respuestas/${respuesta.id}/pdf`" target="_blank" rel="noopener noreferrer">
                        <Button variant="outline">
                            <FileText class="mr-2 h-4 w-4" />
                            Ver PDF
                        </Button>
                    </a>
                    <Link href="/respuestas">
                        <Button variant="outline">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Información de la Respuesta -->
                <Card>
                    <CardHeader>
                        <CardTitle>Información de la Respuesta</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Número de Oficio</p>
                            <p class="text-lg font-semibold">{{ respuesta.numero_oficio_respuesta }}</p>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Correlativo</p>
                                <p class="text-lg">{{ respuesta.correlativo }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Año</p>
                                <p class="text-lg">{{ respuesta.anio }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Fecha de Respuesta</p>
                            <p class="text-lg">{{ formatDate(respuesta.fecha_respuesta) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Estado</p>
                            <div class="mt-1">
                                <Badge :variant="getEstadoBadge(respuesta.estado)">
                                    {{ getEstadoLabel(respuesta.estado) }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Personal Responsable -->
                <Card>
                    <CardHeader>
                        <CardTitle>Personal Responsable</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Analista</p>
                            <p class="text-lg">{{ respuesta.analista.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ respuesta.analista.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Jefe Regional Firmante</p>
                            <p class="text-lg">{{ respuesta.jefe_regional.name }}</p>
                            <p class="text-sm text-muted-foreground">{{ respuesta.jefe_regional.email }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Información de la Solicitud -->
            <Card>
                <CardHeader>
                    <CardTitle>Solicitud de Origen</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Número de Oficio Entrante</p>
                            <p class="text-lg font-semibold">
                                {{ respuesta.solicitud_oficio.numero_oficio_entrante || `#${respuesta.solicitud_oficio.id}` }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Fecha de Recepción</p>
                            <p class="text-lg">{{ formatDate(respuesta.solicitud_oficio.fecha_recepcion) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Institución</p>
                            <p class="text-lg">{{ respuesta.solicitud_oficio.institucion.nombre }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Agente Solicitante</p>
                            <p class="text-base">
                                {{ respuesta.solicitud_oficio.agente_solicitante.nombres }}
                                {{ respuesta.solicitud_oficio.agente_solicitante.apellidos }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {{ respuesta.solicitud_oficio.agente_solicitante.cargo.nombre }}
                            </p>
                        </div>
                        <div v-if="respuesta.solicitud_oficio.unidad">
                            <p class="text-sm font-medium text-muted-foreground">Unidad</p>
                            <p class="text-base">{{ respuesta.solicitud_oficio.unidad.nombre }}</p>
                        </div>
                        <div v-if="respuesta.solicitud_oficio.delito">
                            <p class="text-sm font-medium text-muted-foreground">Delito</p>
                            <p class="text-base">{{ respuesta.solicitud_oficio.delito.nombre }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <Link :href="`/solicitudes/${respuesta.solicitud_oficio.id}`">
                            <Button variant="outline" size="sm">
                                Ver Solicitud Completa
                            </Button>
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <!-- Contenido/Observaciones -->
            <Card v-if="respuesta.contenido_respuesta">
                <CardHeader>
                    <CardTitle>Contenido / Observaciones</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="whitespace-pre-wrap text-base">{{ respuesta.contenido_respuesta }}</p>
                </CardContent>
            </Card>

            <!-- Resultados de Consulta -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Resultados de Consulta</CardTitle>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Se consultaron {{ respuesta.resultados_consulta.length }} persona(s)
                            </p>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex items-center gap-2">
                                <CheckCircle2 class="h-5 w-5 text-green-600" />
                                <span class="font-medium">{{ personasEncontradas }} encontradas</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <XCircle class="h-5 w-5 text-destructive" />
                                <span class="font-medium">{{ personasNoEncontradas }} no encontradas</span>
                            </div>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="respuesta.resultados_consulta.length > 0" class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>DNI</TableHead>
                                    <TableHead>Nombres y Apellidos</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                    <TableHead>Información Encontrada</TableHead>
                                    <TableHead class="text-center">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="resultado in respuesta.resultados_consulta"
                                    :key="resultado.id"
                                >
                                    <TableCell class="font-mono">
                                        {{ resultado.persona_solicitada.dni }}
                                    </TableCell>
                                    <TableCell class="font-medium">
                                        {{ resultado.persona_solicitada.nombres }}
                                        {{ resultado.persona_solicitada.apellidos }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="resultado.encontrada ? 'default' : 'secondary'">
                                            <CheckCircle2 v-if="resultado.encontrada" class="mr-1 h-3 w-3" />
                                            <XCircle v-else class="mr-1 h-3 w-3" />
                                            {{ resultado.encontrada ? 'Encontrada' : 'No Encontrada' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div v-if="resultado.encontrada && resultado.detalles" class="space-y-1 text-sm">
                                            <p v-if="resultado.detalles.grupo_delictivo">
                                                <span class="font-medium">Grupo:</span>
                                                {{ resultado.detalles.grupo_delictivo }}
                                            </p>
                                            <p v-if="resultado.detalles.estructura_criminal">
                                                <span class="font-medium">Estructura:</span>
                                                {{ resultado.detalles.estructura_criminal }}
                                            </p>
                                            <p v-if="resultado.detalles.observaciones" class="text-muted-foreground">
                                                {{ resultado.detalles.observaciones }}
                                            </p>
                                        </div>
                                        <span v-else class="text-sm text-muted-foreground">
                                            Sin antecedentes en el sistema
                                        </span>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Link
                                            v-if="resultado.persona_registrada"
                                            :href="`/personas-registradas/${resultado.persona_registrada.id}`"
                                        >
                                            <Button variant="ghost" size="sm">
                                                Ver Perfil
                                            </Button>
                                        </Link>
                                        <span v-else class="text-sm text-muted-foreground">-</span>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                    <div
                        v-else
                        class="flex h-32 items-center justify-center text-muted-foreground"
                    >
                        No hay resultados de consulta
                    </div>
                </CardContent>
            </Card>

            <!-- Información de Auditoría -->
            <Card>
                <CardHeader>
                    <CardTitle>Información de Auditoría</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Fecha de Creación</p>
                            <p class="text-base">{{ formatDate(respuesta.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Última Modificación</p>
                            <p class="text-base">{{ formatDate(respuesta.updated_at) }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
