<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
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
    nombre_completo: string;
    activo: boolean;
    unidades_count?: number;
    solicitudes_oficios_count?: number;
    created_at: string;
    updated_at: string;
}

interface PaginatedData {
    data: Institucion[];
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
    instituciones: PaginatedData;
    filters: {
        search?: string;
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
        title: 'Instituciones',
    },
];

const performSearch = debounce(() => {
    router.get('/instituciones', { search: search.value }, { preserveState: true });
}, 300);

watch(search, () => {
    performSearch();
});

function deleteInstitucion(id: number) {
    if (confirm('¿Está seguro de eliminar esta institución?')) {
        router.delete(`/instituciones/${id}`);
    }
}
</script>

<template>
    <Head title="Instituciones" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Instituciones</h2>
                    <p class="text-muted-foreground">
                        Gestión de instituciones del sistema
                    </p>
                </div>
                <Link href="/instituciones/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nueva Institución
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex items-center gap-4">
                        <div class="relative flex-1">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="search"
                                placeholder="Buscar instituciones..."
                                class="pl-10"
                            />
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nombre</TableHead>
                                    <TableHead>Nombre Completo</TableHead>
                                    <TableHead class="text-center">Unidades</TableHead>
                                    <TableHead class="text-center">Solicitudes</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                    <TableHead class="text-right">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="institucion in instituciones.data" :key="institucion.id">
                                    <TableCell class="font-medium">
                                        {{ institucion.nombre }}
                                    </TableCell>
                                    <TableCell>
                                        {{ institucion.nombre_completo }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="secondary">
                                            {{ institucion.unidades_count || 0 }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="secondary">
                                            {{ institucion.solicitudes_oficios_count || 0 }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="institucion.activo ? 'default' : 'secondary'">
                                            {{ institucion.activo ? 'Activo' : 'Inactivo' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/instituciones/${institucion.id}`">
                                                <Button variant="ghost" size="icon">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/instituciones/${institucion.id}/edit`">
                                                <Button variant="ghost" size="icon">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="deleteInstitucion(institucion.id)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="instituciones.data.length === 0">
                                    <TableCell colspan="6" class="text-center text-muted-foreground">
                                        No se encontraron instituciones
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="instituciones.last_page > 1" class="mt-4 flex justify-center">
                        <div class="flex gap-2">
                            <Link
                                v-for="link in instituciones.links"
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
