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
import { ArrowLeft, Upload } from 'lucide-vue-next';
import { ref } from 'vue';

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
        title: 'Nueva Persona',
    },
];

const form = ref({
    nombres: '',
    apellidos: '',
    dni: '',
    fecha_nacimiento: '',
    grupo_delictivo: '',
    estructura_criminal: '',
    observaciones: '',
    foto: null as File | null,
    activo: true,
});

const fotoPreview = ref<string | null>(null);
const errors = ref<Record<string, string>>({});
const processing = ref(false);

function handleFotoChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.value.foto = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            fotoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
}

function removeFoto() {
    form.value.foto = null;
    fotoPreview.value = null;
    const input = document.getElementById('foto') as HTMLInputElement;
    if (input) {
        input.value = '';
    }
}

function submit() {
    processing.value = true;
    errors.value = {};

    router.post('/personas-registradas', form.value, {
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
    <Head title="Nueva Persona Registrada" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Nueva Persona Registrada</h2>
                    <p class="text-muted-foreground">
                        Registrar una nueva persona en la base de datos DIPAMPCO
                    </p>
                </div>
                <Link href="/personas-registradas">
                    <Button variant="outline">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Volver
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Información de la Persona</CardTitle>
                    <CardDescription>
                        Complete los datos de la persona a registrar
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
                                    placeholder="Nombres de la persona"
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
                                    placeholder="Apellidos de la persona"
                                    :disabled="processing"
                                />
                                <p v-if="errors.apellidos" class="text-sm text-destructive">
                                    {{ errors.apellidos }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="dni">DNI *</Label>
                                <Input
                                    id="dni"
                                    v-model="form.dni"
                                    required
                                    placeholder="0000-0000-00000"
                                    :disabled="processing"
                                />
                                <p v-if="errors.dni" class="text-sm text-destructive">
                                    {{ errors.dni }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="fecha_nacimiento">Fecha de Nacimiento</Label>
                                <Input
                                    id="fecha_nacimiento"
                                    v-model="form.fecha_nacimiento"
                                    type="date"
                                    :disabled="processing"
                                />
                                <p v-if="errors.fecha_nacimiento" class="text-sm text-destructive">
                                    {{ errors.fecha_nacimiento }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="grupo_delictivo">Grupo Delictivo</Label>
                                <Input
                                    id="grupo_delictivo"
                                    v-model="form.grupo_delictivo"
                                    placeholder="Ej: MS-13, Barrio 18, etc."
                                    :disabled="processing"
                                />
                                <p v-if="errors.grupo_delictivo" class="text-sm text-destructive">
                                    {{ errors.grupo_delictivo }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <Label for="estructura_criminal">Estructura Criminal</Label>
                                <Input
                                    id="estructura_criminal"
                                    v-model="form.estructura_criminal"
                                    placeholder="Ej: Líder, Miembro, etc."
                                    :disabled="processing"
                                />
                                <p v-if="errors.estructura_criminal" class="text-sm text-destructive">
                                    {{ errors.estructura_criminal }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="observaciones">Observaciones</Label>
                            <textarea
                                id="observaciones"
                                v-model="form.observaciones"
                                placeholder="Observaciones adicionales"
                                rows="3"
                                :disabled="processing"
                                class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <p v-if="errors.observaciones" class="text-sm text-destructive">
                                {{ errors.observaciones }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="foto">Fotografía</Label>
                            <div class="flex items-start gap-4">
                                <div
                                    v-if="fotoPreview"
                                    class="relative h-32 w-32 overflow-hidden rounded-md border"
                                >
                                    <img
                                        :src="fotoPreview"
                                        alt="Vista previa"
                                        class="h-full w-full object-cover"
                                    />
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        size="sm"
                                        class="absolute right-2 top-2"
                                        @click="removeFoto"
                                        :disabled="processing"
                                    >
                                        Quitar
                                    </Button>
                                </div>
                                <div class="flex-1">
                                    <Input
                                        id="foto"
                                        type="file"
                                        accept="image/*"
                                        @change="handleFotoChange"
                                        :disabled="processing"
                                        class="cursor-pointer"
                                    />
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        JPG, PNG o GIF (máx. 2MB)
                                    </p>
                                </div>
                            </div>
                            <p v-if="errors.foto" class="text-sm text-destructive">
                                {{ errors.foto }}
                            </p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="activo"
                                v-model:checked="form.activo"
                                :disabled="processing"
                            />
                            <Label for="activo" class="cursor-pointer">
                                Registro activo
                            </Label>
                        </div>

                        <div class="flex justify-end gap-4">
                            <Link href="/personas-registradas">
                                <Button type="button" variant="outline" :disabled="processing">
                                    Cancelar
                                </Button>
                            </Link>
                            <Button type="submit" :disabled="processing">
                                {{ processing ? 'Guardando...' : 'Guardar Persona' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
