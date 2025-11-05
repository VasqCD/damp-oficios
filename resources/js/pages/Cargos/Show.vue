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

interface Unidad {
    id: number;
    nombre: string;
}

interface Agente {
    id: number;
    nombres: string;
    apellidos: string;
    unidad: Unidad;
    tipo: string;
    activo: boolean;
}

interface Cargo {
    id: number;
    nombre: string;
    nivel_jerarquico: number | null;
    activo: boolean;
    agentes: Agente[];
}

interface Props {
    cargo: Cargo;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Cargos',
        href: '/cargos',
    },
    {
        title: 'Detalle de Cargo',
    },
];
</script>

<template>
    <Head title="Detalle de Cargo" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold tracking-tight">
                            {{ cargo.nombre }}
                        </h2>
                        <Badge :variant="cargo.activo ? 'default' : 'secondary'">
                            {{ cargo.activo ? 'Activo' : 'Inactivo' }}
                        </Badge>
                    </div>
                    <p class="text-muted-foreground">
                        Cargo policial
                        {{
                            cargo.nivel_jerarquico
                                ? `- Nivel jerárquico: ${cargo.nivel_jerarquico}`
                                : ''
                        }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="`/cargos/${cargo.id}/edit`">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <Link href="/cargos">
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
                        <CardTitle>Información del Cargo</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Nombre</p>
                            <p class="text-lg">{{ cargo.nombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Nivel Jerárquico</p>
                            <p class="text-lg">{{ cargo.nivel_jerarquico || 'No especificado' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Estado</p>
                            <div class="mt-1">
                                <Badge :variant="cargo.activo ? 'default' : 'secondary'">
                                    {{ cargo.activo ? 'Activo' : 'Inactivo' }}
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
                            <p class="text-3xl font-bold">{{ cargo.agentes.length }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Agentes Activos</p>
                            <p class="text-3xl font-bold">
                                {{ cargo.agentes.filter((a) => a.activo).length }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Agentes con este Cargo ({{ cargo.agentes.length }})</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="cargo.agentes.length > 0" class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Nombre</TableHead>
                                        <TableHead>Unidad</TableHead>
                                        <TableHead class="text-center">Tipo</TableHead>
                                        <TableHead class="text-center">Estado</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="agente in cargo.agentes" :key="agente.id">
                                        <TableCell class="font-medium">
                                            {{ agente.nombres }} {{ agente.apellidos }}
                                        </TableCell>
                                        <TableCell>
                                            {{ agente.unidad.nombre }}
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
                            No hay agentes con este cargo
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
