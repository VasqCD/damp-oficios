<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FileText, Users, CheckCircle, Clock, AlertCircle, Send } from 'lucide-vue-next';

interface Props {
    estadisticas: {
        total_solicitudes: number;
        solicitudes_pendientes: number;
        solicitudes_en_proceso: number;
        solicitudes_respondidas: number;
        total_respuestas: number;
        respuestas_borrador: number;
        respuestas_firmadas: number;
        respuestas_enviadas: number;
        total_personas_registradas: number;
        instituciones_activas: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Dashboard</h2>
                <p class="text-muted-foreground">
                    Vista general del sistema de gestión de oficios
                </p>
            </div>

            <!-- Solicitudes -->
            <div>
                <h3 class="mb-4 text-xl font-semibold">Solicitudes de Oficios</h3>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Total Solicitudes
                            </CardTitle>
                            <FileText class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ estadisticas.total_solicitudes }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Solicitudes registradas
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Pendientes
                            </CardTitle>
                            <Clock class="h-4 w-4 text-yellow-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ estadisticas.solicitudes_pendientes }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Esperando procesamiento
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                En Proceso
                            </CardTitle>
                            <AlertCircle class="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ estadisticas.solicitudes_en_proceso }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Siendo atendidas
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Respondidas
                            </CardTitle>
                            <CheckCircle class="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ estadisticas.solicitudes_respondidas }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Completadas
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Respuestas -->
            <div>
                <h3 class="mb-4 text-xl font-semibold">Respuestas de Oficios</h3>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Total Respuestas
                            </CardTitle>
                            <FileText class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ estadisticas.total_respuestas }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Respuestas generadas
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Borradores
                            </CardTitle>
                            <Clock class="h-4 w-4 text-gray-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ estadisticas.respuestas_borrador }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                En edición
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Firmadas
                            </CardTitle>
                            <CheckCircle class="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ estadisticas.respuestas_firmadas }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Listas para enviar
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Enviadas
                            </CardTitle>
                            <Send class="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ estadisticas.respuestas_enviadas }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Entregadas
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Información General -->
            <div>
                <h3 class="mb-4 text-xl font-semibold">Información General</h3>
                <div class="grid gap-4 md:grid-cols-2">
                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Personas Registradas
                            </CardTitle>
                            <Users class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ estadisticas.total_personas_registradas }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                En la base de datos
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle class="text-sm font-medium">
                                Instituciones Activas
                            </CardTitle>
                            <FileText class="h-4 w-4 text-muted-foreground" />
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">
                                {{ estadisticas.instituciones_activas }}
                            </div>
                            <p class="text-xs text-muted-foreground">
                                Instituciones registradas
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
