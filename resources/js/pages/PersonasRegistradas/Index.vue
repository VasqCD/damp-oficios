<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Search, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { debounce } from 'lodash-es';

interface PersonaRegistrada {
    id: number;
    nombres: string;
    apellidos: string;
    dni: string;
    fecha_nacimiento?: string;
    grupo_delictivo?: string;
    estructura_criminal?: string;
    activo: boolean;
}

interface PaginatedData {
    data: PersonaRegistrada[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
}

interface Props {
    personas: PaginatedData;
    filters: {
        search?: string;
        grupo_delictivo?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Personas Registradas',
    },
];

const performSearch = debounce(() => {
    router.get('/personas-registradas', { search: search.value }, { preserveState: true });
}, 300);

watch(search, () => {
    performSearch();
});

function deletePersona(id: number) {
    if (confirm('¿Está seguro de eliminar esta persona?')) {
        router.delete(`/personas-registradas/${id}`);
    }
}
</script>

<template>
    <Head title="Personas Registradas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Personas Registradas</h2>
                    <p class="text-muted-foreground">
                        Base de datos de personas registradas
                    </p>
                </div>
                <Link href="/personas-registradas/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nueva Persona
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Buscar por nombre, apellido o DNI..."
                            class="pl-10"
                        />
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>DNI</TableHead>
                                    <TableHead>Nombres</TableHead>
                                    <TableHead>Apellidos</TableHead>
                                    <TableHead>Grupo Delictivo</TableHead>
                                    <TableHead>Estructura Criminal</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                    <TableHead class="text-right">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="persona in personas.data" :key="persona.id">
                                    <TableCell class="font-medium">
                                        {{ persona.dni }}
                                    </TableCell>
                                    <TableCell>
                                        {{ persona.nombres }}
                                    </TableCell>
                                    <TableCell>
                                        {{ persona.apellidos }}
                                    </TableCell>
                                    <TableCell>
                                        {{ persona.grupo_delictivo || 'N/A' }}
                                    </TableCell>
                                    <TableCell>
                                        {{ persona.estructura_criminal || 'N/A' }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="persona.activo ? 'default' : 'secondary'">
                                            {{ persona.activo ? 'Activo' : 'Inactivo' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/personas-registradas/${persona.id}`">
                                                <Button variant="ghost" size="icon">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/personas-registradas/${persona.id}/edit`">
                                                <Button variant="ghost" size="icon">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="deletePersona(persona.id)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="personas.data.length === 0">
                                    <TableCell colspan="7" class="text-center text-muted-foreground">
                                        No se encontraron personas registradas
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="personas.last_page > 1" class="mt-4 flex justify-center">
                        <div class="flex gap-2">
                            <Link
                                v-for="link in personas.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    'px-3 py-2 rounded-md text-sm',
                                    link.active
                                        ? 'bg-primary text-primary-foreground'
                                        : 'bg-secondary hover:bg-secondary/80',
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
