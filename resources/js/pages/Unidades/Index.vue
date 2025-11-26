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

interface Institucion {
    id: number;
    nombre: string;
}

interface Unidad {
    id: number;
    nombre: string;
    ciudad: string | null;
    departamento: string | null;
    institucion: Institucion;
    activo: boolean;
    agentes_count: number;
}

interface PaginatedData {
    data: Unidad[];
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
    unidades: PaginatedData;
    instituciones: Institucion[];
    filters: {
        search?: string;
        institucion_id?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const institucionId = ref(props.filters.institucion_id || '');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Unidades',
    },
];

const performSearch = debounce(() => {
    router.get(
        '/unidades',
        {
            search: search.value,
            institucion_id: institucionId.value,
        },
        { preserveState: true }
    );
}, 300);

watch([search, institucionId], () => {
    performSearch();
});

function deleteUnidad(id: number) {
    if (confirm('¿Está seguro de eliminar esta unidad?')) {
        router.delete(`/unidades/${id}`);
    }
}
</script>

<template>
    <Head title="Unidades" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Unidades</h2>
                    <p class="text-muted-foreground">
                        Gestión de unidades por institución
                    </p>
                </div>
                <Link href="/unidades/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nueva Unidad
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-4 md:flex-row">
                        <div class="relative flex-1">
                            <Search
                                class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                            />
                            <Input v-model="search" placeholder="Buscar unidades..." class="pl-10" />
                        </div>
                        <select
                            v-model="institucionId"
                            class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring md:w-[250px]"
                        >
                            <option value="">Todas las instituciones</option>
                            <option
                                v-for="institucion in instituciones"
                                :key="institucion.id"
                                :value="institucion.id"
                            >
                                {{ institucion.nombre }}
                            </option>
                        </select>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nombre</TableHead>
                                    <TableHead>Institución</TableHead>
                                    <TableHead>Ciudad</TableHead>
                                    <TableHead>Departamento</TableHead>
                                    <TableHead class="text-center">Agentes</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                    <TableHead class="text-right">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="unidad in unidades.data" :key="unidad.id">
                                    <TableCell class="font-medium">
                                        {{ unidad.nombre }}
                                    </TableCell>
                                    <TableCell>
                                        {{ unidad.institucion.nombre }}
                                    </TableCell>
                                    <TableCell>
                                        {{ unidad.ciudad || '-' }}
                                    </TableCell>
                                    <TableCell>
                                        {{ unidad.departamento || '-' }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="outline">
                                            {{ unidad.agentes_count }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="unidad.activo ? 'default' : 'secondary'">
                                            {{ unidad.activo ? 'Activo' : 'Inactivo' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/unidades/${unidad.id}`">
                                                <Button variant="ghost" size="icon">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/unidades/${unidad.id}/edit`">
                                                <Button variant="ghost" size="icon">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="deleteUnidad(unidad.id)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="unidades.data.length === 0">
                                    <TableCell colspan="7" class="text-center text-muted-foreground">
                                        No se encontraron unidades
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="unidades.last_page > 1" class="mt-4 flex justify-center">
                        <div class="flex gap-2">
                            <Link
                                v-for="link in unidades.links"
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
