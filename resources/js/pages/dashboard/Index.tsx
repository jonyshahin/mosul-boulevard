import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';

export default function DashboardIndex() {
    return (
        <AppLayout breadcrumbs={[{ title: 'Dashboard', href: '/dashboard' }]}>
            <Head title="Dashboard | Mosul Boulevard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-2xl font-bold">Dashboard</h1>
                <p className="text-muted-foreground">Welcome to Mosul Boulevard Dashboard</p>
            </div>
        </AppLayout>
    );
}
