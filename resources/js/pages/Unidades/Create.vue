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
import { ref } from 'vue';

interface Institucion {
    id: number;
    nombre: string;
}

interface Props {
    instituciones: Institucion[];
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
        title: 'Nueva Unidad',
    },
];

const form = ref({
    institucion_id: '',
    nombre: '',
    ciudad: '',
    departamento: '',
    activo: true,
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);

function submit() {
    processing.value = true;
    errors.value = {};

    router.post('/unidades', form.value, {
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
    <Head title="Nueva Unidad" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Nueva Unidad</h2>
                    <p class="text-muted-foreground">
                        Registrar una nueva unidad en el sistema
                    </p>
                </div>
                <Link href="/unidades">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Información de la Unidad</CardTitle>
                    <CardDescription>
                        Complete los datos de la nueva unidad
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
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
                                <Label for="nombre">Nombre *</Label>
                                <Input
                                    id="nombre"
                                    v-model="form.nombre"
                                    required
                                    placeholder="Nombre de la unidad"
                                    :disabled="processing"
                                />
                                <p v-if="errors.nombre" class="text-sm text-destructive">
                                    {{ errors.nombre }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="ciudad">Ciudad</Label>
                                <Input
                                    id="ciudad"
                                    v-model="form.ciudad"
                                    placeholder="Ciudad donde se ubica"
                                    :disabled="processing"
                                />
                                <p v-if="errors.ciudad" class="text-sm text-destructive">
                                    {{ errors.ciudad }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="departamento">Departamento</Label>
                                <Input
                                    id="departamento"
                                    v-model="form.departamento"
                                    placeholder="Departamento"
                                    :disabled="processing"
                                />
                                <p v-if="errors.departamento" class="text-sm text-destructive">
                                    {{ errors.departamento }}
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
                                Unidad activa
                            </Label>
                        </div>

                        <div class="flex justify-end gap-4">
                            <Link href="/unidades">
                                <Button type="button" variant="outline" :disabled="processing">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button type="submit" :disabled="processing">
                                {{ processing ? 'Guardando...' : 'Guardar Unidad' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
