<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ArrowLeft, Pencil, FileText } from 'lucide-vue-next';

interface Cargo {
    id: number;
    nombre: string;
}

interface AgenteSolicitante {
    id: number;
    nombres: string;
    apellidos: string;
    cargo: Cargo;
}

interface Institucion {
    id: number;
    nombre: string;
}

interface Unidad {
    id: number;
    nombre: string;
    ciudad: string;
}

interface Delito {
    id: number;
    nombre: string;
}

interface UsuarioRegistro {
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

interface RespuestaOficio {
    id: number;
    numero_oficio_respuesta: string;
    fecha_respuesta: string;
    estado: string;
}

interface SolicitudOficio {
    id: number;
    numero_oficio_entrante: string | null;
    fecha_recepcion: string;
    institucion: Institucion | null;
    unidad: Unidad | null;
    agente_solicitante: AgenteSolicitante | null;
    delito: Delito | null;
    ofendido: string | null;
    observaciones: string | null;
    estado: string;
    usuario_registro: UsuarioRegistro | null;
    personas_solicitadas: PersonaSolicitada[];
    respuesta_oficio: RespuestaOficio | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    solicitud: SolicitudOficio;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Solicitudes',
        href: '/solicitudes',
    },
    {
        title: 'Detalle de Solicitud',
    },
];

function getEstadoBadge(estado: string) {
    const badges = {
        pendiente: 'destructive',
        en_proceso: 'default',
        respondida: 'secondary',
    };
    return badges[estado as keyof typeof badges] || 'default';
}

function getEstadoLabel(estado: string) {
    const labels = {
        pendiente: 'Pendiente',
        en_proceso: 'En Proceso',
        respondida: 'Respondida',
    };
    return labels[estado as keyof typeof labels] || estado;
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function formatDateTime(date: string) {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head title="Detalle de Solicitud" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold tracking-tight">
                            {{ solicitud.numero_oficio_entrante || `Solicitud #${solicitud.id}` }}
                        </h2>
                        <Badge :variant="getEstadoBadge(solicitud.estado)">
                            {{ getEstadoLabel(solicitud.estado) }}
                        </Badge>
                    </div>
                    <p class="text-muted-foreground">
                        Solicitud recibida el {{ formatDate(solicitud.fecha_recepcion) }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link
                        v-if="solicitud.estado !== 'respondida'"
                        :href="`/solicitudes/${solicitud.id}/edit`"
                    >
                        <Button variant="outline">
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <Link
                        v-if="solicitud.estado === 'pendiente'"
                        :href="`/solicitudes/${solicitud.id}/responder`"
                    >
                        <Button>
                            <FileText class="mr-2 h-4 w-4" />
                            Generar Respuesta
                        </Button>
                    </Link>
                    <Link href="/solicitudes">
                        <Button variant="outline">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Información de la Solicitud</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="solicitud.numero_oficio_entrante">
                            <p class="text-sm font-medium text-muted-foreground">Número de Oficio</p>
                            <p class="text-lg">{{ solicitud.numero_oficio_entrante }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Fecha de Recepción</p>
                            <p class="text-lg">{{ formatDate(solicitud.fecha_recepcion) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Estado</p>
                            <div class="mt-1">
                                <Badge :variant="getEstadoBadge(solicitud.estado)">
                                    {{ getEstadoLabel(solicitud.estado) }}
                                </Badge>
                            </div>
                        </div>
                        <div v-if="solicitud.delito">
                            <p class="text-sm font-medium text-muted-foreground">Delito</p>
                            <p class="text-lg">{{ solicitud.delito.nombre }}</p>
                        </div>
                        <div v-if="solicitud.ofendido">
                            <p class="text-sm font-medium text-muted-foreground">Ofendido</p>
                            <p class="text-lg">{{ solicitud.ofendido }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Institución Solicitante</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="solicitud.institucion">
                            <p class="text-sm font-medium text-muted-foreground">Institución</p>
                            <p class="text-lg">{{ solicitud.institucion.nombre }}</p>
                        </div>
                        <div v-if="solicitud.unidad">
                            <p class="text-sm font-medium text-muted-foreground">Unidad</p>
                            <p class="text-lg">
                                {{ solicitud.unidad.nombre }} - {{ solicitud.unidad.ciudad }}
                            </p>
                        </div>
                        <div v-if="solicitud.agente_solicitante">
                            <p class="text-sm font-medium text-muted-foreground">Agente Solicitante</p>
                            <p class="text-lg">
                                {{ solicitud.agente_solicitante.nombres }}
                                {{ solicitud.agente_solicitante.apellidos }}
                            </p>
                            <p v-if="solicitud.agente_solicitante.cargo" class="text-sm text-muted-foreground">
                                {{ solicitud.agente_solicitante.cargo.nombre }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>
                            Personas Solicitadas ({{ solicitud.personas_solicitadas.length }})
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nombres</TableHead>
                                        <TableHead>Apellidos</TableHead>
                                        <TableHead>DNI</TableHead>
                                        <TableHead>Fecha de Nacimiento</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-for="persona in solicitud.personas_solicitadas"
                                        :key="persona.id"
                                    >
                                        <TableCell class="font-medium">{{ persona.nombres }}</TableCell>
                                        <TableCell>{{ persona.apellidos }}</TableCell>
                                        <TableCell>{{ persona.dni }}</TableCell>
                                        <TableCell>
                                            {{
                                                persona.fecha_nacimiento
                                                    ? formatDate(persona.fecha_nacimiento)
                                                    : 'No especificada'
                                            }}
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="solicitud.observaciones" class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Observaciones</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">{{ solicitud.observaciones }}</p>
                    </CardContent>
                </Card>

                <Card v-if="solicitud.respuesta_oficio" class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Respuesta Generada</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">
                                    Número de Respuesta
                                </p>
                                <p class="text-lg">
                                    {{ solicitud.respuesta_oficio.numero_oficio_respuesta }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Fecha de Respuesta</p>
                                <p class="text-lg">
                                    {{ formatDate(solicitud.respuesta_oficio.fecha_respuesta) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Estado</p>
                                <div class="mt-1">
                                    <Badge>{{ solicitud.respuesta_oficio.estado }}</Badge>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Link :href="`/respuestas/${solicitud.respuesta_oficio.id}`">
                                <Button variant="outline" size="sm">Ver Detalle de Respuesta</Button>
                            </Link>
                            <Link :href="`/respuestas/${solicitud.respuesta_oficio.id}/pdf`">
                                <Button variant="outline" size="sm">Descargar PDF</Button>
                            </Link>
                        </div>
                    </CardContent>
                </Card>

                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Información del Sistema</CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-3">
                        <div v-if="solicitud.usuario_registro">
                            <p class="text-sm font-medium text-muted-foreground">Registrado por</p>
                            <p class="text-lg">{{ solicitud.usuario_registro.name }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ solicitud.usuario_registro.email }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Fecha de Registro</p>
                            <p class="text-lg">{{ formatDateTime(solicitud.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Última Actualización</p>
                            <p class="text-lg">{{ formatDateTime(solicitud.updated_at) }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
