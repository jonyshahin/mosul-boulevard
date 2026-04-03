import { Head } from '@inertiajs/react';
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
import { SetupNav } from './Stages';

interface StatusOption {
    id: number;
    category: string;
    name: string;
    color_code: string | null;
    sort_order: number;
    is_active: boolean;
}

interface StatusesProps {
    statuses: StatusOption[];
}

export default function Statuses({ statuses }: StatusesProps) {
    const categories = [...new Set(statuses.map((s) => s.category))];
    const grouped = categories.map((cat) => ({
        category: cat,
        items: statuses.filter((s) => s.category === cat),
    }));

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Setup', href: '/dashboard/setup/stages' },
                { title: 'Statuses', href: '/dashboard/setup/statuses' },
            ]}
        >
            <Head title="Status Options | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center gap-3">
                    <Settings className="h-6 w-6 text-mbp-gold" />
                    <h1 className="text-2xl font-bold tracking-tight">Setup</h1>
                </div>

                <SetupNav />

                {grouped.map((group) => (
                    <Card key={group.category}>
                        <CardHeader>
                            <CardTitle className="text-base capitalize">{group.category} Statuses</CardTitle>
                        </CardHeader>
                        <CardContent className="p-0">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Name</TableHead>
                                        <TableHead>Color</TableHead>
                                        <TableHead className="text-center">Sort Order</TableHead>
                                        <TableHead className="text-center">Active</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {group.items.map((status) => (
                                        <TableRow key={status.id}>
                                            <TableCell>
                                                <Badge
                                                    style={
                                                        status.color_code
                                                            ? { backgroundColor: status.color_code, color: '#fff' }
                                                            : undefined
                                                    }
                                                >
                                                    {status.name}
                                                </Badge>
                                            </TableCell>
                                            <TableCell className="font-mono text-sm">
                                                {status.color_code ?? '-'}
                                            </TableCell>
                                            <TableCell className="text-center">{status.sort_order}</TableCell>
                                            <TableCell className="text-center">
                                                {status.is_active ? (
                                                    <Badge className="bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                        Active
                                                    </Badge>
                                                ) : (
                                                    <Badge variant="secondary">Inactive</Badge>
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </AppLayout>
    );
}
