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
import { Search, Eye, Pencil, Trash2, FileDown } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { debounce } from 'lodash-es';

interface RespuestaOficio {
    id: number;
    numero_oficio_respuesta: string;
    fecha_respuesta: string;
    estado: string;
    solicitud_oficio: {
        numero_oficio_entrante: string;
        institucion: {
            nombre: string;
        };
    };
    analista: {
        name: string;
    };
    jefe_regional: {
        name: string;
    };
}

interface PaginatedData {
    data: RespuestaOficio[];
    links: any;
    meta: any;
}

interface Props {
    respuestas: PaginatedData;
    filters: {
        search?: string;
        estado?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const estado = ref(props.filters.estado || '');

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Respuestas',
    },
];

const performSearch = debounce(() => {
    router.get('/respuestas', { search: search.value, estado: estado.value }, { preserveState: true });
}, 300);

watch([search, estado], () => {
    performSearch();
});

function deleteRespuesta(id: number) {
    if (confirm('¿Está seguro de eliminar esta respuesta?')) {
        router.delete(`/respuestas/${id}`);
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
        borrador: 'outline',
        firmada: 'secondary',
        enviada: 'default',
    };
    return variants[estado] || 'secondary';
}
</script>

<template>
    <Head title="Respuestas de Oficios" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Respuestas de Oficios</h2>
                    <p class="text-muted-foreground">
                        Gestión de respuestas generadas
                    </p>
                </div>
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
                                <SelectItem value="">Todos</SelectItem>
                                <SelectItem value="borrador">Borrador</SelectItem>
                                <SelectItem value="firmada">Firmada</SelectItem>
                                <SelectItem value="enviada">Enviada</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Número Respuesta</TableHead>
                                    <TableHead>Fecha</TableHead>
                                    <TableHead>Solicitud Origen</TableHead>
                                    <TableHead>Institución</TableHead>
                                    <TableHead>Analista</TableHead>
                                    <TableHead class="text-center">Estado</TableHead>
                                    <TableHead class="text-right">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="respuesta in respuestas.data" :key="respuesta.id">
                                    <TableCell class="font-medium">
                                        {{ respuesta.numero_oficio_respuesta }}
                                    </TableCell>
                                    <TableCell>
                                        {{ formatDate(respuesta.fecha_respuesta) }}
                                    </TableCell>
                                    <TableCell>
                                        {{ respuesta.solicitud_oficio.numero_oficio_entrante }}
                                    </TableCell>
                                    <TableCell>
                                        {{ respuesta.solicitud_oficio.institucion.nombre }}
                                    </TableCell>
                                    <TableCell>
                                        {{ respuesta.analista.name }}
                                    </TableCell>
                                    <TableCell class="text-center">
                                        <Badge :variant="getEstadoBadge(respuesta.estado)">
                                            {{ respuesta.estado }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Link :href="`/respuestas/${respuesta.id}`">
                                                <Button variant="ghost" size="icon">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/respuestas/${respuesta.id}/pdf`">
                                                <Button variant="ghost" size="icon">
                                                    <FileDown class="h-4 w-4 text-blue-600" />
                                                </Button>
                                            </Link>
                                            <Link
                                                v-if="respuesta.estado === 'borrador'"
                                                :href="`/respuestas/${respuesta.id}/edit`"
                                            >
                                                <Button variant="ghost" size="icon">
                                                    <Pencil class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button
                                                v-if="respuesta.estado === 'borrador'"
                                                variant="ghost"
                                                size="icon"
                                                @click="deleteRespuesta(respuesta.id)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="respuestas.data.length === 0">
                                    <TableCell colspan="7" class="text-center text-muted-foreground">
                                        No se encontraron respuestas
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div v-if="respuestas.meta.last_page > 1" class="mt-4 flex justify-center">
                        <div class="flex gap-2">
                            <Link
                                v-for="link in respuestas.meta.links"
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
