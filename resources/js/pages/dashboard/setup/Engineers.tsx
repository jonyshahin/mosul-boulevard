import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
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

interface Engineer {
    id: number;
    name: string;
    phone: string | null;
    email: string | null;
    specialty: string | null;
    is_active: boolean;
    deleted_at: string | null;
}

interface EngineersProps {
    engineers: Engineer[];
}

export default function Engineers({ engineers }: EngineersProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Setup', href: '/dashboard/setup/stages' },
                { title: 'Engineers', href: '/dashboard/setup/engineers' },
            ]}
        >
            <Head title="Engineers | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center gap-3">
                    <Settings className="h-6 w-6 text-mbp-gold" />
                    <h1 className="text-2xl font-bold tracking-tight">Setup</h1>
                </div>

                <SetupNav />

                <Card>
                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Phone</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Specialty</TableHead>
                                    <TableHead className="text-center">Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {engineers.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={5} className="text-muted-foreground h-24 text-center">
                                            No engineers found.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    engineers.map((engineer) => (
                                        <TableRow key={engineer.id} className={engineer.deleted_at ? 'opacity-50' : ''}>
                                            <TableCell className="font-medium">{engineer.name}</TableCell>
                                            <TableCell>{engineer.phone ?? '-'}</TableCell>
                                            <TableCell>{engineer.email ?? '-'}</TableCell>
                                            <TableCell>{engineer.specialty ?? '-'}</TableCell>
                                            <TableCell className="text-center">
                                                {engineer.deleted_at ? (
                                                    <Badge variant="destructive">Deleted</Badge>
                                                ) : engineer.is_active ? (
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
            </div>
        </AppLayout>
    );
}
