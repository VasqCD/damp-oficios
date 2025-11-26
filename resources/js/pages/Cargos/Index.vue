<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Eye, Pencil, Trash2 } from 'lucide-vue-next';

interface Cargo {
    id: number;
    nombre: string;
    nivel_jerarquico: number | null;
    activo: boolean;
    agentes_count: number;
}

interface PaginatedData {
    data: Cargo[];
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
    cargos: PaginatedData;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Cargos',
    },
];

function deleteCargo(id: number) {
    if (confirm('¿Está seguro de eliminar este cargo?')) {
        router.delete(`/cargos/${id}`);
    }
}
</script>

<template>
    <Head title="Cargos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Cargos Policiales</h2>
                    <p class="text-muted-foreground">
                        Gestión de cargos y niveles jerárquicos
                    </p>
                </div>
                <Link href="/cargos/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nuevo Cargo
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <p class="text-sm text-muted-foreground">
                        Los cargos están ordenados por nivel jerárquico (descendente)
                    </p>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nombre</TableHead>
                                    <TableHead class="text-center">Nivel Jerárquico</TableHead>
                                    <TableHead class="text-center">Agentes</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                    <TableHead class="text-right">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="cargo in cargos.data" :key="cargo.id">
                                    <TableCell class="font-medium">
                                        {{ cargo.nombre }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="outline">
                                            {{ cargo.nivel_jerarquico || 'N/A' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="outline">
                                            {{ cargo.agentes_count }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="cargo.activo ? 'default' : 'secondary'">
                                            {{ cargo.activo ? 'Activo' : 'Inactivo' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/cargos/${cargo.id}`">
                                                <Button variant="ghost" size="icon">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/cargos/${cargo.id}/edit`">
                                                <Button variant="ghost" size="icon">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="deleteCargo(cargo.id)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="cargos.data.length === 0">
                                    <TableCell colspan="5" class="text-center text-muted-foreground">
                                        No se encontraron cargos
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="cargos.last_page > 1" class="mt-4 flex justify-center">
                        <div class="flex gap-2">
                            <Link
                                v-for="link in cargos.links"
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
