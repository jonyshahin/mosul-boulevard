import { Link, usePage } from '@inertiajs/react';
import {
    BarChart3,
    Bell,
    Building,
    Building2,
    ClipboardList,
    Home,
    LayoutGrid,
    Mail,
    Settings,
    Settings2,
    Tag,
    UserCog,
    Users,
} from 'lucide-react';
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
import type { UserRole } from '@/types/auth';

type NavItemWithRoles = NavItem & { roles?: UserRole[] };

const mainNavItems: NavItemWithRoles[] = [
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
        title: 'Inspections',
        href: '/dashboard/inspection-requests',
        icon: ClipboardList,
        roles: ['admin', 'engineer', 'viewer'],
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
        title: 'Users',
        href: '/dashboard/users',
        icon: UserCog,
    },
    {
        title: 'Settings',
        href: '/dashboard/settings',
        icon: Settings2,
    },
    {
        title: 'Request Types',
        href: '/dashboard/settings/request-types',
        icon: Tag,
        roles: ['admin'],
    },
    {
        title: 'Notification Rules',
        href: '/dashboard/settings/notification-recipient-rules',
        icon: Bell,
        roles: ['admin'],
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
    const page = usePage();
    const user = (page.props as { auth?: { user?: { role?: UserRole } } }).auth?.user;
    const role = user?.role;

    const visibleItems = mainNavItems.filter((item) => {
        if (!item.roles) {
            return true;
        }
        return role !== undefined && item.roles.includes(role);
    });

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
                <NavMain items={visibleItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
