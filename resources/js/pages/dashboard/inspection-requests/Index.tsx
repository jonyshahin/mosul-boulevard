import { Head, Link, router } from '@inertiajs/react';
import {
    AlertTriangle,
    ChevronLeft,
    ChevronRight,
    ClipboardList,
    FilterX,
    Loader2,
    Search,
} from 'lucide-react';
import { useMemo, useState } from 'react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
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
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group';
import AppLayout from '@/layouts/app-layout';
import { cn } from '@/lib/utils';
import type {
    InspectionListFilters,
    InspectionRequestSummary,
    InspectionSharedTranslations,
    PaginatedInspectionRequests,
    SeverityValue,
    StatusValue,
} from '@/types/inspection-requests';

type ListTranslations = {
    empty_state: string;
    create_via_api_link: string;
    overdue_badge: string;
    replies_count: string;
    filters: {
        status: string;
        severity: string;
        subject_type: string;
        subject_all: string;
        subject_villa: string;
        subject_tower_unit: string;
        assigned_to_me: string;
        overdue_only: string;
        sort_by: string;
        sort_newest: string;
        sort_oldest: string;
        sort_severity: string;
        sort_due: string;
        clear: string;
    };
    columns: {
        id: string;
        title: string;
        subject: string;
        type: string;
        severity: string;
        status: string;
        assignee: string;
        due_date: string;
        created: string;
    };
    pagination: {
        showing: string;
        previous: string;
        next: string;
        page: string;
    };
};

type PageProps = {
    requests: PaginatedInspectionRequests;
    filters: InspectionListFilters;
    translations: {
        title: string;
        list: ListTranslations;
        shared: InspectionSharedTranslations;
    };
    auth: {
        id: number | null;
        can_create: boolean;
    };
};

const SEVERITY_VALUES: SeverityValue[] = ['low', 'medium', 'high', 'critical'];

const STATUS_VALUES: StatusValue[] = [
    'open',
    'in_progress',
    'resolved',
    'verified',
    'closed',
    'reopened',
];

const SEVERITY_CLASS: Record<SeverityValue, string> = {
    low: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
    medium: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
    high: 'bg-orange-100 text-orange-900 dark:bg-orange-900/40 dark:text-orange-200',
    critical: 'bg-red-100 text-red-900 dark:bg-red-900/50 dark:text-red-100',
};

const STATUS_CLASS: Record<StatusValue, string> = {
    open: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
    in_progress: 'bg-amber-100 text-amber-900 dark:bg-amber-900/40 dark:text-amber-200',
    resolved: 'bg-emerald-100 text-emerald-900 dark:bg-emerald-900/40 dark:text-emerald-100',
    verified: 'bg-emerald-200 text-emerald-900 dark:bg-emerald-800 dark:text-emerald-50',
    closed: 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
    reopened: 'bg-red-100 text-red-900 dark:bg-red-900/40 dark:text-red-100',
};

function buildQueryString(filters: InspectionListFilters): string {
    const params = new URLSearchParams();
    filters.status.forEach((s) => params.append('status[]', s));
    filters.severity.forEach((s) => params.append('severity[]', s));

    if (filters.subject_type) {
        params.set('subject_type', filters.subject_type);
    }

    params.set('assigned_to_me', filters.assigned_to_me ? '1' : '0');

    if (filters.overdue_only) {
        params.set('overdue_only', '1');
    }

    if (filters.sort && filters.sort !== '-created_at') {
        params.set('sort', filters.sort);
    }

    return params.toString();
}

function useFilterNav(currentFilters: InspectionListFilters) {
    const [busy, setBusy] = useState(false);

    function navigate(patch: Partial<InspectionListFilters>): void {
        const next = { ...currentFilters, ...patch };
        const qs = buildQueryString(next);
        setBusy(true);
        router.get(
            `/dashboard/inspection-requests${qs ? `?${qs}` : ''}`,
            {},
            {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => setBusy(false),
            },
        );
    }

    return { busy, navigate };
}

function formatRelative(iso: string): string {
    const date = new Date(iso);
    const diffMs = Date.now() - date.getTime();
    const diffMin = Math.round(diffMs / 60000);

    if (diffMin < 1) {
        return 'just now';
    }

    if (diffMin < 60) {
        return `${diffMin}m ago`;
    }

    const diffHr = Math.round(diffMin / 60);

    if (diffHr < 24) {
        return `${diffHr}h ago`;
    }

    const diffDay = Math.round(diffHr / 24);

    if (diffDay < 30) {
        return `${diffDay}d ago`;
    }

    return date.toLocaleDateString();
}

function SeverityBadge({ severity, label }: { severity: SeverityValue; label: string }) {
    return <Badge className={cn('font-medium', SEVERITY_CLASS[severity])}>{label}</Badge>;
}

function StatusBadge({ status, label }: { status: StatusValue; label: string }) {
    return <Badge className={cn('font-medium', STATUS_CLASS[status])}>{label}</Badge>;
}

function AssigneeCell({ assignee }: { assignee: InspectionRequestSummary['assignee'] }) {
    if (!assignee) {
        return <span className="text-muted-foreground">—</span>;
    }

    const initials = assignee.name
        .split(' ')
        .map((part) => part[0])
        .filter(Boolean)
        .slice(0, 2)
        .join('')
        .toUpperCase();

    return (
        <div className="flex items-center gap-2">
            <span className="bg-muted text-muted-foreground inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-medium">
                {initials || '?'}
            </span>
            <span className="text-sm">{assignee.name}</span>
        </div>
    );
}

export default function InspectionRequestsIndex({
    requests,
    filters,
    translations,
    auth,
}: PageProps) {
    const t = translations.list;
    const shared = translations.shared;
    const { busy, navigate } = useFilterNav(filters);

    const showingText = useMemo(() => {
        if (!requests.meta.total) {
            return null;
        }

        return t.pagination.showing
            .replace(':from', String(requests.meta.from ?? 0))
            .replace(':to', String(requests.meta.to ?? 0))
            .replace(':total', String(requests.meta.total));
    }, [requests.meta, t.pagination.showing]);

    function toggleArrayValue<V extends string>(current: V[], value: V): V[] {
        return current.includes(value) ? current.filter((v) => v !== value) : [...current, value];
    }

    function clearFilters(): void {
        navigate({
            status: [],
            severity: [],
            subject_type: null,
            assigned_to_me: false,
            overdue_only: false,
            sort: '-created_at',
        });
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: translations.title, href: '/dashboard/inspection-requests' },
            ]}
        >
            <Head title={`${translations.title} | Mosul Boulevard`} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <ClipboardList className="text-mbp-gold h-6 w-6" />
                        <h1 className="text-2xl font-bold tracking-tight">{translations.title}</h1>
                        <Badge variant="secondary">{requests.meta.total}</Badge>
                        {busy && (
                            <Loader2 className="text-muted-foreground h-4 w-4 animate-spin" aria-hidden />
                        )}
                    </div>
                </div>

                <Card>
                    <CardContent className="space-y-4 p-4 md:p-6">
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                            <div className="space-y-2">
                                <Label className="text-xs font-semibold uppercase">{t.filters.status}</Label>
                                <div className="flex flex-wrap gap-1.5">
                                    {STATUS_VALUES.map((s) => {
                                        const active = filters.status.includes(s);

                                        return (
                                            <button
                                                key={s}
                                                type="button"
                                                onClick={() =>
                                                    navigate({ status: toggleArrayValue(filters.status, s) })
                                                }
                                                className={cn(
                                                    'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium transition-colors',
                                                    active
                                                        ? `${STATUS_CLASS[s]} border-transparent`
                                                        : 'border-border text-muted-foreground hover:text-foreground',
                                                )}
                                            >
                                                {shared.status[s]}
                                            </button>
                                        );
                                    })}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label className="text-xs font-semibold uppercase">{t.filters.severity}</Label>
                                <div className="flex flex-wrap gap-1.5">
                                    {SEVERITY_VALUES.map((s) => {
                                        const active = filters.severity.includes(s);

                                        return (
                                            <button
                                                key={s}
                                                type="button"
                                                onClick={() =>
                                                    navigate({ severity: toggleArrayValue(filters.severity, s) })
                                                }
                                                className={cn(
                                                    'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium transition-colors',
                                                    active
                                                        ? `${SEVERITY_CLASS[s]} border-transparent`
                                                        : 'border-border text-muted-foreground hover:text-foreground',
                                                )}
                                            >
                                                {shared.severity[s]}
                                            </button>
                                        );
                                    })}
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label className="text-xs font-semibold uppercase">{t.filters.subject_type}</Label>
                                <ToggleGroup
                                    type="single"
                                    value={filters.subject_type ?? 'all'}
                                    onValueChange={(value) =>
                                        navigate({
                                            subject_type:
                                                value === 'villa' || value === 'tower_unit' ? value : null,
                                        })
                                    }
                                    className="justify-start"
                                >
                                    <ToggleGroupItem value="all" className="text-xs">
                                        {t.filters.subject_all}
                                    </ToggleGroupItem>
                                    <ToggleGroupItem value="villa" className="text-xs">
                                        {t.filters.subject_villa}
                                    </ToggleGroupItem>
                                    <ToggleGroupItem value="tower_unit" className="text-xs">
                                        {t.filters.subject_tower_unit}
                                    </ToggleGroupItem>
                                </ToggleGroup>
                            </div>

                            <div className="space-y-2">
                                <Label className="text-xs font-semibold uppercase">{t.filters.sort_by}</Label>
                                <Select value={filters.sort} onValueChange={(v) => navigate({ sort: v })}>
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="-created_at">{t.filters.sort_newest}</SelectItem>
                                        <SelectItem value="created_at">{t.filters.sort_oldest}</SelectItem>
                                        <SelectItem value="-severity">{t.filters.sort_severity}</SelectItem>
                                        <SelectItem value="due_date">{t.filters.sort_due}</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div className="flex flex-wrap items-center gap-4">
                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="assigned-to-me"
                                    checked={filters.assigned_to_me}
                                    onCheckedChange={(checked) => navigate({ assigned_to_me: checked === true })}
                                />
                                <Label htmlFor="assigned-to-me" className="text-sm">
                                    {t.filters.assigned_to_me}
                                </Label>
                            </div>
                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="overdue-only"
                                    checked={filters.overdue_only}
                                    onCheckedChange={(checked) => navigate({ overdue_only: checked === true })}
                                />
                                <Label htmlFor="overdue-only" className="text-sm">
                                    {t.filters.overdue_only}
                                </Label>
                            </div>
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                onClick={clearFilters}
                                className="ms-auto"
                            >
                                <FilterX className="me-1 h-4 w-4" />
                                {t.filters.clear}
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-[80px]">{t.columns.id}</TableHead>
                                    <TableHead>{t.columns.title}</TableHead>
                                    <TableHead>{t.columns.subject}</TableHead>
                                    <TableHead>{t.columns.type}</TableHead>
                                    <TableHead>{t.columns.severity}</TableHead>
                                    <TableHead>{t.columns.status}</TableHead>
                                    <TableHead>{t.columns.assignee}</TableHead>
                                    <TableHead>{t.columns.due_date}</TableHead>
                                    <TableHead>{t.columns.created}</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {requests.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={9} className="py-16 text-center">
                                            <div className="text-muted-foreground flex flex-col items-center gap-3">
                                                <Search className="h-8 w-8" />
                                                <p className="text-sm">{t.empty_state}</p>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    requests.data.map((r) => (
                                        <InspectionRow key={r.id} row={r} overdueLabel={t.overdue_badge} />
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {requests.meta.last_page > 1 && (
                    <div className="flex items-center justify-between gap-4">
                        <p className="text-muted-foreground text-sm">{showingText}</p>
                        <div className="flex items-center gap-2">
                            {requests.links.prev ? (
                                <Link
                                    href={requests.links.prev}
                                    preserveState
                                    preserveScroll
                                    className="hover:bg-accent inline-flex h-9 items-center gap-1 rounded-md border px-3 text-sm font-medium transition-colors"
                                >
                                    <ChevronLeft className="h-4 w-4" />
                                    {t.pagination.previous}
                                </Link>
                            ) : (
                                <span className="text-muted-foreground inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-md border px-3 text-sm font-medium opacity-50">
                                    <ChevronLeft className="h-4 w-4" />
                                    {t.pagination.previous}
                                </span>
                            )}
                            <span className="text-muted-foreground inline-flex h-9 items-center px-2 text-sm">
                                {t.pagination.page
                                    .replace(':current', String(requests.meta.current_page))
                                    .replace(':last', String(requests.meta.last_page))}
                            </span>
                            {requests.links.next ? (
                                <Link
                                    href={requests.links.next}
                                    preserveState
                                    preserveScroll
                                    className="hover:bg-accent inline-flex h-9 items-center gap-1 rounded-md border px-3 text-sm font-medium transition-colors"
                                >
                                    {t.pagination.next}
                                    <ChevronRight className="h-4 w-4" />
                                </Link>
                            ) : (
                                <span className="text-muted-foreground inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-md border px-3 text-sm font-medium opacity-50">
                                    {t.pagination.next}
                                    <ChevronRight className="h-4 w-4" />
                                </span>
                            )}
                        </div>
                    </div>
                )}

                {auth.can_create && !requests.data.length && (
                    <p className="text-muted-foreground text-center text-xs">{t.create_via_api_link}</p>
                )}
            </div>
        </AppLayout>
    );
}

function InspectionRow({
    row,
    overdueLabel,
}: {
    row: InspectionRequestSummary;
    overdueLabel: string;
}) {
    const highlightClass = cn(
        row.is_overdue && 'bg-red-50/50 hover:bg-red-50 dark:bg-red-950/20 dark:hover:bg-red-950/30',
        row.severity.value === 'critical' && 'border-s-4 border-s-red-600',
    );

    return (
        <TableRow
            className={cn('cursor-pointer', highlightClass)}
            onClick={() => router.get(`/dashboard/inspection-requests/${row.id}`)}
        >
            <TableCell className="font-mono text-xs">#{row.id}</TableCell>
            <TableCell>
                <div className="flex flex-col">
                    <Link
                        href={`/dashboard/inspection-requests/${row.id}`}
                        className="text-mbp-gold font-medium hover:underline"
                        onClick={(e) => e.stopPropagation()}
                    >
                        {row.title}
                    </Link>
                    {row.is_overdue && (
                        <span className="mt-0.5 inline-flex items-center gap-1 text-xs text-red-700 dark:text-red-300">
                            <AlertTriangle className="h-3 w-3" />
                            {overdueLabel}
                        </span>
                    )}
                </div>
            </TableCell>
            <TableCell className="text-sm">{row.subject?.display_name ?? '—'}</TableCell>
            <TableCell>
                {row.request_type ? (
                    <span
                        className="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                        style={{
                            backgroundColor: `${row.request_type.color}22`,
                            color: row.request_type.color,
                        }}
                    >
                        {row.request_type.name}
                    </span>
                ) : (
                    <span className="text-muted-foreground">—</span>
                )}
            </TableCell>
            <TableCell>
                <SeverityBadge severity={row.severity.value} label={row.severity.label} />
            </TableCell>
            <TableCell>
                <StatusBadge status={row.status.value} label={row.status.label} />
            </TableCell>
            <TableCell>
                <AssigneeCell assignee={row.assignee} />
            </TableCell>
            <TableCell className="text-sm">{row.due_date ?? '—'}</TableCell>
            <TableCell className="text-muted-foreground text-xs">
                {formatRelative(row.created_at)}
            </TableCell>
        </TableRow>
    );
}
