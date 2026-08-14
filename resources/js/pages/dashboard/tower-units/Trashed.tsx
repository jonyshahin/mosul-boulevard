import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { ArrowLeft, ChevronLeft, ChevronRight, RotateCcw, Trash2 } from 'lucide-react';

interface TrashedTowerUnit {
    id: number;
    code: string;
    deleted_at: string | null;
    customer_name: string | null;
    completion_pct: number | null;
    tower_definition: { id: number; name: string } | null;
    floor_definition: { id: number; name: string } | null;
    status: { id: number; name: string; color_code: string | null } | null;
}

interface PaginatedTrashedTowerUnits {
    data: TrashedTowerUnit[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface TrashedProps {
    towerUnits: PaginatedTrashedTowerUnits;
}

function formatDeletedAt(value: string | null): string {
    if (!value) return '-';

    return new Date(value).toLocaleString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

export default function TowerUnitTrashed({ towerUnits }: TrashedProps) {
    function onRestore(id: number) {
        router.post(`/dashboard/tower-units/${id}/restore`);
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Tower Units', href: '/dashboard/tower-units' },
                { title: 'Deleted', href: '/dashboard/tower-units/trashed' },
            ]}
        >
            <Head title="Deleted Tower Units | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <Link
                    href="/dashboard/tower-units"
                    className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Tower Units
                </Link>

                {/* Header */}
                <div className="flex items-center gap-3">
                    <Trash2 className="text-mbp-blue h-6 w-6" />
                    <h1 className="text-2xl font-bold tracking-tight">Deleted Tower Units</h1>
                    <Badge variant="secondary" className="ml-1">
                        {towerUnits.total}
                    </Badge>
                </div>

                <p className="text-muted-foreground max-w-3xl text-sm">
                    Deleted units keep their code reserved, so a new unit cannot reuse it until
                    the old one is restored. Restoring a unit brings back its tasks, site updates
                    and inspection requests, and adds it back into dashboard and public progress
                    figures.
                </p>

                <Card>
                    <CardContent className="pt-6">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Code</TableHead>
                                    <TableHead>Tower</TableHead>
                                    <TableHead>Floor</TableHead>
                                    <TableHead>Customer</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Deleted</TableHead>
                                    <TableHead className="text-right">Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {towerUnits.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={7}
                                            className="text-muted-foreground h-24 text-center"
                                        >
                                            No deleted tower units.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    towerUnits.data.map((unit) => (
                                        <TableRow key={unit.id}>
                                            <TableCell className="text-mbp-blue font-medium">
                                                {unit.code}
                                            </TableCell>
                                            <TableCell>{unit.tower_definition?.name ?? '-'}</TableCell>
                                            <TableCell>{unit.floor_definition?.name ?? '-'}</TableCell>
                                            <TableCell>{unit.customer_name ?? '-'}</TableCell>
                                            <TableCell>
                                                {unit.status ? (
                                                    <Badge
                                                        variant="outline"
                                                        style={
                                                            unit.status.color_code
                                                                ? {
                                                                      borderColor: unit.status.color_code,
                                                                      color: unit.status.color_code,
                                                                  }
                                                                : undefined
                                                        }
                                                    >
                                                        {unit.status.name}
                                                    </Badge>
                                                ) : (
                                                    '-'
                                                )}
                                            </TableCell>
                                            <TableCell className="text-muted-foreground">
                                                {formatDeletedAt(unit.deleted_at)}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => onRestore(unit.id)}
                                                >
                                                    <RotateCcw className="mr-1 h-4 w-4" />
                                                    Restore
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Pagination */}
                {towerUnits.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-muted-foreground text-sm">
                            Showing {towerUnits.from} to {towerUnits.to} of {towerUnits.total}
                        </p>
                        <div className="flex gap-2">
                            {towerUnits.prev_page_url ? (
                                <Link
                                    href={towerUnits.prev_page_url}
                                    preserveState
                                    className="hover:bg-accent inline-flex h-9 items-center gap-1 rounded-md border px-3 text-sm font-medium transition-colors"
                                >
                                    <ChevronLeft className="h-4 w-4" />
                                    Previous
                                </Link>
                            ) : (
                                <span className="text-muted-foreground inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-md border px-3 text-sm font-medium opacity-50">
                                    <ChevronLeft className="h-4 w-4" />
                                    Previous
                                </span>
                            )}
                            <span className="text-muted-foreground inline-flex h-9 items-center px-2 text-sm">
                                Page {towerUnits.current_page} of {towerUnits.last_page}
                            </span>
                            {towerUnits.next_page_url ? (
                                <Link
                                    href={towerUnits.next_page_url}
                                    preserveState
                                    className="hover:bg-accent inline-flex h-9 items-center gap-1 rounded-md border px-3 text-sm font-medium transition-colors"
                                >
                                    Next
                                    <ChevronRight className="h-4 w-4" />
                                </Link>
                            ) : (
                                <span className="text-muted-foreground inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-md border px-3 text-sm font-medium opacity-50">
                                    Next
                                    <ChevronRight className="h-4 w-4" />
                                </span>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
