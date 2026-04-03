import { Head, Link, router } from '@inertiajs/react';
import { Building2, ChevronLeft, ChevronRight } from 'lucide-react';
import WebsiteLayout from '@/layouts/website-layout';

interface Villa {
    id: number;
    code: string;
    is_sold: boolean;
    customer_name: string | null;
    completion_pct: number | null;
    villa_type: { id: number; name: string } | null;
    current_stage: { id: number; name: string } | null;
    status: { id: number; name: string; color_code: string | null } | null;
}

interface PaginatedVillas {
    data: Villa[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface VillaType {
    id: number;
    name: string;
    total_count: number;
}

interface Filters {
    villa_type_id?: string;
}

interface VillasIndexProps {
    villas: PaginatedVillas;
    villaTypes: VillaType[];
    filters: Filters;
}

export default function VillasIndex({ villas, villaTypes, filters }: VillasIndexProps) {
    function setFilter(villaTypeId: string) {
        const params = villaTypeId ? { villa_type_id: villaTypeId } : {};
        router.get('/villas', params, { preserveState: true, preserveScroll: true });
    }

    return (
        <WebsiteLayout>
            <Head title="Our Villas | Mosul Boulevard" />

            {/* Hero */}
            <section className="border-b border-white/10 bg-gradient-to-b from-[#1B1B1B] to-[#1B1B1B]/95 px-4 py-20">
                <div className="mx-auto max-w-7xl text-center">
                    <Building2 className="mx-auto mb-4 h-10 w-10 text-[#B8860B]" />
                    <h1 className="mb-2 text-4xl font-bold">Our Villas</h1>
                    <p className="text-lg text-gray-400">{villas.total} villas available</p>
                </div>
            </section>

            {/* Filters */}
            <section className="border-b border-white/10 px-4 py-6">
                <div className="mx-auto flex max-w-7xl flex-wrap items-center gap-3">
                    <span className="text-sm text-gray-400">Filter:</span>
                    <button
                        type="button"
                        onClick={() => setFilter('')}
                        className={`rounded-full px-4 py-1.5 text-sm font-medium transition-colors ${
                            !filters.villa_type_id
                                ? 'bg-[#B8860B] text-white'
                                : 'border border-white/20 text-gray-300 hover:border-[#B8860B] hover:text-[#B8860B]'
                        }`}
                    >
                        All
                    </button>
                    {villaTypes.map((vt) => (
                        <button
                            key={vt.id}
                            type="button"
                            onClick={() => setFilter(String(vt.id))}
                            className={`rounded-full px-4 py-1.5 text-sm font-medium transition-colors ${
                                filters.villa_type_id === String(vt.id)
                                    ? 'bg-[#B8860B] text-white'
                                    : 'border border-white/20 text-gray-300 hover:border-[#B8860B] hover:text-[#B8860B]'
                            }`}
                        >
                            {vt.name} ({vt.total_count})
                        </button>
                    ))}
                </div>
            </section>

            {/* Grid */}
            <section className="px-4 py-12">
                <div className="mx-auto max-w-7xl">
                    {villas.data.length === 0 ? (
                        <div className="py-20 text-center text-gray-400">
                            <Building2 className="mx-auto mb-4 h-12 w-12" />
                            <p>No villas found.</p>
                        </div>
                    ) : (
                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {villas.data.map((villa) => (
                                <Link
                                    key={villa.id}
                                    href={`/villas/${villa.id}`}
                                    className="group rounded-xl border border-white/10 bg-white/5 p-6 transition-all hover:border-[#B8860B]/30 hover:bg-white/10"
                                >
                                    <div className="mb-4 flex items-center justify-between">
                                        <h3 className="text-lg font-bold text-[#B8860B]">{villa.code}</h3>
                                        <span className="rounded-full bg-white/10 px-3 py-0.5 text-xs font-medium text-gray-300">
                                            {villa.villa_type?.name ?? '-'}
                                        </span>
                                    </div>

                                    <div className="mb-4 flex items-center gap-2">
                                        {villa.is_sold ? (
                                            <span className="rounded-full bg-emerald-900/50 px-3 py-0.5 text-xs font-medium text-emerald-300">
                                                Sold
                                            </span>
                                        ) : (
                                            <span className="rounded-full bg-white/10 px-3 py-0.5 text-xs font-medium text-gray-400">
                                                Available
                                            </span>
                                        )}
                                        {villa.status && (
                                            <span
                                                className="rounded-full px-3 py-0.5 text-xs font-medium"
                                                style={
                                                    villa.status.color_code
                                                        ? { backgroundColor: villa.status.color_code + '33', color: villa.status.color_code }
                                                        : undefined
                                                }
                                            >
                                                {villa.status.name}
                                            </span>
                                        )}
                                    </div>

                                    {villa.current_stage && (
                                        <p className="mb-3 text-sm text-gray-400">
                                            Stage: <span className="text-gray-300">{villa.current_stage.name}</span>
                                        </p>
                                    )}

                                    {/* Completion Bar */}
                                    <div className="mt-auto">
                                        <div className="mb-1 flex items-center justify-between text-xs">
                                            <span className="text-gray-400">Completion</span>
                                            <span className="font-medium text-[#B8860B]">
                                                {villa.completion_pct != null ? `${villa.completion_pct.toFixed(0)}%` : '0%'}
                                            </span>
                                        </div>
                                        <div className="h-1.5 overflow-hidden rounded-full bg-white/10">
                                            <div
                                                className="h-full rounded-full bg-[#B8860B] transition-all"
                                                style={{ width: `${Math.min(villa.completion_pct ?? 0, 100)}%` }}
                                            />
                                        </div>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    )}

                    {/* Pagination */}
                    {villas.last_page > 1 && (
                        <div className="mt-12 flex items-center justify-between">
                            <p className="text-sm text-gray-400">
                                Showing {villas.from} to {villas.to} of {villas.total}
                            </p>
                            <div className="flex gap-2">
                                {villas.prev_page_url ? (
                                    <Link
                                        href={villas.prev_page_url}
                                        preserveState
                                        className="inline-flex h-9 items-center gap-1 rounded-md border border-white/20 px-3 text-sm font-medium text-gray-300 transition-colors hover:border-[#B8860B] hover:text-[#B8860B]"
                                    >
                                        <ChevronLeft className="h-4 w-4" />
                                        Previous
                                    </Link>
                                ) : (
                                    <span className="inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-md border border-white/10 px-3 text-sm font-medium text-gray-600">
                                        <ChevronLeft className="h-4 w-4" />
                                        Previous
                                    </span>
                                )}
                                <span className="inline-flex h-9 items-center px-2 text-sm text-gray-400">
                                    Page {villas.current_page} of {villas.last_page}
                                </span>
                                {villas.next_page_url ? (
                                    <Link
                                        href={villas.next_page_url}
                                        preserveState
                                        className="inline-flex h-9 items-center gap-1 rounded-md border border-white/20 px-3 text-sm font-medium text-gray-300 transition-colors hover:border-[#B8860B] hover:text-[#B8860B]"
                                    >
                                        Next
                                        <ChevronRight className="h-4 w-4" />
                                    </Link>
                                ) : (
                                    <span className="inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-md border border-white/10 px-3 text-sm font-medium text-gray-600">
                                        Next
                                        <ChevronRight className="h-4 w-4" />
                                    </span>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </section>
        </WebsiteLayout>
    );
}
