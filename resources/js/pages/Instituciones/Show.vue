<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ArrowLeft, Pencil, Building2, Users } from 'lucide-vue-next';

interface Unidad {
    id: number;
    nombre: string;
    activo: boolean;
    agentes_count?: number;
}

interface SolicitudOficio {
    id: number;
    numero_oficio_entrante: string;
    fecha_oficio: string;
    estado: string;
    delito?: {
        nombre: string;
    };
}

interface Institucion {
    id: number;
    nombre: string;
    nombre_completo: string;
    activo: boolean;
    created_at: string;
    updated_at: string;
    unidades: Unidad[];
    solicitudes_oficios: SolicitudOficio[];
}

interface Props {
    institucion: Institucion;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Instituciones',
        href: '/instituciones',
    },
    {
        title: props.institucion.nombre,
    },
];

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-HN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}
</script>

<template>
    <Head :title="institucion.nombre" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">{{ institucion.nombre }}</h2>
                    <p class="text-muted-foreground">
                        {{ institucion.nombre_completo }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link href="/instituciones">
                        <Button variant="outline">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver
                        </Button>
                    </Link>
                    <Link :href="`/instituciones/${institucion.id}/edit`">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Información General</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Nombre (Siglas)</p>
                            <p class="text-lg font-semibold">{{ institucion.nombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Nombre Completo</p>
                            <p class="text-lg">{{ institucion.nombre_completo }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Estado</p>
                            <Badge :variant="institucion.activo ? 'default' : 'secondary'" class="mt-1">
                                {{ institucion.activo ? 'Activo' : 'Inactivo' }}
                            </Badge>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Fecha de Registro</p>
                            <p class="text-sm">{{ formatDate(institucion.created_at) }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Estadísticas</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Building2 class="h-5 w-5 text-muted-foreground" />
                                <span class="font-medium">Unidades</span>
                            </div>
                            <Badge variant="secondary">
                                {{ institucion.unidades.length }}
                            </Badge>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Users class="h-5 w-5 text-muted-foreground" />
                                <span class="font-medium">Solicitudes</span>
                            </div>
                            <Badge variant="secondary">
                                {{ institucion.solicitudes_oficios.length }}
                            </Badge>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Unidades Adscritas</CardTitle>
                    <CardDescription>
                        Listado de unidades pertenecientes a esta institución
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nombre</TableHead>
                                    <TableHead class="text-center">Agentes</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="unidad in institucion.unidades" :key="unidad.id">
                                    <TableCell class="font-medium">
                                        {{ unidad.nombre }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="secondary">
                                            {{ unidad.agentes_count || 0 }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="unidad.activo ? 'default' : 'secondary'">
                                            {{ unidad.activo ? 'Activa' : 'Inactiva' }}
                                        </Badge>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="institucion.unidades.length === 0">
                                    <TableCell colspan="3" class="text-center text-muted-foreground">
                                        No hay unidades registradas
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Solicitudes Recientes</CardTitle>
                    <CardDescription>
                        Últimas 10 solicitudes realizadas por esta institución
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Número de Oficio</TableHead>
                                    <TableHead>Fecha</TableHead>
                                    <TableHead>Delito</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="solicitud in institucion.solicitudes_oficios" :key="solicitud.id">
                                    <TableCell class="font-medium">
                                        {{ solicitud.numero_oficio_entrante }}
                                    </TableCell>
                                    <TableCell>
                                        {{ formatDate(solicitud.fecha_oficio) }}
                                    </TableCell>
                                    <TableCell>
                                        {{ solicitud.delito?.nombre || 'N/A' }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge
                                            :variant="
                                                solicitud.estado === 'respondida'
                                                    ? 'default'
                                                    : solicitud.estado === 'en_proceso'
                                                      ? 'secondary'
                                                      : 'outline'
                                            "
                                        >
                                            {{ solicitud.estado }}
                                        </Badge>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="institucion.solicitudes_oficios.length === 0">
                                    <TableCell colspan="4" class="text-center text-muted-foreground">
                                        No hay solicitudes registradas
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
