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
import { Building, ChevronLeft, ChevronRight, Search } from 'lucide-react';

interface TowerUnit {
    id: number;
    code: string;
    is_sold: boolean;
    customer_name: string | null;
    completion_pct: number | null;
    tower_definition: { id: number; name: string } | null;
    floor_definition: { id: number; name: string } | null;
    current_stage: { id: number; name: string } | null;
    status: { id: number; name: string; color_code: string | null } | null;
    engineer: { id: number; name: string } | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedTowerUnits {
    data: TowerUnit[];
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
    tower_definition_id?: string;
    is_sold?: string;
    status_option_id?: string;
    engineer_id?: string;
    current_stage_id?: string;
}

interface TowerUnitsIndexProps {
    towerUnits: PaginatedTowerUnits;
    towerDefinitions: FilterOption[];
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

export default function TowerUnitsIndex({
    towerUnits,
    towerDefinitions,
    engineers,
    stages,
    statuses,
    filters,
}: TowerUnitsIndexProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Tower Units', href: '/dashboard/tower-units' },
            ]}
        >
            <Head title="Tower Units | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                {/* Header */}
                <div className="flex items-center gap-3">
                    <Building className="h-6 w-6 text-mbp-blue" />
                    <h1 className="text-2xl font-bold tracking-tight">Tower Units</h1>
                    <Badge variant="secondary" className="ml-1">
                        {towerUnits.total}
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

                            {/* Tower */}
                            <Select
                                value={filters.tower_definition_id ?? 'all'}
                                onValueChange={(v) => applyFilter('tower_definition_id', v)}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Tower" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">All Towers</SelectItem>
                                    {towerDefinitions.map((t) => (
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
                                    <TableHead>Tower</TableHead>
                                    <TableHead>Floor</TableHead>
                                    <TableHead>Sold</TableHead>
                                    <TableHead>Customer</TableHead>
                                    <TableHead>Stage</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Engineer</TableHead>
                                    <TableHead className="text-right">Completion %</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {towerUnits.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={9} className="text-muted-foreground h-24 text-center">
                                            No tower units found.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    towerUnits.data.map((unit) => (
                                        <TableRow
                                            key={unit.id}
                                            className="cursor-pointer"
                                            onClick={() => router.get(`/dashboard/tower-units/${unit.id}`)}
                                        >
                                            <TableCell className="font-medium text-mbp-blue">
                                                {unit.code}
                                            </TableCell>
                                            <TableCell>{unit.tower_definition?.name ?? '-'}</TableCell>
                                            <TableCell>{unit.floor_definition?.name ?? '-'}</TableCell>
                                            <TableCell>
                                                {unit.is_sold ? (
                                                    <Badge className="bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                        Sold
                                                    </Badge>
                                                ) : (
                                                    <Badge variant="outline">Not Sold</Badge>
                                                )}
                                            </TableCell>
                                            <TableCell>{unit.customer_name ?? '-'}</TableCell>
                                            <TableCell>{unit.current_stage?.name ?? '-'}</TableCell>
                                            <TableCell>
                                                {unit.status ? (
                                                    <Badge
                                                        style={
                                                            unit.status.color_code
                                                                ? { backgroundColor: unit.status.color_code, color: '#fff' }
                                                                : undefined
                                                        }
                                                    >
                                                        {unit.status.name}
                                                    </Badge>
                                                ) : (
                                                    '-'
                                                )}
                                            </TableCell>
                                            <TableCell>{unit.engineer?.name ?? '-'}</TableCell>
                                            <TableCell className="text-right">
                                                {unit.completion_pct != null
                                                    ? `${unit.completion_pct.toFixed(0)}%`
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
                                Page {towerUnits.current_page} of {towerUnits.last_page}
                            </span>
                            {towerUnits.next_page_url ? (
                                <Link
                                    href={towerUnits.next_page_url}
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
