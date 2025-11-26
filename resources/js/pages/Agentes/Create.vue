<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowLeft } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Cargo {
    id: number;
    nombre: string;
}

interface Institucion {
    id: number;
    nombre: string;
}

interface Unidad {
    id: number;
    nombre: string;
    ciudad: string;
}

interface Props {
    cargos: Cargo[];
    instituciones: Institucion[];
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
        title: 'Nuevo Agente',
    },
];

const form = ref({
    nombres: '',
    apellidos: '',
    cargo_id: '',
    institucion_id: '',
    unidad_id: '',
    tipo: 'solicitante',
    activo: true,
});

const unidades = ref<Unidad[]>([]);
const loadingUnidades = ref(false);
const errors = ref<Record<string, string>>({});
const processing = ref(false);

// Watch institucion_id to load unidades
watch(
    () => form.value.institucion_id,
    async (newId) => {
        form.value.unidad_id = '';
        unidades.value = [];

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

function submit() {
    processing.value = true;
    errors.value = {};

    router.post('/agentes', form.value, {
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
    <Head title="Nuevo Agente" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Nuevo Agente</h2>
                    <p class="text-muted-foreground">
                        Registrar un nuevo agente en el sistema
                    </p>
                </div>
                <Link href="/agentes">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Información del Agente</CardTitle>
                    <CardDescription>
                        Complete los datos del nuevo agente
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="nombres">Nombres *</Label>
                                <Input
                                    id="nombres"
                                    v-model="form.nombres"
                                    required
                                    placeholder="Nombres del agente"
                                    :disabled="processing"
                                />
                                <p v-if="errors.nombres" class="text-sm text-destructive">
                                    {{ errors.nombres }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="apellidos">Apellidos *</Label>
                                <Input
                                    id="apellidos"
                                    v-model="form.apellidos"
                                    required
                                    placeholder="Apellidos del agente"
                                    :disabled="processing"
                                />
                                <p v-if="errors.apellidos" class="text-sm text-destructive">
                                    {{ errors.apellidos }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="cargo_id">Cargo *</Label>
                                <select
                                    id="cargo_id"
                                    v-model="form.cargo_id"
                                    required
                                    :disabled="processing"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="">Seleccione un cargo</option>
                                    <option v-for="cargo in cargos" :key="cargo.id" :value="cargo.id">
                                        {{ cargo.nombre }}
                                    </option>
                                </select>
                                <p v-if="errors.cargo_id" class="text-sm text-destructive">
                                    {{ errors.cargo_id }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="tipo">Tipo *</Label>
                                <select
                                    id="tipo"
                                    v-model="form.tipo"
                                    required
                                    :disabled="processing"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option value="solicitante">Solicitante</option>
                                    <option value="firmante">Firmante</option>
                                </select>
                                <p v-if="errors.tipo" class="text-sm text-destructive">
                                    {{ errors.tipo }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="institucion_id">Institución *</Label>
                                <select
                                    id="institucion_id"
                                    v-model="form.institucion_id"
                                    required
                                    :disabled="processing"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
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
                                    :disabled="!form.institucion_id || loadingUnidades || processing"
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
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="activo"
                                v-model:checked="form.activo"
                                :disabled="processing"
                            />
                            <Label for="activo" class="cursor-pointer">
                                Agente activo
                            </Label>
                        </div>

                        <div class="flex justify-end gap-4">
                            <Link href="/agentes">
                                <Button type="button" variant="outline" :disabled="processing">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button type="submit" :disabled="processing">
                                {{ processing ? 'Guardando...' : 'Guardar Agente' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
