<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ArrowLeft, CheckCircle2, XCircle, Save } from 'lucide-vue-next';
import { ref } from 'vue';

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

interface AgenteSolicitante {
    id: number;
    nombres: string;
    apellidos: string;
    cargo: Cargo;
}

interface PersonaSolicitada {
    id: number;
    nombres: string;
    apellidos: string;
    dni: string;
    fecha_nacimiento: string | null;
}

interface SolicitudOficio {
    id: number;
    numero_oficio_entrante: string | null;
    fecha_recepcion: string;
    institucion: Institucion;
    unidad: Unidad | null;
    agente_solicitante: AgenteSolicitante;
    personas_solicitadas: PersonaSolicitada[];
}

interface PersonaRegistrada {
    id: number;
    nombres: string;
    apellidos: string;
    dni: string;
    grupo_delictivo: string | null;
    estructura_criminal: string | null;
    observaciones: string | null;
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
    resultados_consulta: ResultadoConsulta[];
}

interface JefeRegional {
    id: number;
    name: string;
    email: string;
}

interface Props {
    respuesta: RespuestaOficio;
    jefesRegionales: JefeRegional[];
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
        title: 'Editar Respuesta',
    },
];

const form = ref({
    fecha_respuesta: props.respuesta.fecha_respuesta,
    jefe_regional_id: '',
    contenido_respuesta: props.respuesta.contenido_respuesta || '',
    estado: props.respuesta.estado,
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);

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

function handleSubmit() {
    processing.value = true;
    errors.value = {};

    router.put(`/respuestas/${props.respuesta.id}`, form.value, {
        onError: (err) => {
            errors.value = err;
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
}
</script>

<template>
    <Head title="Editar Respuesta" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold tracking-tight">Editar Respuesta</h2>
                        <Badge :variant="getEstadoBadge(respuesta.estado)">
                            {{ getEstadoLabel(respuesta.estado) }}
                        </Badge>
                    </div>
                    <p class="text-muted-foreground">
                        Modificar respuesta {{ respuesta.numero_oficio_respuesta }}
                    </p>
                </div>
                <Link href="/respuestas">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver
                    </Button>
                </Link>
            </div>

            <!-- Información de la Solicitud (Solo lectura) -->
            <Card>
                <CardHeader>
                    <CardTitle>Información de la Solicitud</CardTitle>
                    <CardDescription>Detalles del oficio entrante (solo lectura)</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Número de Oficio</p>
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

                    <div class="grid gap-4 md:grid-cols-2">
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
                    </div>
                </CardContent>
            </Card>

            <!-- Resultados de Consulta (Solo lectura) -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Resultados de Consulta</CardTitle>
                            <CardDescription>
                                Resultados generados al crear la respuesta (solo lectura)
                            </CardDescription>
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
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <!-- Formulario de Edición -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Datos de la Respuesta</CardTitle>
                        <CardDescription>
                            Modifique los datos editables de la respuesta
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Label>Número de Oficio de Respuesta</Label>
                            <Input
                                :value="respuesta.numero_oficio_respuesta"
                                disabled
                                class="bg-muted"
                            />
                            <p class="text-sm text-muted-foreground">
                                El número correlativo no se puede modificar
                            </p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="fecha_respuesta">Fecha de Respuesta *</Label>
                                <Input
                                    id="fecha_respuesta"
                                    v-model="form.fecha_respuesta"
                                    type="date"
                                    required
                                    :disabled="processing"
                                />
                                <p v-if="errors.fecha_respuesta" class="text-sm text-destructive">
                                    {{ errors.fecha_respuesta }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="jefe_regional_id">Jefe Regional Firmante *</Label>
                                <select
                                    id="jefe_regional_id"
                                    v-model="form.jefe_regional_id"
                                    required
                                    :disabled="processing"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">Seleccione un jefe regional</option>
                                    <option
                                        v-for="jefe in jefesRegionales"
                                        :key="jefe.id"
                                        :value="jefe.id"
                                    >
                                        {{ jefe.name }}
                                    </option>
                                </select>
                                <p v-if="errors.jefe_regional_id" class="text-sm text-destructive">
                                    {{ errors.jefe_regional_id }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="contenido_respuesta">Contenido / Observaciones Adicionales</Label>
                            <textarea
                                id="contenido_respuesta"
                                v-model="form.contenido_respuesta"
                                placeholder="Observaciones o contenido adicional para la respuesta (opcional)"
                                rows="4"
                                :disabled="processing"
                                class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <p v-if="errors.contenido_respuesta" class="text-sm text-destructive">
                                {{ errors.contenido_respuesta }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="estado">Estado de la Respuesta *</Label>
                            <select
                                id="estado"
                                v-model="form.estado"
                                required
                                :disabled="processing"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="borrador">Borrador</option>
                                <option value="firmada">Firmada</option>
                                <option value="enviada">Enviada</option>
                            </select>
                            <p v-if="errors.estado" class="text-sm text-destructive">
                                {{ errors.estado }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                Al cambiar a "Firmada" o "Enviada", la solicitud se marcará como "Respondida"
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <div class="flex justify-end gap-4">
                    <Link href="/respuestas">
                        <Button type="button" variant="outline" :disabled="processing">
                            Cancelar
                        </Button>
                    </Link>
                    <Button type="submit" :disabled="processing">
                        <Save class="mr-2 h-4 w-4" />
                        {{ processing ? 'Guardando...' : 'Actualizar Respuesta' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
