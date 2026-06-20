<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { BookOpen, Settings, Bot, MessageSquarePlus, } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
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
import type { NavItem } from '@/types';
import Conversations from '@/pages/ask/Conversations.vue';

type Conversation = {
    id: number | string;
    title: string;
};

const page = usePage();
const conversations = computed(() => (page.props.conversations ?? []) as Conversation[]);

const mainNavItems: NavItem[] = [
    {
        title: 'Démarrer une conversation',
        href: '/ask',
        icon: MessageSquarePlus,
    },
    {
        title: 'Statistiques',
        href: '/user/usage',
        icon: BookOpen,
    },
    {
        title: 'Ma Boite à Tartine',
        href: '/user',
        icon: Settings,
    },
    // Personnaliser Manuelle
    {
        title: 'Personnaliser Manuelle',
        href: '/iasettings',
        icon: Bot,
    },
];


const footerNavItems: NavItem[] = [
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="('/')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
            <Conversations :conversations="conversations" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
