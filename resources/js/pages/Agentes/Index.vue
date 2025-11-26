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

interface Cargo {
    id: number;
    nombre: string;
}

interface Institucion {
    id: number;
    nombre: string;
}

interface Agente {
    id: number;
    nombres: string;
    apellidos: string;
    cargo: Cargo;
    unidad: {
        nombre: string;
        institucion: Institucion;
    };
    tipo: string;
    activo: boolean;
}

interface PaginatedData {
    data: Agente[];
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
    agentes: PaginatedData;
    cargos: Cargo[];
    instituciones: Institucion[];
    filters: {
        search?: string;
        unidad_id?: string;
        cargo_id?: string;
        tipo?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const cargoId = ref(props.filters.cargo_id || '');
const tipo = ref(props.filters.tipo || '');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Agentes',
    },
];

const performSearch = debounce(() => {
    router.get(
        '/agentes',
        {
            search: search.value,
            cargo_id: cargoId.value,
            tipo: tipo.value,
        },
        { preserveState: true }
    );
}, 300);

watch([search, cargoId, tipo], () => {
    performSearch();
});

function deleteAgente(id: number) {
    if (confirm('¿Está seguro de eliminar este agente?')) {
        router.delete(`/agentes/${id}`);
    }
}

function getTipoBadge(tipo: string) {
    return tipo === 'solicitante' ? 'default' : 'secondary';
}
</script>

<template>
    <Head title="Agentes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Agentes</h2>
                    <p class="text-muted-foreground">
                        Gestión de agentes solicitantes y firmantes
                    </p>
                </div>
                <Link href="/agentes/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nuevo Agente
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
                            <Input v-model="search" placeholder="Buscar agentes..." class="pl-10" />
                        </div>
                        <select
                            v-model="cargoId"
                            class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring md:w-[200px]"
                        >
                            <option value="">Todos los cargos</option>
                            <option v-for="cargo in cargos" :key="cargo.id" :value="cargo.id">
                                {{ cargo.nombre }}
                            </option>
                        </select>
                        <select
                            v-model="tipo"
                            class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring md:w-[180px]"
                        >
                            <option value="">Todos los tipos</option>
                            <option value="solicitante">Solicitante</option>
                            <option value="firmante">Firmante</option>
                        </select>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nombre</TableHead>
                                    <TableHead>Cargo</TableHead>
                                    <TableHead>Institución/Unidad</TableHead>
                                    <TableHead class="text-center">Tipo</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                    <TableHead class="text-right">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="agente in agentes.data" :key="agente.id">
                                    <TableCell class="font-medium">
                                        {{ agente.nombres }} {{ agente.apellidos }}
                                    </TableCell>
                                    <TableCell>
                                        {{ agente.cargo.nombre }}
                                    </TableCell>
                                    <TableCell>
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{
                                                agente.unidad.institucion.nombre
                                            }}</span>
                                            <span class="text-sm text-muted-foreground">{{
                                                agente.unidad.nombre
                                            }}</span>
                                        </div>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="getTipoBadge(agente.tipo)">
                                            {{ agente.tipo === 'solicitante' ? 'Solicitante' : 'Firmante' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="agente.activo ? 'default' : 'secondary'">
                                            {{ agente.activo ? 'Activo' : 'Inactivo' }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/agentes/${agente.id}`">
                                                <Button variant="ghost" size="icon">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/agentes/${agente.id}/edit`">
                                                <Button variant="ghost" size="icon">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="deleteAgente(agente.id)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="agentes.data.length === 0">
                                    <TableCell colspan="6" class="text-center text-muted-foreground">
                                        No se encontraron agentes
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="agentes.last_page > 1" class="mt-4 flex justify-center">
                        <div class="flex gap-2">
                            <Link
                                v-for="link in agentes.links"
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
