<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Pencil } from 'lucide-vue-next';

interface Cargo {
    id: number;
    nombre: string;
}

interface Unidad {
    id: number;
    nombre: string;
    ciudad: string;
    institucion: {
        id: number;
        nombre: string;
    };
}

interface Agente {
    id: number;
    nombres: string;
    apellidos: string;
    cargo: Cargo;
    unidad: Unidad;
    tipo: string;
    activo: boolean;
    created_at: string;
    updated_at: string;
}

interface Props {
    agente: Agente;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Agentes',
        href: '/agentes',
    },
    {
        title: 'Detalle del Agente',
    },
];

function getTipoBadge(tipo: string) {
    return tipo === 'solicitante' ? 'default' : 'secondary';
}

function formatDate(date: string) {
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
    <Head title="Detalle del Agente" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">
                        {{ agente.nombres }} {{ agente.apellidos }}
                    </h2>
                    <p class="text-muted-foreground">
                        Información detallada del agente
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link :href="`/agentes/${agente.id}/edit`">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <Link href="/agentes">
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
                        <CardTitle>Información Personal</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Nombres</p>
                            <p class="text-lg">{{ agente.nombres }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Apellidos</p>
                            <p class="text-lg">{{ agente.apellidos }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Cargo</p>
                            <p class="text-lg">{{ agente.cargo.nombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Tipo</p>
                            <div class="mt-1">
                                <Badge :variant="getTipoBadge(agente.tipo)">
                                    {{ agente.tipo === 'solicitante' ? 'Solicitante' : 'Firmante' }}
                                </Badge>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Estado</p>
                            <div class="mt-1">
                                <Badge :variant="agente.activo ? 'default' : 'secondary'">
                                    {{ agente.activo ? 'Activo' : 'Inactivo' }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Información Institucional</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Institución</p>
                            <p class="text-lg">{{ agente.unidad.institucion.nombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Unidad</p>
                            <p class="text-lg">{{ agente.unidad.nombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Ciudad</p>
                            <p class="text-lg">{{ agente.unidad.ciudad }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Información del Sistema</CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Fecha de Registro</p>
                            <p class="text-lg">{{ formatDate(agente.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Última Actualización</p>
                            <p class="text-lg">{{ formatDate(agente.updated_at) }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
