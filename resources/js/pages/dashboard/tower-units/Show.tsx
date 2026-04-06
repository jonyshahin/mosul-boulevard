import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { CustomerInfoDialog, type CustomerSummary } from '@/components/customer-info-dialog';
import { ArrowLeft, Calendar, ClipboardList, FileText, Image, Pencil } from 'lucide-react';

interface StatusOption {
    id: number;
    name: string;
    color_code: string | null;
}

interface TowerTask {
    id: number;
    wbs_code: string;
    task_name: string;
    planned_start: string | null;
    planned_finish: string | null;
    actual_start: string | null;
    actual_finish: string | null;
    completion_pct: number | null;
    status: StatusOption | null;
}

interface SiteUpdatePhoto {
    id: number;
    photo_path: string;
    caption: string | null;
    sort_order: number;
}

interface TowerSiteUpdate {
    id: number;
    update_date: string;
    notes: string | null;
    photos: SiteUpdatePhoto[];
}

interface TowerUnit {
    id: number;
    code: string;
    is_sold: boolean;
    customer: CustomerSummary | null;
    customer_name: string | null;
    sale_date: string | null;
    completion_pct: number | null;
    planned_start: string | null;
    planned_finish: string | null;
    actual_start: string | null;
    actual_finish: string | null;
    acc_concrete_qty: number | null;
    acc_steel_qty: number | null;
    remarks: string | null;
    tower_definition: { id: number; name: string } | null;
    floor_definition: { id: number; name: string } | null;
    current_stage: { id: number; name: string } | null;
    status: StatusOption | null;
    engineer: { id: number; name: string } | null;
    structural_status: StatusOption | null;
    finishing_status: StatusOption | null;
    facade_status: StatusOption | null;
    tower_tasks: TowerTask[];
    tower_site_updates: TowerSiteUpdate[];
}

interface TowerUnitShowProps {
    towerUnit: TowerUnit;
    stages: { id: number; name: string }[];
    statuses: StatusOption[];
    structuralStatuses: StatusOption[];
    finishingStatuses: StatusOption[];
    facadeStatuses: StatusOption[];
    engineers: { id: number; name: string }[];
}

function StatusBadge({ status }: { status: StatusOption | null }) {
    if (!status) return <span className="text-muted-foreground">-</span>;
    return (
        <Badge
            style={
                status.color_code
                    ? { backgroundColor: status.color_code, color: '#fff' }
                    : undefined
            }
        >
            {status.name}
        </Badge>
    );
}

function InfoRow({ label, children }: { label: string; children: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1">
            <dt className="text-muted-foreground text-sm">{label}</dt>
            <dd className="text-sm font-medium">{children}</dd>
        </div>
    );
}

function formatDate(date: string | null): string {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

export default function TowerUnitShow({ towerUnit }: TowerUnitShowProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Tower Units', href: '/dashboard/tower-units' },
                { title: towerUnit.code, href: `/dashboard/tower-units/${towerUnit.id}` },
            ]}
        >
            <Head title={`Unit ${towerUnit.code} | Mosul Boulevard`} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                {/* Back Link */}
                <Link
                    href="/dashboard/tower-units"
                    className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Tower Units
                </Link>

                {/* Header Card */}
                <Card>
                    <CardHeader className="pb-3">
                        <div className="flex items-center justify-between gap-3">
                            <div className="flex items-center gap-3">
                                <CardTitle className="text-xl">Unit {towerUnit.code}</CardTitle>
                                {towerUnit.is_sold ? (
                                    <Badge className="bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                        Sold
                                    </Badge>
                                ) : (
                                    <Badge variant="outline">Not Sold</Badge>
                                )}
                                <StatusBadge status={towerUnit.status} />
                            </div>
                            <Button asChild variant="outline" size="sm">
                                <Link href={`/dashboard/tower-units/${towerUnit.id}/edit`}>
                                    <Pencil className="mr-1 h-4 w-4" />
                                    Edit
                                </Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <dl className="grid grid-cols-2 gap-4 md:grid-cols-3">
                            <InfoRow label="Tower">{towerUnit.tower_definition?.name ?? '-'}</InfoRow>
                            <InfoRow label="Floor">{towerUnit.floor_definition?.name ?? '-'}</InfoRow>
                            <InfoRow label="Customer">
                                <CustomerInfoDialog
                                    customer={towerUnit.customer}
                                    fallbackName={towerUnit.customer_name}
                                />
                            </InfoRow>
                            <InfoRow label="Engineer">{towerUnit.engineer?.name ?? '-'}</InfoRow>
                            <InfoRow label="Current Stage">{towerUnit.current_stage?.name ?? '-'}</InfoRow>
                            <InfoRow label="Completion">
                                {towerUnit.completion_pct != null ? (
                                    <div className="flex items-center gap-2">
                                        <div className="bg-muted h-2 w-24 overflow-hidden rounded-full">
                                            <div
                                                className="h-full rounded-full bg-mbp-blue transition-all"
                                                style={{ width: `${Math.min(towerUnit.completion_pct, 100)}%` }}
                                            />
                                        </div>
                                        <span>{towerUnit.completion_pct.toFixed(0)}%</span>
                                    </div>
                                ) : (
                                    '-'
                                )}
                            </InfoRow>
                        </dl>
                    </CardContent>
                </Card>

                {/* Tabs */}
                <Tabs defaultValue="details">
                    <TabsList>
                        <TabsTrigger value="details" className="gap-1.5">
                            <FileText className="h-4 w-4" />
                            Details
                        </TabsTrigger>
                        <TabsTrigger value="tasks" className="gap-1.5">
                            <ClipboardList className="h-4 w-4" />
                            Tasks
                            <Badge variant="secondary" className="ml-1 text-xs">
                                {towerUnit.tower_tasks.length}
                            </Badge>
                        </TabsTrigger>
                        <TabsTrigger value="updates" className="gap-1.5">
                            <Image className="h-4 w-4" />
                            Site Updates
                            <Badge variant="secondary" className="ml-1 text-xs">
                                {towerUnit.tower_site_updates.length}
                            </Badge>
                        </TabsTrigger>
                    </TabsList>

                    {/* Details Tab */}
                    <TabsContent value="details">
                        <div className="grid gap-4 md:grid-cols-2">
                            {/* Status Info */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-base">Status Information</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <dl className="grid grid-cols-1 gap-4">
                                        <InfoRow label="Overall Status">
                                            <StatusBadge status={towerUnit.status} />
                                        </InfoRow>
                                        <InfoRow label="Structural Status">
                                            <StatusBadge status={towerUnit.structural_status} />
                                        </InfoRow>
                                        <InfoRow label="Finishing Status">
                                            <StatusBadge status={towerUnit.finishing_status} />
                                        </InfoRow>
                                        <InfoRow label="Facade Status">
                                            <StatusBadge status={towerUnit.facade_status} />
                                        </InfoRow>
                                        <InfoRow label="Current Stage">
                                            {towerUnit.current_stage?.name ?? '-'}
                                        </InfoRow>
                                    </dl>
                                </CardContent>
                            </Card>

                            {/* Schedule & Quantities */}
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-base">Schedule & Quantities</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <dl className="grid grid-cols-1 gap-4">
                                        <InfoRow label="Planned Start">
                                            <span className="inline-flex items-center gap-1.5">
                                                <Calendar className="text-muted-foreground h-3.5 w-3.5" />
                                                {formatDate(towerUnit.planned_start)}
                                            </span>
                                        </InfoRow>
                                        <InfoRow label="Planned Finish">
                                            <span className="inline-flex items-center gap-1.5">
                                                <Calendar className="text-muted-foreground h-3.5 w-3.5" />
                                                {formatDate(towerUnit.planned_finish)}
                                            </span>
                                        </InfoRow>
                                        <InfoRow label="Actual Start">
                                            <span className="inline-flex items-center gap-1.5">
                                                <Calendar className="text-muted-foreground h-3.5 w-3.5" />
                                                {formatDate(towerUnit.actual_start)}
                                            </span>
                                        </InfoRow>
                                        <InfoRow label="Actual Finish">
                                            <span className="inline-flex items-center gap-1.5">
                                                <Calendar className="text-muted-foreground h-3.5 w-3.5" />
                                                {formatDate(towerUnit.actual_finish)}
                                            </span>
                                        </InfoRow>
                                        <InfoRow label="Accumulated Concrete">
                                            {towerUnit.acc_concrete_qty != null
                                                ? `${towerUnit.acc_concrete_qty.toLocaleString()} m³`
                                                : '-'}
                                        </InfoRow>
                                        <InfoRow label="Accumulated Steel">
                                            {towerUnit.acc_steel_qty != null
                                                ? `${towerUnit.acc_steel_qty.toLocaleString()} kg`
                                                : '-'}
                                        </InfoRow>
                                        <InfoRow label="Sale Date">{formatDate(towerUnit.sale_date)}</InfoRow>
                                    </dl>
                                </CardContent>
                            </Card>

                            {/* Remarks */}
                            {towerUnit.remarks && (
                                <Card className="md:col-span-2">
                                    <CardHeader>
                                        <CardTitle className="text-base">Remarks</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <p className="text-sm whitespace-pre-line">{towerUnit.remarks}</p>
                                    </CardContent>
                                </Card>
                            )}
                        </div>
                    </TabsContent>

                    {/* Tasks Tab */}
                    <TabsContent value="tasks">
                        <Card>
                            <CardContent className="p-0">
                                {towerUnit.tower_tasks.length === 0 ? (
                                    <div className="text-muted-foreground flex flex-col items-center gap-2 py-12 text-center">
                                        <ClipboardList className="h-8 w-8" />
                                        <p>No tasks yet.</p>
                                    </div>
                                ) : (
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>WBS Code</TableHead>
                                                <TableHead>Task Name</TableHead>
                                                <TableHead>Status</TableHead>
                                                <TableHead>Planned Start</TableHead>
                                                <TableHead>Planned Finish</TableHead>
                                                <TableHead className="text-right">Completion %</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {towerUnit.tower_tasks.map((task) => (
                                                <TableRow key={task.id}>
                                                    <TableCell className="font-mono text-sm">
                                                        {task.wbs_code}
                                                    </TableCell>
                                                    <TableCell>{task.task_name}</TableCell>
                                                    <TableCell>
                                                        <StatusBadge status={task.status} />
                                                    </TableCell>
                                                    <TableCell>{formatDate(task.planned_start)}</TableCell>
                                                    <TableCell>{formatDate(task.planned_finish)}</TableCell>
                                                    <TableCell className="text-right">
                                                        {task.completion_pct != null
                                                            ? `${task.completion_pct.toFixed(0)}%`
                                                            : '-'}
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                )}
                            </CardContent>
                        </Card>
                    </TabsContent>

                    {/* Site Updates Tab */}
                    <TabsContent value="updates">
                        {towerUnit.tower_site_updates.length === 0 ? (
                            <Card>
                                <CardContent>
                                    <div className="text-muted-foreground flex flex-col items-center gap-2 py-12 text-center">
                                        <Image className="h-8 w-8" />
                                        <p>No updates yet.</p>
                                    </div>
                                </CardContent>
                            </Card>
                        ) : (
                            <div className="relative space-y-6 pl-6 before:absolute before:top-0 before:bottom-0 before:left-2 before:w-px before:bg-border">
                                {towerUnit.tower_site_updates.map((update) => (
                                    <div key={update.id} className="relative">
                                        <div className="bg-mbp-blue absolute -left-6 top-1 h-4 w-4 rounded-full border-2 border-white dark:border-gray-900" />
                                        <Card>
                                            <CardHeader className="pb-2">
                                                <CardTitle className="flex items-center gap-2 text-sm">
                                                    <Calendar className="h-4 w-4 text-mbp-blue" />
                                                    {formatDate(update.update_date)}
                                                </CardTitle>
                                            </CardHeader>
                                            <CardContent className="space-y-3">
                                                {update.notes && (
                                                    <p className="text-sm whitespace-pre-line">{update.notes}</p>
                                                )}
                                                {update.photos.length > 0 && (
                                                    <div className="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6">
                                                        {update.photos.map((photo) => (
                                                            <a
                                                                key={photo.id}
                                                                href={`/storage/${photo.photo_path}`}
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                className="group relative aspect-square overflow-hidden rounded-md border"
                                                            >
                                                                <img
                                                                    src={`/storage/${photo.photo_path}`}
                                                                    alt={photo.caption ?? 'Site photo'}
                                                                    className="h-full w-full object-cover transition-transform group-hover:scale-105"
                                                                />
                                                            </a>
                                                        ))}
                                                    </div>
                                                )}
                                            </CardContent>
                                        </Card>
                                    </div>
                                ))}
                            </div>
                        )}
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}
