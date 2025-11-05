<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as institucionesIndex } from '@/routes/instituciones';
import { index as unidadesIndex } from '@/routes/unidades';
import { index as cargosIndex } from '@/routes/cargos';
import { index as agentesIndex } from '@/routes/agentes';
import { index as delitosIndex } from '@/routes/delitos';
import { index as solicitudesIndex } from '@/routes/solicitudes';
import { index as respuestasIndex } from '@/routes/respuestas';
import { index as personasRegistradasIndex } from '@/routes/personas-registradas';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    Folder,
    LayoutGrid,
    Building2,
    FileText,
    Send,
    Users,
    UserCheck,
    MapPin,
    Shield,
    AlertCircle
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Instituciones',
        href: institucionesIndex(),
        icon: Building2,
    },
    {
        title: 'Unidades',
        href: unidadesIndex(),
        icon: MapPin,
    },
    {
        title: 'Cargos',
        href: cargosIndex(),
        icon: Shield,
    },
    {
        title: 'Agentes',
        href: agentesIndex(),
        icon: UserCheck,
    },
    {
        title: 'Delitos',
        href: delitosIndex(),
        icon: AlertCircle,
    },
    {
        title: 'Solicitudes',
        href: solicitudesIndex(),
        icon: FileText,
    },
    {
        title: 'Respuestas',
        href: respuestasIndex(),
        icon: Send,
    },
    {
        title: 'Personas Registradas',
        href: personasRegistradasIndex(),
        icon: Users,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
