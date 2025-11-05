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

interface ResultadoConsulta {
    id: number;
    encontrada: boolean;
    created_at: string;
    respuesta_oficio: {
        id: number;
        numero_oficio_respuesta: string;
        solicitud_oficio: {
            numero_oficio_entrante: string | null;
            institucion: {
                nombre: string;
            };
        };
    };
}

interface PersonaRegistrada {
    id: number;
    nombres: string;
    apellidos: string;
    dni: string;
    fecha_nacimiento: string | null;
    grupo_delictivo: string | null;
    estructura_criminal: string | null;
    observaciones: string | null;
    foto: string | null;
    activo: boolean;
    created_at: string;
    updated_at: string;
    resultados_consulta: ResultadoConsulta[];
}

interface Props {
    persona: PersonaRegistrada;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Personas Registradas',
        href: '/personas-registradas',
    },
    {
        title: 'Detalle de Persona',
    },
];

function formatDate(date: string | null) {
    if (!date) return 'No especificada';
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
    <Head title="Detalle de Persona Registrada" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold tracking-tight">
                            {{ persona.nombres }} {{ persona.apellidos }}
                        </h2>
                        <Badge :variant="persona.activo ? 'default' : 'secondary'">
                            {{ persona.activo ? 'Activo' : 'Inactivo' }}
                        </Badge>
                    </div>
                    <p class="text-muted-foreground">DNI: {{ persona.dni }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="`/personas-registradas/${persona.id}/edit`">
                        <Button>
                            <Pencil class="mr-2 h-4 w-4" />
                            Editar
                        </Button>
                    </Link>
                    <Link href="/personas-registradas">
                        <Button variant="outline">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Volver
                        </Button>
                    </Link>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <Card v-if="persona.foto" class="md:row-span-2">
                    <CardHeader>
                        <CardTitle>Fotografía</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <img
                            :src="`/storage/${persona.foto}`"
                            :alt="`${persona.nombres} ${persona.apellidos}`"
                            class="w-full rounded-md object-cover"
                        />
                    </CardContent>
                </Card>

                <Card :class="persona.foto ? 'md:col-span-2' : 'md:col-span-3'">
                    <CardHeader>
                        <CardTitle>Información Personal</CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Nombres</p>
                            <p class="text-lg">{{ persona.nombres }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Apellidos</p>
                            <p class="text-lg">{{ persona.apellidos }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">DNI</p>
                            <p class="text-lg">{{ persona.dni }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">
                                Fecha de Nacimiento
                            </p>
                            <p class="text-lg">{{ formatDate(persona.fecha_nacimiento) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Estado</p>
                            <div class="mt-1">
                                <Badge :variant="persona.activo ? 'default' : 'secondary'">
                                    {{ persona.activo ? 'Activo' : 'Inactivo' }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card :class="persona.foto ? 'md:col-span-2' : 'md:col-span-3'">
                    <CardHeader>
                        <CardTitle>Información Criminal</CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">
                                Grupo Delictivo
                            </p>
                            <p class="text-lg">{{ persona.grupo_delictivo || 'No especificado' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">
                                Estructura Criminal
                            </p>
                            <p class="text-lg">
                                {{ persona.estructura_criminal || 'No especificada' }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="persona.observaciones" class="md:col-span-3">
                    <CardHeader>
                        <CardTitle>Observaciones</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">{{ persona.observaciones }}</p>
                    </CardContent>
                </Card>

                <Card class="md:col-span-3">
                    <CardHeader>
                        <CardTitle>
                            Historial de Consultas ({{ persona.resultados_consulta.length }})
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="persona.resultados_consulta.length > 0" class="rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Fecha</TableHead>
                                        <TableHead>Oficio Respuesta</TableHead>
                                        <TableHead>Oficio Solicitud</TableHead>
                                        <TableHead>Institución</TableHead>
                                        <TableHead>Estado</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow
                                        v-for="resultado in persona.resultados_consulta"
                                        :key="resultado.id"
                                    >
                                        <TableCell>
                                            {{ formatDate(resultado.created_at) }}
                                        </TableCell>
                                        <TableCell class="font-medium">
                                            {{
                                                resultado.respuesta_oficio.numero_oficio_respuesta
                                            }}
                                        </TableCell>
                                        <TableCell>
                                            {{
                                                resultado.respuesta_oficio.solicitud_oficio
                                                    .numero_oficio_entrante || 'Sin número'
                                            }}
                                        </TableCell>
                                        <TableCell>
                                            {{
                                                resultado.respuesta_oficio.solicitud_oficio
                                                    .institucion.nombre
                                            }}
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                :variant="
                                                    resultado.encontrada ? 'default' : 'secondary'
                                                "
                                            >
                                                {{ resultado.encontrada ? 'Encontrada' : 'No encontrada' }}
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
                            No hay consultas registradas para esta persona
                        </div>
                    </CardContent>
                </Card>

                <Card class="md:col-span-3">
                    <CardHeader>
                        <CardTitle>Información del Sistema</CardTitle>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Fecha de Registro</p>
                            <p class="text-lg">{{ formatDateTime(persona.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">
                                Última Actualización
                            </p>
                            <p class="text-lg">{{ formatDateTime(persona.updated_at) }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
