import { Link } from '@inertiajs/react';
import { BarChart3, Building, Building2, Home, LayoutGrid, Mail, Settings, Settings2, Users } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
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

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Messages',
        href: '/dashboard/messages',
        icon: Mail,
    },
    {
        title: 'Customers',
        href: '/dashboard/customers',
        icon: Users,
    },
    {
        title: 'Villas',
        href: '/dashboard/villas',
        icon: Building2,
    },
    {
        title: 'Tower Units',
        href: '/dashboard/tower-units',
        icon: Building,
    },
    {
        title: 'Reports',
        href: '/dashboard/reports',
        icon: BarChart3,
    },
    {
        title: 'Setup',
        href: '/dashboard/setup/stages',
        icon: Settings,
    },
    {
        title: 'Settings',
        href: '/dashboard/settings',
        icon: Settings2,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Mosul Boulevard',
        href: '/',
        icon: Home,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
