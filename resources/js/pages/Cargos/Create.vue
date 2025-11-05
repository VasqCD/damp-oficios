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
        title: 'Nuevo Cargo',
    },
];

const form = ref({
    nombre: '',
    nivel_jerarquico: '',
    activo: true,
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);

function submit() {
    processing.value = true;
    errors.value = {};

    router.post('/cargos', form.value, {
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
    <Head title="Nuevo Cargo" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Nuevo Cargo</h2>
                    <p class="text-muted-foreground">
                        Registrar un nuevo cargo policial en el sistema
                    </p>
                </div>
                <Link href="/cargos">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Información del Cargo</CardTitle>
                    <CardDescription>
                        Complete los datos del nuevo cargo policial
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="nombre">Nombre *</Label>
                                <Input
                                    id="nombre"
                                    v-model="form.nombre"
                                    required
                                    placeholder="Ej: Comisionado, Sub Comisionado, Agente"
                                    :disabled="processing"
                                />
                                <p v-if="errors.nombre" class="text-sm text-destructive">
                                    {{ errors.nombre }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="nivel_jerarquico">Nivel Jerárquico</Label>
                                <Input
                                    id="nivel_jerarquico"
                                    v-model="form.nivel_jerarquico"
                                    type="number"
                                    min="1"
                                    max="100"
                                    placeholder="1-100 (mayor número = mayor jerarquía)"
                                    :disabled="processing"
                                />
                                <p class="text-sm text-muted-foreground">
                                    Opcional: 1 = rango más bajo, 100 = rango más alto
                                </p>
                                <p v-if="errors.nivel_jerarquico" class="text-sm text-destructive">
                                    {{ errors.nivel_jerarquico }}
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
                                Cargo activo
                            </Label>
                        </div>

                        <div class="flex justify-end gap-4">
                            <Link href="/cargos">
                                <Button type="button" variant="outline" :disabled="processing">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button type="submit" :disabled="processing">
                                {{ processing ? 'Guardando...' : 'Guardar Cargo' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
