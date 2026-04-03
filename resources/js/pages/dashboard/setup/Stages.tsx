import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Settings } from 'lucide-react';

interface PropertyType {
    id: number;
    name: string;
}

interface ConstructionStage {
    id: number;
    name: string;
    sort_order: number;
    is_active: boolean;
    property_type_id: number;
    property_type: PropertyType | null;
}

interface StagesProps {
    stages: ConstructionStage[];
    propertyTypes: PropertyType[];
}

function SetupNav() {
    const pathname = typeof window !== 'undefined' ? window.location.pathname : '';
    const links = [
        { title: 'Stages', href: '/dashboard/setup/stages' },
        { title: 'Statuses', href: '/dashboard/setup/statuses' },
        { title: 'Engineers', href: '/dashboard/setup/engineers' },
    ];

    return (
        <div className="flex gap-1 rounded-lg border p-1">
            {links.map((link) => (
                <Link
                    key={link.href}
                    href={link.href}
                    className={`rounded-md px-3 py-1.5 text-sm font-medium transition-colors ${
                        pathname === link.href
                            ? 'bg-mbp-gold text-white'
                            : 'hover:bg-accent'
                    }`}
                >
                    {link.title}
                </Link>
            ))}
        </div>
    );
}

export { SetupNav };

export default function Stages({ stages, propertyTypes }: StagesProps) {
    const grouped = propertyTypes.map((pt) => ({
        ...pt,
        stages: stages.filter((s) => s.property_type_id === pt.id),
    }));

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Setup', href: '/dashboard/setup/stages' },
                { title: 'Stages', href: '/dashboard/setup/stages' },
            ]}
        >
            <Head title="Construction Stages | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center gap-3">
                    <Settings className="h-6 w-6 text-mbp-gold" />
                    <h1 className="text-2xl font-bold tracking-tight">Setup</h1>
                </div>

                <SetupNav />

                {grouped.map((group) => (
                    <Card key={group.id}>
                        <CardHeader>
                            <CardTitle className="text-base">{group.name} Stages</CardTitle>
                        </CardHeader>
                        <CardContent className="p-0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Name</TableHead>
                                        <TableHead className="text-center">Sort Order</TableHead>
                                        <TableHead className="text-center">Active</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {group.stages.length === 0 ? (
                                        <TableRow>
                                            <TableCell colSpan={3} className="text-muted-foreground h-16 text-center">
                                                No stages defined.
                                            </TableCell>
                                        </TableRow>
                                    ) : (
                                        group.stages.map((stage) => (
                                            <TableRow key={stage.id}>
                                                <TableCell className="font-medium">{stage.name}</TableCell>
                                                <TableCell className="text-center">{stage.sort_order}</TableCell>
                                                <TableCell className="text-center">
                                                    {stage.is_active ? (
                                                        <Badge className="bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                            Active
                                                        </Badge>
                                                    ) : (
                                                        <Badge variant="secondary">Inactive</Badge>
                                                    )}
                                                </TableCell>
                                            </TableRow>
                                        ))
                                    )}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </AppLayout>
    );
}
