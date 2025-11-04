<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import InputError from '@/components/InputError.vue';
import { ArrowLeft } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Instituciones',
        href: '/instituciones',
    },
    {
        title: 'Nueva Institución',
    },
];

const form = useForm({
    nombre: '',
    nombre_completo: '',
    activo: true,
});

function submit() {
    form.post('/instituciones');
}
</script>

<template>
    <Head title="Nueva Institución" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Nueva Institución</h2>
                    <p class="text-muted-foreground">
                        Crear una nueva institución en el sistema
                    </p>
                </div>
                <Link href="/instituciones">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Información de la Institución</CardTitle>
                    <CardDescription>
                        Complete los datos de la nueva institución
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="nombre">Nombre (Siglas) *</Label>
                                <Input
                                    id="nombre"
                                    v-model="form.nombre"
                                    placeholder="Ej: DPI"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.nombre" />
                            </div>

                            <div class="space-y-2">
                                <Label for="nombre_completo">Nombre Completo *</Label>
                                <Input
                                    id="nombre_completo"
                                    v-model="form.nombre_completo"
                                    placeholder="Ej: Dirección Policial de Investigaciones"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.nombre_completo" />
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="activo"
                                v-model:checked="form.activo"
                                :disabled="form.processing"
                            />
                            <Label for="activo" class="cursor-pointer">
                                Institución activa
                            </Label>
                        </div>

                        <div class="flex justify-end gap-4">
                            <Link href="/instituciones">
                                <Button type="button" variant="outline" :disabled="form.processing">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Guardando...' : 'Guardar Institución' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
