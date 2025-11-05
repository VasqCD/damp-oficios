<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ArrowLeft, Pencil } from 'lucide-vue-next';

interface Institucion {
    id: number;
    nombre: string;
}

interface Unidad {
    id: number;
    nombre: string;
}

interface SolicitudOficio {
    id: number;
    numero_oficio_entrante: string | null;
    fecha_recepcion: string;
    institucion: Institucion;
    unidad: Unidad | null;
    estado: string;
}

interface Delito {
    id: number;
    nombre: string;
    descripcion: string | null;
    activo: boolean;
    solicitudes_oficios: SolicitudOficio[];
}

interface Props {
    delito: Delito;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Delitos',
        href: '/delitos',
    },
    {
        title: 'Detalle de Delito',
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
</script>

<template>
    <Head title="Detalle de Delito" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold tracking-tight">
                            {{ delito.nombre }}
                        </h2>
                        <Badge :variant="delito.activo ? 'default' : 'secondary'">
                            {{ delito.activo ? 'Activo' : 'Inactivo' }}
                        </Badge>
                    </div>
                    <p class="text-muted-foreground">Tipo de delito</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="`/delitos/${delito.id}/edit`">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <Link href="/delitos">
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
                        <CardTitle>Información del Delito</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Nombre</p>
                            <p class="text-lg">{{ delito.nombre }}</p>
                        </div>
                        <div v-if="delito.descripcion">
                            <p class="text-sm font-medium text-muted-foreground">Descripción</p>
                            <p class="text-lg">{{ delito.descripcion }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Estado</p>
                            <div class="mt-1">
                                <Badge :variant="delito.activo ? 'default' : 'secondary'">
                                    {{ delito.activo ? 'Activo' : 'Inactivo' }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Estadísticas</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Total de Solicitudes</p>
                            <p class="text-3xl font-bold">{{ delito.solicitudes_oficios.length }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>
                            Últimas Solicitudes con este Delito ({{ delito.solicitudes_oficios.length }})
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="delito.solicitudes_oficios.length > 0" class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Número de Oficio</TableHead>
                                        <TableHead>Fecha</TableHead>
                                        <TableHead>Institución</TableHead>
                                        <TableHead>Unidad</TableHead>
                                        <TableHead class="text-center">Estado</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-for="solicitud in delito.solicitudes_oficios"
                                        :key="solicitud.id"
                                    >
                                        <TableCell class="font-medium">
                                            {{ solicitud.numero_oficio_entrante || `#${solicitud.id}` }}
                                        </TableCell>
                                        <TableCell>
                                            {{ formatDate(solicitud.fecha_recepcion) }}
                                        </TableCell>
                                        <TableCell>
                                            {{ solicitud.institucion.nombre }}
                                        </TableCell>
                                        <TableCell>
                                            {{ solicitud.unidad?.nombre || '-' }}
                                        </TableCell>
                                        <TableCell class="text-center">
                                            <Badge :variant="getEstadoBadge(solicitud.estado)">
                                                {{ getEstadoLabel(solicitud.estado) }}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                        <div
                            v-else
                            class="flex h-32 items-center justify-center text-muted-foreground"
                        >
                            No hay solicitudes registradas con este delito
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
