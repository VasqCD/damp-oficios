<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Eye, Pencil, Trash2 } from 'lucide-vue-next';

interface Delito {
    id: number;
    nombre: string;
    descripcion: string | null;
    activo: boolean;
    solicitudes_oficios_count: number;
}

interface PaginatedData {
    data: Delito[];
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
    delitos: PaginatedData;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Delitos',
    },
];

function deleteDelito(id: number) {
    if (confirm('¿Está seguro de eliminar este delito?')) {
        router.delete(`/delitos/${id}`);
    }
}
</script>

<template>
    <Head title="Delitos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Catálogo de Delitos</h2>
                    <p class="text-muted-foreground">
                        Gestión de tipos de delitos para solicitudes
                    </p>
                </div>
                <Link href="/delitos/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nuevo Delito
                    </Button>
                </Link>
            </div>

            <Card>
                <CardContent class="pt-6">
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nombre</TableHead>
                                    <TableHead>Descripción</TableHead>
                                    <TableHead class="text-center">Solicitudes</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                    <TableHead class="text-right">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="delito in delitos.data" :key="delito.id">
                                    <TableCell class="font-medium">
                                        {{ delito.nombre }}
                                    </TableCell>
                                    <TableCell>
                                        <span class="line-clamp-2">
                                            {{ delito.descripcion || '-' }}
                                        </span>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="outline">
                                            {{ delito.solicitudes_oficios_count }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="delito.activo ? 'default' : 'secondary'">
                                            {{ delito.activo ? 'Activo' : 'Inactivo' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/delitos/${delito.id}`">
                                                <Button variant="ghost" size="icon">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/delitos/${delito.id}/edit`">
                                                <Button variant="ghost" size="icon">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="deleteDelito(delito.id)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="delitos.data.length === 0">
                                    <TableCell colspan="5" class="text-center text-muted-foreground">
                                        No se encontraron delitos
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="delitos.last_page > 1" class="mt-4 flex justify-center">
                        <div class="flex gap-2">
                            <Link
                                v-for="link in delitos.links"
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
