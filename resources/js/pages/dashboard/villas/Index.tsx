import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Building2, ChevronLeft, ChevronRight, Search } from 'lucide-react';

interface Villa {
    id: number;
    code: string;
    is_sold: boolean;
    customer_name: string | null;
    completion_pct: number | null;
    villa_type: { id: number; name: string } | null;
    current_stage: { id: number; name: string } | null;
    status: { id: number; name: string; color_code: string | null } | null;
    engineer: { id: number; name: string } | null;
    structural_status: { id: number; name: string } | null;
    finishing_status: { id: number; name: string } | null;
    facade_status: { id: number; name: string } | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedVillas {
    data: Villa[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface FilterOption {
    id: number;
    name: string;
}

interface Filters {
    search?: string;
    villa_type_id?: string;
    is_sold?: string;
    status_option_id?: string;
    engineer_id?: string;
    current_stage_id?: string;
}

interface VillasIndexProps {
    villas: PaginatedVillas;
    villaTypes: FilterOption[];
    engineers: FilterOption[];
    stages: FilterOption[];
    statuses: FilterOption[];
    filters: Filters;
}

function applyFilter(key: string, value: string) {
    const params = new URLSearchParams(window.location.search);

    if (value === '' || value === 'all') {
        params.delete(key);
    } else {
        params.set(key, value);
    }
    params.delete('page');

    router.get(
        `${window.location.pathname}?${params.toString()}`,
        {},
        { preserveState: true, preserveScroll: true },
    );
}

export default function VillasIndex({
    villas,
    villaTypes,
    engineers,
    stages,
    statuses,
    filters,
}: VillasIndexProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Villas', href: '/dashboard/villas' },
            ]}
        >
            <Head title="Villas | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                {/* Header */}
                <div className="flex items-center gap-3">
                    <Building2 className="h-6 w-6 text-mbp-gold" />
                    <h1 className="text-2xl font-bold tracking-tight">Villas</h1>
                    <Badge variant="secondary" className="ml-1">
                        {villas.total}
                    </Badge>
                </div>

                {/* Filters */}
                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-sm font-medium">Filters</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6">
                            {/* Search */}
                            <div className="relative">
                                <Search className="text-muted-foreground absolute top-1/2 left-2.5 h-4 w-4 -translate-y-1/2" />
                                <Input
                                    placeholder="Search code..."
                                    defaultValue={filters.search ?? ''}
                                    className="pl-8"
                                    onKeyDown={(e) => {
                                        if (e.key === 'Enter') {
                                            applyFilter('search', e.currentTarget.value);
                                        }
                                    }}
                                    onBlur={(e) => {
                                        if (e.target.value !== (filters.search ?? '')) {
                                            applyFilter('search', e.target.value);
                                        }
                                    }}
                                />
                            </div>

                            {/* Villa Type */}
                            <Select
                                value={filters.villa_type_id ?? 'all'}
                                onValueChange={(v) => applyFilter('villa_type_id', v)}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Villa Type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Types</SelectItem>
                                    {villaTypes.map((t) => (
                                        <SelectItem key={t.id} value={String(t.id)}>
                                            {t.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>

                            {/* Sold Status */}
                            <Select
                                value={filters.is_sold ?? 'all'}
                                onValueChange={(v) => applyFilter('is_sold', v)}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Sold Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All</SelectItem>
                                    <SelectItem value="1">Sold</SelectItem>
                                    <SelectItem value="0">Not Sold</SelectItem>
                                </SelectContent>
                            </Select>

                            {/* Stage */}
                            <Select
                                value={filters.current_stage_id ?? 'all'}
                                onValueChange={(v) => applyFilter('current_stage_id', v)}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Stage" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Stages</SelectItem>
                                    {stages.map((s) => (
                                        <SelectItem key={s.id} value={String(s.id)}>
                                            {s.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>

                            {/* Status */}
                            <Select
                                value={filters.status_option_id ?? 'all'}
                                onValueChange={(v) => applyFilter('status_option_id', v)}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Statuses</SelectItem>
                                    {statuses.map((s) => (
                                        <SelectItem key={s.id} value={String(s.id)}>
                                            {s.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>

                            {/* Engineer */}
                            <Select
                                value={filters.engineer_id ?? 'all'}
                                onValueChange={(v) => applyFilter('engineer_id', v)}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Engineer" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Engineers</SelectItem>
                                    {engineers.map((e) => (
                                        <SelectItem key={e.id} value={String(e.id)}>
                                            {e.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                    </CardContent>
                </Card>

                {/* Data Table */}
                <Card>
                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Code</TableHead>
                                    <TableHead>Type</TableHead>
                                    <TableHead>Sold</TableHead>
                                    <TableHead>Customer</TableHead>
                                    <TableHead>Stage</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Engineer</TableHead>
                                    <TableHead className="text-right">Completion %</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {villas.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={8} className="text-muted-foreground h-24 text-center">
                                            No villas found.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    villas.data.map((villa) => (
                                        <TableRow
                                            key={villa.id}
                                            className="cursor-pointer"
                                            onClick={() => router.get(`/dashboard/villas/${villa.id}`)}
                                        >
                                            <TableCell className="font-medium text-mbp-gold">
                                                {villa.code}
                                            </TableCell>
                                            <TableCell>{villa.villa_type?.name ?? '-'}</TableCell>
                                            <TableCell>
                                                {villa.is_sold ? (
                                                    <Badge className="bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                        Sold
                                                    </Badge>
                                                ) : (
                                                    <Badge variant="outline">Not Sold</Badge>
                                                )}
                                            </TableCell>
                                            <TableCell>{villa.customer_name ?? '-'}</TableCell>
                                            <TableCell>{villa.current_stage?.name ?? '-'}</TableCell>
                                            <TableCell>
                                                {villa.status ? (
                                                    <Badge
                                                        style={
                                                            villa.status.color_code
                                                                ? { backgroundColor: villa.status.color_code, color: '#fff' }
                                                                : undefined
                                                        }
                                                    >
                                                        {villa.status.name}
                                                    </Badge>
                                                ) : (
                                                    '-'
                                                )}
                                            </TableCell>
                                            <TableCell>{villa.engineer?.name ?? '-'}</TableCell>
                                            <TableCell className="text-right">
                                                {villa.completion_pct != null
                                                    ? `${villa.completion_pct.toFixed(0)}%`
                                                    : '-'}
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
                                    className="inline-flex h-9 items-center gap-1 rounded-md border px-3 text-sm font-medium transition-colors hover:bg-accent"
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
                                    className="inline-flex h-9 items-center gap-1 rounded-md border px-3 text-sm font-medium transition-colors hover:bg-accent"
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
