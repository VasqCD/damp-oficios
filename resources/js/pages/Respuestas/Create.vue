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
import { ArrowLeft, CheckCircle2, XCircle, FileText } from 'lucide-vue-next';
import { ref, computed } from 'vue';

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
    delito: Delito | null;
    ofendido: string | null;
    observaciones: string | null;
    personas_solicitadas: PersonaSolicitada[];
}

interface JefeRegional {
    id: number;
    name: string;
    email: string;
}

interface ResultadoConsulta {
    persona_solicitada_id: number;
    nombres: string;
    apellidos: string;
    dni: string;
    encontrada: boolean;
    grupo_delictivo?: string;
    estructura_criminal?: string;
    observaciones?: string;
}

interface Props {
    solicitud: SolicitudOficio | null;
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
        title: 'Nueva Respuesta',
    },
];

const form = ref({
    solicitud_oficio_id: props.solicitud?.id || '',
    fecha_respuesta: new Date().toISOString().split('T')[0],
    jefe_regional_id: '',
    contenido_respuesta: '',
});

const resultadosConsulta = ref<ResultadoConsulta[]>([]);
const loadingConsultas = ref(false);
const consultasRealizadas = ref(false);
const errors = ref<Record<string, string>>({});
const processing = ref(false);

// Realizar consulta automática si hay solicitud
if (props.solicitud) {
    realizarConsultasAutomaticas();
}

async function realizarConsultasAutomaticas() {
    if (!props.solicitud) return;

    loadingConsultas.value = true;
    consultasRealizadas.value = false;

    try {
        const response = await fetch(`/api/consultar-personas`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                personas_solicitadas: props.solicitud.personas_solicitadas.map((p) => ({
                    id: p.id,
                    dni: p.dni,
                    nombres: p.nombres,
                    apellidos: p.apellidos,
                })),
            }),
        });

        if (!response.ok) {
            throw new Error('Error al consultar personas');
        }

        const data = await response.json();
        resultadosConsulta.value = data.resultados || [];
        consultasRealizadas.value = true;
    } catch (error) {
        console.error('Error al realizar consultas:', error);
        // Si falla la consulta automática, crear resultados en false por defecto
        resultadosConsulta.value =
            props.solicitud?.personas_solicitadas.map((p) => ({
                persona_solicitada_id: p.id,
                nombres: p.nombres,
                apellidos: p.apellidos,
                dni: p.dni,
                encontrada: false,
            })) || [];
        consultasRealizadas.value = true;
    } finally {
        loadingConsultas.value = false;
    }
}

const personasEncontradas = computed(() => {
    return resultadosConsulta.value.filter((r) => r.encontrada).length;
});

const personasNoEncontradas = computed(() => {
    return resultadosConsulta.value.filter((r) => !r.encontrada).length;
});

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function handleSubmit() {
    processing.value = true;
    errors.value = {};

    router.post('/respuestas', form.value, {
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
    <Head title="Nueva Respuesta" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Nueva Respuesta de Oficio</h2>
                    <p class="text-muted-foreground">
                        Generar respuesta a una solicitud de información
                    </p>
                </div>
                <Link href="/respuestas">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver
                    </Button>
                </Link>
            </div>

            <!-- Información de la Solicitud -->
            <Card v-if="solicitud">
                <CardHeader>
                    <CardTitle>Información de la Solicitud</CardTitle>
                    <CardDescription>Detalles del oficio entrante</CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Número de Oficio</p>
                            <p class="text-lg font-semibold">
                                {{ solicitud.numero_oficio_entrante || `#${solicitud.id}` }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Fecha de Recepción</p>
                            <p class="text-lg">{{ formatDate(solicitud.fecha_recepcion) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Institución</p>
                            <p class="text-lg">{{ solicitud.institucion.nombre }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Agente Solicitante</p>
                            <p class="text-base">
                                {{ solicitud.agente_solicitante.nombres }}
                                {{ solicitud.agente_solicitante.apellidos }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {{ solicitud.agente_solicitante.cargo.nombre }}
                            </p>
                        </div>
                        <div v-if="solicitud.unidad">
                            <p class="text-sm font-medium text-muted-foreground">Unidad</p>
                            <p class="text-base">{{ solicitud.unidad.nombre }}</p>
                        </div>
                        <div v-if="solicitud.delito">
                            <p class="text-sm font-medium text-muted-foreground">Delito</p>
                            <p class="text-base">{{ solicitud.delito.nombre }}</p>
                        </div>
                    </div>

                    <div v-if="solicitud.ofendido">
                        <p class="text-sm font-medium text-muted-foreground">Ofendido</p>
                        <p class="text-base">{{ solicitud.ofendido }}</p>
                    </div>

                    <div v-if="solicitud.observaciones">
                        <p class="text-sm font-medium text-muted-foreground">Observaciones</p>
                        <p class="text-base">{{ solicitud.observaciones }}</p>
                    </div>
                </CardContent>
            </Card>

            <!-- Resultados de Consulta -->
            <Card v-if="solicitud">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Resultados de Consulta Automática</CardTitle>
                            <CardDescription>
                                Se consultaron {{ resultadosConsulta.length }} persona(s) en el sistema
                            </CardDescription>
                        </div>
                        <div v-if="consultasRealizadas" class="flex gap-4">
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
                    <div v-if="loadingConsultas" class="flex h-32 items-center justify-center">
                        <div class="text-center">
                            <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-current border-r-transparent motion-reduce:animate-[spin_1.5s_linear_infinite]"></div>
                            <p class="mt-2 text-sm text-muted-foreground">Consultando personas...</p>
                        </div>
                    </div>

                    <div v-else-if="resultadosConsulta.length > 0" class="rounded-md border">
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
                                <TableRow v-for="resultado in resultadosConsulta" :key="resultado.persona_solicitada_id">
                                    <TableCell class="font-mono">{{ resultado.dni }}</TableCell>
                                    <TableCell class="font-medium">
                                        {{ resultado.nombres }} {{ resultado.apellidos }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="resultado.encontrada ? 'default' : 'secondary'">
                                            <CheckCircle2 v-if="resultado.encontrada" class="mr-1 h-3 w-3" />
                                            <XCircle v-else class="mr-1 h-3 w-3" />
                                            {{ resultado.encontrada ? 'Encontrada' : 'No Encontrada' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div v-if="resultado.encontrada" class="space-y-1 text-sm">
                                            <p v-if="resultado.grupo_delictivo">
                                                <span class="font-medium">Grupo:</span>
                                                {{ resultado.grupo_delictivo }}
                                            </p>
                                            <p v-if="resultado.estructura_criminal">
                                                <span class="font-medium">Estructura:</span>
                                                {{ resultado.estructura_criminal }}
                                            </p>
                                            <p v-if="resultado.observaciones" class="text-muted-foreground">
                                                {{ resultado.observaciones }}
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

                    <div
                        v-else
                        class="flex h-32 items-center justify-center text-muted-foreground"
                    >
                        No hay personas para consultar
                    </div>
                </CardContent>
            </Card>

            <!-- Formulario de Respuesta -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Datos de la Respuesta</CardTitle>
                        <CardDescription>
                            Complete la información para generar la respuesta oficial
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
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
                            <p class="text-sm text-muted-foreground">
                                La respuesta se guardará como borrador y se generará automáticamente el número correlativo RE-XXXX-{{ new Date().getFullYear() }}
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
                    <Button type="submit" :disabled="processing || !solicitud">
                        <FileText class="mr-2 h-4 w-4" />
                        {{ processing ? 'Generando...' : 'Generar Respuesta (Borrador)' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
