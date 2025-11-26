<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Plus, Search, Eye, Pencil, Trash2, FileText } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { debounce } from 'lodash-es';

interface SolicitudOficio {
    id: number;
    numero_oficio_entrante: string;
    fecha_recepcion: string;
    estado: string;
    institucion: {
        nombre: string;
    };
    delito?: {
        nombre: string;
    };
    personas_solicitadas_count: number;
}

interface PaginatedData {
    data: SolicitudOficio[];
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
    solicitudes: PaginatedData;
    filters: {
        search?: string;
        estado?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const estado = ref(props.filters.estado || 'todos');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Solicitudes',
    },
];

const performSearch = debounce(() => {
    const params: any = { search: search.value };
    if (estado.value && estado.value !== 'todos') {
        params.estado = estado.value;
    }
    router.get('/solicitudes', params, { preserveState: true });
}, 300);

watch([search, estado], () => {
    performSearch();
});

function deleteSolicitud(id: number) {
    if (confirm('¿Está seguro de eliminar esta solicitud?')) {
        router.delete(`/solicitudes/${id}`);
    }
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-HN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    });
}

function getEstadoBadge(estado: string) {
    const variants: Record<string, any> = {
        pendiente: 'outline',
        en_proceso: 'secondary',
        respondida: 'default',
    };
    return variants[estado] || 'secondary';
}
</script>

<template>
    <Head title="Solicitudes de Oficios" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Solicitudes de Oficios</h2>
                    <p class="text-muted-foreground">
                        Gestión de solicitudes entrantes
                    </p>
                </div>
                <Link href="/solicitudes/create">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nueva Solicitud
                    </Button>
                </Link>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-4 md:flex-row md:items-center">
                        <div class="relative flex-1">
                            <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                v-model="search"
                                placeholder="Buscar por número de oficio..."
                                class="pl-10"
                            />
                        </div>
                        <Select v-model="estado">
                            <SelectTrigger class="w-full md:w-[200px]">
                                <SelectValue placeholder="Todos los estados" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="todos">Todos</SelectItem>
                                <SelectItem value="pendiente">Pendiente</SelectItem>
                                <SelectItem value="en_proceso">En Proceso</SelectItem>
                                <SelectItem value="respondida">Respondida</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Número Oficio</TableHead>
                                    <TableHead>Fecha</TableHead>
                                    <TableHead>Institución</TableHead>
                                    <TableHead>Delito</TableHead>
                                    <TableHead class="text-center">Personas</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                    <TableHead class="text-right">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="solicitud in solicitudes.data" :key="solicitud.id">
                                    <TableCell class="font-medium">
                                        {{ solicitud.numero_oficio_entrante }}
                                    </TableCell>
                                    <TableCell>
                                        {{ formatDate(solicitud.fecha_recepcion) }}
                                    </TableCell>
                                    <TableCell>
                                        {{ solicitud.institucion.nombre }}
                                    </TableCell>
                                    <TableCell>
                                        {{ solicitud.delito?.nombre || 'N/A' }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge variant="secondary">
                                            {{ solicitud.personas_solicitadas_count }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="getEstadoBadge(solicitud.estado)">
                                            {{ solicitud.estado.replace('_', ' ') }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/solicitudes/${solicitud.id}`">
                                                <Button variant="ghost" size="icon">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link
                                                v-if="solicitud.estado === 'pendiente'"
                                                :href="`/solicitudes/${solicitud.id}/responder`"
                                            >
                                                <Button variant="ghost" size="icon">
                                                    <FileText class="h-4 w-4 text-green-600" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/solicitudes/${solicitud.id}/edit`">
                                                <Button variant="ghost" size="icon">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                @click="deleteSolicitud(solicitud.id)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="solicitudes.data.length === 0">
                                    <TableCell colspan="7" class="text-center text-muted-foreground">
                                        No se encontraron solicitudes
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="solicitudes.last_page > 1" class="mt-4 flex justify-center">
                        <div class="flex gap-2">
                            <Link
                                v-for="link in solicitudes.links"
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
