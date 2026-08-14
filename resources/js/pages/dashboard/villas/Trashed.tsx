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

interface TrashedVilla {
    id: number;
    code: string;
    deleted_at: string | null;
    customer_name: string | null;
    completion_pct: number | null;
    villa_type: { id: number; name: string } | null;
    status: { id: number; name: string; color_code: string | null } | null;
}

interface PaginatedTrashedVillas {
    data: TrashedVilla[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface TrashedProps {
    villas: PaginatedTrashedVillas;
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

export default function VillaTrashed({ villas }: TrashedProps) {
    function onRestore(id: number) {
        router.post(`/dashboard/villas/${id}/restore`);
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Villas', href: '/dashboard/villas' },
                { title: 'Deleted', href: '/dashboard/villas/trashed' },
            ]}
        >
            <Head title="Deleted Villas | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <Link
                    href="/dashboard/villas"
                    className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Villas
                </Link>

                {/* Header */}
                <div className="flex items-center gap-3">
                    <Trash2 className="text-mbp-gold h-6 w-6" />
                    <h1 className="text-2xl font-bold tracking-tight">Deleted Villas</h1>
                    <Badge variant="secondary" className="ml-1">
                        {villas.total}
                    </Badge>
                </div>

                <p className="text-muted-foreground max-w-3xl text-sm">
                    Deleted villas keep their code reserved, so a new villa cannot reuse it until
                    the old one is restored. Restoring a villa brings back its tasks, site updates
                    and inspection requests, and adds it back into dashboard and public progress
                    figures.
                </p>

                <Card>
                    <CardContent className="pt-6">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Code</TableHead>
                                    <TableHead>Type</TableHead>
                                    <TableHead>Customer</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Deleted</TableHead>
                                    <TableHead className="text-right">Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {villas.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={6}
                                            className="text-muted-foreground h-24 text-center"
                                        >
                                            No deleted villas.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    villas.data.map((villa) => (
                                        <TableRow key={villa.id}>
                                            <TableCell className="text-mbp-gold font-medium">
                                                {villa.code}
                                            </TableCell>
                                            <TableCell>{villa.villa_type?.name ?? '-'}</TableCell>
                                            <TableCell>{villa.customer_name ?? '-'}</TableCell>
                                            <TableCell>
                                                {villa.status ? (
                                                    <Badge
                                                        variant="outline"
                                                        style={
                                                            villa.status.color_code
                                                                ? {
                                                                      borderColor: villa.status.color_code,
                                                                      color: villa.status.color_code,
                                                                  }
                                                                : undefined
                                                        }
                                                    >
                                                        {villa.status.name}
                                                    </Badge>
                                                ) : (
                                                    '-'
                                                )}
                                            </TableCell>
                                            <TableCell className="text-muted-foreground">
                                                {formatDeletedAt(villa.deleted_at)}
                                            </TableCell>
                                            <TableCell className="text-right">
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => onRestore(villa.id)}
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
                {villas.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-muted-foreground text-sm">
                            Showing {villas.from} to {villas.to} of {villas.total}
                        </p>
                        <div className="flex gap-2">
                            {villas.prev_page_url ? (
                                <Link
                                    href={villas.prev_page_url}
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
                                Page {villas.current_page} of {villas.last_page}
                            </span>
                            {villas.next_page_url ? (
                                <Link
                                    href={villas.next_page_url}
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
