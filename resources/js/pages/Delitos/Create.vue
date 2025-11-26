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
        title: 'Delitos',
        href: '/delitos',
    },
    {
        title: 'Nuevo Delito',
    },
];

const form = ref({
    nombre: '',
    descripcion: '',
    activo: true,
});

const errors = ref<Record<string, string>>({});
const processing = ref(false);

function submit() {
    processing.value = true;
    errors.value = {};

    router.post('/delitos', form.value, {
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
    <Head title="Nuevo Delito" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Nuevo Delito</h2>
                    <p class="text-muted-foreground">
                        Registrar un nuevo tipo de delito en el sistema
                    </p>
                </div>
                <Link href="/delitos">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Información del Delito</CardTitle>
                    <CardDescription>
                        Complete los datos del nuevo delito
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="nombre">Nombre *</Label>
                            <Input
                                id="nombre"
                                v-model="form.nombre"
                                required
                                placeholder="Ej: Homicidio, Robo, Tráfico de Drogas"
                                :disabled="processing"
                            />
                            <p v-if="errors.nombre" class="text-sm text-destructive">
                                {{ errors.nombre }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="descripcion">Descripción</Label>
                            <textarea
                                id="descripcion"
                                v-model="form.descripcion"
                                placeholder="Descripción adicional del delito (opcional)"
                                rows="3"
                                :disabled="processing"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <p v-if="errors.descripcion" class="text-sm text-destructive">
                                {{ errors.descripcion }}
                            </p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="activo"
                                v-model:checked="form.activo"
                                :disabled="processing"
                            />
                            <Label for="activo" class="cursor-pointer">
                                Delito activo
                            </Label>
                        </div>

                        <div class="flex justify-end gap-4">
                            <Link href="/delitos">
                                <Button type="button" variant="outline" :disabled="processing">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button type="submit" :disabled="processing">
                                {{ processing ? 'Guardando...' : 'Guardar Delito' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
