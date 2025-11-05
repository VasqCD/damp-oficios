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

interface Cargo {
    id: number;
    nombre: string;
}

interface Agente {
    id: number;
    nombres: string;
    apellidos: string;
    cargo: Cargo;
    tipo: string;
    activo: boolean;
}

interface Unidad {
    id: number;
    nombre: string;
    ciudad: string | null;
    departamento: string | null;
    institucion: Institucion;
    activo: boolean;
    agentes: Agente[];
}

interface Props {
    unidad: Unidad;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Unidades',
        href: '/unidades',
    },
    {
        title: 'Detalle de Unidad',
    },
];
</script>

<template>
    <Head title="Detalle de Unidad" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold tracking-tight">
                            {{ unidad.nombre }}
                        </h2>
                        <Badge :variant="unidad.activo ? 'default' : 'secondary'">
                            {{ unidad.activo ? 'Activo' : 'Inactivo' }}
                        </Badge>
                    </div>
                    <p class="text-muted-foreground">{{ unidad.institucion.nombre }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="`/unidades/${unidad.id}/edit`">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <Link href="/unidades">
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
                        <CardTitle>Información de la Unidad</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Nombre</p>
                            <p class="text-lg">{{ unidad.nombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Institución</p>
                            <p class="text-lg">{{ unidad.institucion.nombre }}</p>
                        </div>
                        <div v-if="unidad.ciudad">
                            <p class="text-sm font-medium text-muted-foreground">Ciudad</p>
                            <p class="text-lg">{{ unidad.ciudad }}</p>
                        </div>
                        <div v-if="unidad.departamento">
                            <p class="text-sm font-medium text-muted-foreground">Departamento</p>
                            <p class="text-lg">{{ unidad.departamento }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Estado</p>
                            <div class="mt-1">
                                <Badge :variant="unidad.activo ? 'default' : 'secondary'">
                                    {{ unidad.activo ? 'Activo' : 'Inactivo' }}
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
                            <p class="text-sm font-medium text-muted-foreground">Total de Agentes</p>
                            <p class="text-3xl font-bold">{{ unidad.agentes.length }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Agentes Activos</p>
                            <p class="text-3xl font-bold">
                                {{ unidad.agentes.filter((a) => a.activo).length }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Agentes Adscritos ({{ unidad.agentes.length }})</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="unidad.agentes.length > 0" class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nombre</TableHead>
                                        <TableHead>Cargo</TableHead>
                                        <TableHead class="text-center">Tipo</TableHead>
                                        <TableHead class="text-center">Estado</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="agente in unidad.agentes" :key="agente.id">
                                        <TableCell class="font-medium">
                                            {{ agente.nombres }} {{ agente.apellidos }}
                                        </TableCell>
                                        <TableCell>
                                            {{ agente.cargo.nombre }}
                                        </TableCell>
                                        <TableCell class="text-center">
                                            <Badge
                                                :variant="
                                                    agente.tipo === 'solicitante'
                                                        ? 'default'
                                                        : 'secondary'
                                                "
                                            >
                                                {{
                                                    agente.tipo === 'solicitante'
                                                        ? 'Solicitante'
                                                        : 'Firmante'
                                                }}
                                            </Badge>
                                        </TableCell>
                                        <TableCell class="text-center">
                                            <Badge :variant="agente.activo ? 'default' : 'secondary'">
                                                {{ agente.activo ? 'Activo' : 'Inactivo' }}
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
                            No hay agentes adscritos a esta unidad
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
