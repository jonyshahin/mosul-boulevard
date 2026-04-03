import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Calendar, CheckCircle2, ClipboardList, Image } from 'lucide-react';
import WebsiteLayout from '@/layouts/website-layout';

interface StatusOption {
    id: number;
    name: string;
    color_code: string | null;
}

interface VillaTask {
    id: number;
    wbs_code: string;
    task_name: string;
    planned_start: string | null;
    planned_finish: string | null;
    completion_pct: number | null;
    status: StatusOption | null;
}

interface SiteUpdatePhoto {
    id: number;
    photo_path: string;
    caption: string | null;
}

interface VillaSiteUpdate {
    id: number;
    update_date: string;
    notes: string | null;
    photos: SiteUpdatePhoto[];
}

interface Villa {
    id: number;
    code: string;
    is_sold: boolean;
    customer_name: string | null;
    sale_date: string | null;
    completion_pct: number | null;
    planned_start: string | null;
    planned_finish: string | null;
    actual_start: string | null;
    actual_finish: string | null;
    acc_concrete_qty: number | null;
    acc_steel_qty: number | null;
    villa_type: { id: number; name: string } | null;
    current_stage: { id: number; name: string } | null;
    status: StatusOption | null;
    engineer: { id: number; name: string } | null;
    structural_status: StatusOption | null;
    finishing_status: StatusOption | null;
    facade_status: StatusOption | null;
    villa_tasks: VillaTask[];
    villa_site_updates: VillaSiteUpdate[];
}

function StatusBadge({ status, label }: { status: StatusOption | null; label: string }) {
    if (!status) return null;
    return (
        <div className="flex items-center justify-between rounded-lg bg-white/5 px-4 py-3">
            <span className="text-sm text-gray-400">{label}</span>
            <span
                className="rounded-full px-3 py-0.5 text-xs font-medium"
                style={
                    status.color_code
                        ? { backgroundColor: status.color_code + '33', color: status.color_code }
                        : { backgroundColor: 'rgba(255,255,255,0.1)', color: '#d1d5db' }
                }
            >
                {status.name}
            </span>
        </div>
    );
}

function formatDate(date: string | null): string {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function InfoItem({ label, value }: { label: string; value: string | number | null }) {
    return (
        <div className="flex items-center justify-between rounded-lg bg-white/5 px-4 py-3">
            <span className="text-sm text-gray-400">{label}</span>
            <span className="text-sm font-medium text-gray-200">{value ?? '-'}</span>
        </div>
    );
}

export default function VillaShow({ villa }: { villa: Villa }) {
    return (
        <WebsiteLayout>
            <Head title={`Villa ${villa.code} | Mosul Boulevard`} />

            <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                {/* Back Link */}
                <Link
                    href="/villas"
                    className="mb-8 inline-flex items-center gap-1 text-sm text-gray-400 transition-colors hover:text-[#B8860B]"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Villas
                </Link>

                {/* Header */}
                <div className="mb-8 flex flex-wrap items-center gap-3">
                    <h1 className="text-3xl font-bold">Villa {villa.code}</h1>
                    <span className="rounded-full bg-white/10 px-3 py-1 text-sm font-medium text-gray-300">
                        {villa.villa_type?.name ?? '-'}
                    </span>
                    {villa.is_sold ? (
                        <span className="rounded-full bg-emerald-900/50 px-3 py-1 text-sm font-medium text-emerald-300">
                            Sold
                        </span>
                    ) : (
                        <span className="rounded-full bg-white/10 px-3 py-1 text-sm font-medium text-gray-400">
                            Available
                        </span>
                    )}
                </div>

                {/* Completion */}
                <div className="mb-8 rounded-xl border border-white/10 bg-white/5 p-6">
                    <div className="mb-2 flex items-center justify-between">
                        <span className="text-sm font-medium text-gray-300">Overall Completion</span>
                        <span className="text-2xl font-bold text-[#B8860B]">
                            {villa.completion_pct != null ? `${villa.completion_pct.toFixed(0)}%` : '0%'}
                        </span>
                    </div>
                    <div className="h-3 overflow-hidden rounded-full bg-white/10">
                        <div
                            className="h-full rounded-full bg-[#B8860B] transition-all"
                            style={{ width: `${Math.min(villa.completion_pct ?? 0, 100)}%` }}
                        />
                    </div>
                </div>

                {/* Two Column Layout */}
                <div className="mb-8 grid gap-6 lg:grid-cols-2">
                    {/* Property Details */}
                    <div className="rounded-xl border border-white/10 bg-white/5 p-6">
                        <h2 className="mb-4 text-lg font-bold">Property Details</h2>
                        <div className="space-y-2">
                            <InfoItem label="Current Stage" value={villa.current_stage?.name} />
                            <InfoItem label="Engineer" value={villa.engineer?.name} />
                            <InfoItem label="Customer" value={villa.customer_name} />
                            <InfoItem label="Sale Date" value={formatDate(villa.sale_date)} />
                            <InfoItem label="Planned Start" value={formatDate(villa.planned_start)} />
                            <InfoItem label="Planned Finish" value={formatDate(villa.planned_finish)} />
                            <InfoItem label="Actual Start" value={formatDate(villa.actual_start)} />
                            <InfoItem label="Actual Finish" value={formatDate(villa.actual_finish)} />
                            <InfoItem
                                label="Concrete Qty"
                                value={villa.acc_concrete_qty != null ? `${villa.acc_concrete_qty.toLocaleString()} m³` : null}
                            />
                            <InfoItem
                                label="Steel Qty"
                                value={villa.acc_steel_qty != null ? `${villa.acc_steel_qty.toLocaleString()} kg` : null}
                            />
                        </div>
                    </div>

                    {/* Status Overview */}
                    <div className="rounded-xl border border-white/10 bg-white/5 p-6">
                        <h2 className="mb-4 text-lg font-bold">Status Overview</h2>
                        <div className="space-y-2">
                            <StatusBadge status={villa.status} label="Overall Status" />
                            <StatusBadge status={villa.structural_status} label="Structural" />
                            <StatusBadge status={villa.finishing_status} label="Finishing" />
                            <StatusBadge status={villa.facade_status} label="Facade" />
                        </div>
                    </div>
                </div>

                {/* Tasks */}
                <div className="mb-8 rounded-xl border border-white/10 bg-white/5 p-6">
                    <h2 className="mb-4 flex items-center gap-2 text-lg font-bold">
                        <ClipboardList className="h-5 w-5 text-[#B8860B]" />
                        Tasks ({villa.villa_tasks.length})
                    </h2>
                    {villa.villa_tasks.length === 0 ? (
                        <p className="py-8 text-center text-gray-500">No tasks yet.</p>
                    ) : (
                        <div className="space-y-2">
                            {villa.villa_tasks.map((task) => (
                                <div
                                    key={task.id}
                                    className="flex flex-wrap items-center justify-between gap-2 rounded-lg bg-white/5 px-4 py-3"
                                >
                                    <div className="flex items-center gap-3">
                                        <CheckCircle2 className="h-4 w-4 shrink-0 text-gray-500" />
                                        <div>
                                            <p className="text-sm font-medium text-gray-200">{task.task_name}</p>
                                            <p className="text-xs text-gray-500">
                                                {task.wbs_code} &bull; {formatDate(task.planned_start)} - {formatDate(task.planned_finish)}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-3">
                                        {task.status && (
                                            <span
                                                className="rounded-full px-2.5 py-0.5 text-xs font-medium"
                                                style={
                                                    task.status.color_code
                                                        ? { backgroundColor: task.status.color_code + '33', color: task.status.color_code }
                                                        : { backgroundColor: 'rgba(255,255,255,0.1)', color: '#d1d5db' }
                                                }
                                            >
                                                {task.status.name}
                                            </span>
                                        )}
                                        <span className="text-xs font-medium text-[#B8860B]">
                                            {task.completion_pct != null ? `${task.completion_pct.toFixed(0)}%` : '-'}
                                        </span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {/* Site Updates */}
                <div className="rounded-xl border border-white/10 bg-white/5 p-6">
                    <h2 className="mb-4 flex items-center gap-2 text-lg font-bold">
                        <Image className="h-5 w-5 text-[#B8860B]" />
                        Site Updates ({villa.villa_site_updates.length})
                    </h2>
                    {villa.villa_site_updates.length === 0 ? (
                        <p className="py-8 text-center text-gray-500">No updates yet.</p>
                    ) : (
                        <div className="relative space-y-6 pl-6 before:absolute before:top-0 before:bottom-0 before:left-2 before:w-px before:bg-white/10">
                            {villa.villa_site_updates.map((update) => (
                                <div key={update.id} className="relative">
                                    <div className="absolute -left-6 top-1 h-4 w-4 rounded-full border-2 border-[#1B1B1B] bg-[#B8860B]" />
                                    <div className="rounded-lg bg-white/5 p-4">
                                        <p className="mb-2 flex items-center gap-2 text-sm font-medium text-[#B8860B]">
                                            <Calendar className="h-3.5 w-3.5" />
                                            {formatDate(update.update_date)}
                                        </p>
                                        {update.notes && (
                                            <p className="mb-3 text-sm whitespace-pre-line text-gray-300">{update.notes}</p>
                                        )}
                                        {update.photos.length > 0 && (
                                            <div className="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6">
                                                {update.photos.map((photo) => (
                                                    <a
                                                        key={photo.id}
                                                        href={`/storage/${photo.photo_path}`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="group relative aspect-square overflow-hidden rounded-md border border-white/10"
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
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </WebsiteLayout>
    );
}
