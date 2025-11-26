<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Institucion {
    id: number;
    nombre: string;
    nombre_completo: string;
}

interface Delito {
    id: number;
    nombre: string;
}

interface Unidad {
    id: number;
    nombre: string;
    ciudad: string;
}

interface Agente {
    id: number;
    nombres: string;
    apellidos: string;
    cargo: {
        nombre: string;
    };
}

interface Persona {
    nombres: string;
    apellidos: string;
    dni: string;
    fecha_nacimiento: string | null;
}

interface Props {
    instituciones: Institucion[];
    delitos: Delito[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Solicitudes',
        href: '/solicitudes',
    },
    {
        title: 'Nueva Solicitud',
        href: '/solicitudes/create',
    },
];

const form = ref({
    numero_oficio_entrante: '',
    fecha_recepcion: new Date().toISOString().split('T')[0],
    institucion_id: '',
    unidad_id: '',
    agente_solicitante_id: '',
    delito_id: '',
    ofendido: '',
    observaciones: '',
    personas: [
        {
            nombres: '',
            apellidos: '',
            dni: '',
            fecha_nacimiento: null as string | null,
        },
    ] as Persona[],
});

const unidades = ref<Unidad[]>([]);
const agentes = ref<Agente[]>([]);
const loadingUnidades = ref(false);
const loadingAgentes = ref(false);
const errors = ref<Record<string, string>>({});
const processing = ref(false);

// Watch institucion_id to load unidades
watch(
    () => form.value.institucion_id,
    async (newId) => {
        form.value.unidad_id = '';
        form.value.agente_solicitante_id = '';
        unidades.value = [];
        agentes.value = [];

        if (newId) {
            loadingUnidades.value = true;
            try {
                const response = await fetch(`/api/instituciones/${newId}/unidades`);
                const data = await response.json();
                unidades.value = data.unidades;
            } catch (error) {
                console.error('Error loading unidades:', error);
            } finally {
                loadingUnidades.value = false;
            }
        }
    }
);

// Watch unidad_id to load agentes
watch(
    () => form.value.unidad_id,
    async (newId) => {
        form.value.agente_solicitante_id = '';
        agentes.value = [];

        if (newId) {
            loadingAgentes.value = true;
            try {
                const response = await fetch(`/api/unidades/${newId}/agentes`);
                const data = await response.json();
                agentes.value = data.agentes;
            } catch (error) {
                console.error('Error loading agentes:', error);
            } finally {
                loadingAgentes.value = false;
            }
        }
    }
);

const addPersona = () => {
    form.value.personas.push({
        nombres: '',
        apellidos: '',
        dni: '',
        fecha_nacimiento: null,
    });
};

const removePersona = (index: number) => {
    if (form.value.personas.length > 1) {
        form.value.personas.splice(index, 1);
    }
};

const handleSubmit = () => {
    processing.value = true;
    errors.value = {};

    router.post('/solicitudes', form.value, {
        onError: (err) => {
            errors.value = err;
            processing.value = false;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>

<template>
    <Head title="Nueva Solicitud" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Nueva Solicitud de Oficio</h2>
                    <p class="text-muted-foreground">
                        Registre una nueva solicitud de información
                    </p>
                </div>
                <Button variant="outline" as-child>
                    <Link href="/solicitudes">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver
                    </Link>
                </Button>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Información de la Solicitud -->
                <Card>
                    <CardHeader>
                        <CardTitle>Información de la Solicitud</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="numero_oficio_entrante">Número de Oficio Entrante</Label>
                                <Input
                                    id="numero_oficio_entrante"
                                    v-model="form.numero_oficio_entrante"
                                    placeholder="Ej: OF-2024-001 (opcional)"
                                />
                                <p v-if="errors.numero_oficio_entrante" class="text-sm text-destructive">
                                    {{ errors.numero_oficio_entrante }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="fecha_recepcion">Fecha de Recepción *</Label>
                                <Input
                                    id="fecha_recepcion"
                                    v-model="form.fecha_recepcion"
                                    type="date"
                                    required
                                />
                                <p v-if="errors.fecha_recepcion" class="text-sm text-destructive">
                                    {{ errors.fecha_recepcion }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="space-y-2">
                                <Label for="institucion_id">Institución *</Label>
                                <select
                                    id="institucion_id"
                                    v-model="form.institucion_id"
                                    required
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                    <option value="">Seleccione una institución</option>
                                    <option
                                        v-for="institucion in instituciones"
                                        :key="institucion.id"
                                        :value="institucion.id"
                                    >
                                        {{ institucion.nombre }}
                                    </option>
                                </select>
                                <p v-if="errors.institucion_id" class="text-sm text-destructive">
                                    {{ errors.institucion_id }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="unidad_id">Unidad *</Label>
                                <select
                                    id="unidad_id"
                                    v-model="form.unidad_id"
                                    :disabled="!form.institucion_id || loadingUnidades"
                                    required
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">
                                        {{ loadingUnidades ? 'Cargando...' : 'Seleccione una unidad' }}
                                    </option>
                                    <option v-for="unidad in unidades" :key="unidad.id" :value="unidad.id">
                                        {{ unidad.nombre }} - {{ unidad.ciudad }}
                                    </option>
                                </select>
                                <p v-if="errors.unidad_id" class="text-sm text-destructive">
                                    {{ errors.unidad_id }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="agente_solicitante_id">Agente Solicitante *</Label>
                                <select
                                    id="agente_solicitante_id"
                                    v-model="form.agente_solicitante_id"
                                    :disabled="!form.unidad_id || loadingAgentes"
                                    required
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">
                                        {{ loadingAgentes ? 'Cargando...' : 'Seleccione un agente' }}
                                    </option>
                                    <option v-for="agente in agentes" :key="agente.id" :value="agente.id">
                                        {{ agente.nombres }} {{ agente.apellidos }} ({{
                                            agente.cargo.nombre
                                        }})
                                    </option>
                                </select>
                                <p v-if="errors.agente_solicitante_id" class="text-sm text-destructive">
                                    {{ errors.agente_solicitante_id }}
                                </p>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="delito_id">Delito</Label>
                                <select
                                    id="delito_id"
                                    v-model="form.delito_id"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                    <option value="">Seleccione un delito (opcional)</option>
                                    <option v-for="delito in delitos" :key="delito.id" :value="delito.id">
                                        {{ delito.nombre }}
                                    </option>
                                </select>
                                <p v-if="errors.delito_id" class="text-sm text-destructive">
                                    {{ errors.delito_id }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="ofendido">Ofendido</Label>
                                <Input
                                    id="ofendido"
                                    v-model="form.ofendido"
                                    placeholder="Nombre del ofendido (opcional)"
                                />
                                <p v-if="errors.ofendido" class="text-sm text-destructive">
                                    {{ errors.ofendido }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="observaciones">Observaciones</Label>
                            <textarea
                                id="observaciones"
                                v-model="form.observaciones"
                                placeholder="Observaciones adicionales (opcional)"
                                rows="3"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            />
                            <p v-if="errors.observaciones" class="text-sm text-destructive">
                                {{ errors.observaciones }}
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Personas Solicitadas -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <CardTitle>Personas Solicitadas *</CardTitle>
                        <Button type="button" size="sm" @click="addPersona">
                            <Plus class="mr-2 h-4 w-4" />
                            Agregar Persona
                        </Button>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div
                            v-for="(persona, index) in form.personas"
                            :key="index"
                            class="space-y-4 rounded-lg border p-4"
                        >
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium">Persona {{ index + 1 }}</h4>
                                <Button
                                    v-if="form.personas.length > 1"
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    @click="removePersona(index)"
                                >
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </Button>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <Label :for="`nombres_${index}`">Nombres *</Label>
                                    <Input
                                        :id="`nombres_${index}`"
                                        v-model="persona.nombres"
                                        required
                                        placeholder="Nombres"
                                    />
                                    <p
                                        v-if="errors[`personas.${index}.nombres`]"
                                        class="text-sm text-destructive"
                                    >
                                        {{ errors[`personas.${index}.nombres`] }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label :for="`apellidos_${index}`">Apellidos *</Label>
                                    <Input
                                        :id="`apellidos_${index}`"
                                        v-model="persona.apellidos"
                                        required
                                        placeholder="Apellidos"
                                    />
                                    <p
                                        v-if="errors[`personas.${index}.apellidos`]"
                                        class="text-sm text-destructive"
                                    >
                                        {{ errors[`personas.${index}.apellidos`] }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label :for="`dni_${index}`">DNI *</Label>
                                    <Input
                                        :id="`dni_${index}`"
                                        v-model="persona.dni"
                                        required
                                        placeholder="Documento de identidad"
                                    />
                                    <p
                                        v-if="errors[`personas.${index}.dni`]"
                                        class="text-sm text-destructive"
                                    >
                                        {{ errors[`personas.${index}.dni`] }}
                                    </p>
                                </div>

                                <div class="space-y-2">
                                    <Label :for="`fecha_nacimiento_${index}`">Fecha de Nacimiento</Label>
                                    <Input
                                        :id="`fecha_nacimiento_${index}`"
                                        v-model="persona.fecha_nacimiento"
                                        type="date"
                                        placeholder="Fecha de nacimiento"
                                    />
                                    <p
                                        v-if="errors[`personas.${index}.fecha_nacimiento`]"
                                        class="text-sm text-destructive"
                                    >
                                        {{ errors[`personas.${index}.fecha_nacimiento`] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Actions -->
                <div class="flex justify-end gap-4">
                    <Button type="button" variant="outline" as-child>
                        <Link href="/solicitudes">Cancelar</Link>
                    </Button>
                    <Button type="submit" :disabled="processing">
                        {{ processing ? 'Guardando...' : 'Guardar Solicitud' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
