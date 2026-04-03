import { Head, Link, router } from '@inertiajs/react';
import { Building, ChevronLeft, ChevronRight } from 'lucide-react';
import WebsiteLayout from '@/layouts/website-layout';

interface TowerUnit {
    id: number;
    code: string;
    is_sold: boolean;
    completion_pct: number | null;
    tower_definition: { id: number; name: string } | null;
    floor_definition: { id: number; name: string } | null;
    current_stage: { id: number; name: string } | null;
    status: { id: number; name: string; color_code: string | null } | null;
}

interface PaginatedTowerUnits {
    data: TowerUnit[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface TowerDefinition {
    id: number;
    name: string;
}

interface Filters {
    tower_definition_id?: string;
}

interface TowersIndexProps {
    towerUnits: PaginatedTowerUnits;
    towerDefinitions: TowerDefinition[];
    filters: Filters;
}

export default function TowersIndex({ towerUnits, towerDefinitions, filters }: TowersIndexProps) {
    function setFilter(towerDefId: string) {
        const params = towerDefId ? { tower_definition_id: towerDefId } : {};
        router.get('/towers', params, { preserveState: true, preserveScroll: true });
    }

    return (
        <WebsiteLayout>
            <Head title="Our Towers | Mosul Boulevard" />

            {/* Hero */}
            <section className="border-b border-white/10 bg-gradient-to-b from-[#1B1B1B] to-[#1B1B1B]/95 px-4 py-20">
                <div className="mx-auto max-w-7xl text-center">
                    <Building className="mx-auto mb-4 h-10 w-10 text-[#1B4F72]" />
                    <h1 className="mb-2 text-4xl font-bold">Our Towers</h1>
                    <p className="text-lg text-gray-400">{towerUnits.total} apartments available</p>
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
                            !filters.tower_definition_id
                                ? 'bg-[#1B4F72] text-white'
                                : 'border border-white/20 text-gray-300 hover:border-[#1B4F72] hover:text-[#1B4F72]'
                        }`}
                    >
                        All
                    </button>
                    {towerDefinitions.map((td) => (
                        <button
                            key={td.id}
                            type="button"
                            onClick={() => setFilter(String(td.id))}
                            className={`rounded-full px-4 py-1.5 text-sm font-medium transition-colors ${
                                filters.tower_definition_id === String(td.id)
                                    ? 'bg-[#1B4F72] text-white'
                                    : 'border border-white/20 text-gray-300 hover:border-[#1B4F72] hover:text-[#1B4F72]'
                            }`}
                        >
                            {td.name}
                        </button>
                    ))}
                </div>
            </section>

            {/* Grid */}
            <section className="px-4 py-12">
                <div className="mx-auto max-w-7xl">
                    {towerUnits.data.length === 0 ? (
                        <div className="py-20 text-center text-gray-400">
                            <Building className="mx-auto mb-4 h-12 w-12" />
                            <p>No tower units found.</p>
                        </div>
                    ) : (
                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {towerUnits.data.map((unit) => (
                                <Link
                                    key={unit.id}
                                    href={`/towers/${unit.id}`}
                                    className="group rounded-xl border border-white/10 bg-white/5 p-6 transition-all hover:border-[#1B4F72]/30 hover:bg-white/10"
                                >
                                    <div className="mb-3 flex items-center justify-between">
                                        <h3 className="text-lg font-bold text-[#1B4F72]">{unit.code}</h3>
                                        {unit.is_sold ? (
                                            <span className="rounded-full bg-emerald-900/50 px-3 py-0.5 text-xs font-medium text-emerald-300">
                                                Sold
                                            </span>
                                        ) : (
                                            <span className="rounded-full bg-white/10 px-3 py-0.5 text-xs font-medium text-gray-400">
                                                Available
                                            </span>
                                        )}
                                    </div>

                                    <div className="mb-3 flex flex-wrap gap-2">
                                        {unit.tower_definition && (
                                            <span className="rounded-full bg-[#1B4F72]/20 px-3 py-0.5 text-xs font-medium text-[#5DADE2]">
                                                {unit.tower_definition.name}
                                            </span>
                                        )}
                                        {unit.floor_definition && (
                                            <span className="rounded-full bg-white/10 px-3 py-0.5 text-xs font-medium text-gray-300">
                                                {unit.floor_definition.name}
                                            </span>
                                        )}
                                        {unit.status && (
                                            <span
                                                className="rounded-full px-3 py-0.5 text-xs font-medium"
                                                style={
                                                    unit.status.color_code
                                                        ? { backgroundColor: unit.status.color_code + '33', color: unit.status.color_code }
                                                        : undefined
                                                }
                                            >
                                                {unit.status.name}
                                            </span>
                                        )}
                                    </div>

                                    {unit.current_stage && (
                                        <p className="mb-3 text-sm text-gray-400">
                                            Stage: <span className="text-gray-300">{unit.current_stage.name}</span>
                                        </p>
                                    )}

                                    <div className="mt-auto">
                                        <div className="mb-1 flex items-center justify-between text-xs">
                                            <span className="text-gray-400">Completion</span>
                                            <span className="font-medium text-[#1B4F72]">
                                                {unit.completion_pct != null ? `${unit.completion_pct.toFixed(0)}%` : '0%'}
                                            </span>
                                        </div>
                                        <div className="h-1.5 overflow-hidden rounded-full bg-white/10">
                                            <div
                                                className="h-full rounded-full bg-[#1B4F72] transition-all"
                                                style={{ width: `${Math.min(unit.completion_pct ?? 0, 100)}%` }}
                                            />
                                        </div>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    )}

                    {/* Pagination */}
                    {towerUnits.last_page > 1 && (
                        <div className="mt-12 flex items-center justify-between">
                            <p className="text-sm text-gray-400">
                                Showing {towerUnits.from} to {towerUnits.to} of {towerUnits.total}
                            </p>
                            <div className="flex gap-2">
                                {towerUnits.prev_page_url ? (
                                    <Link
                                        href={towerUnits.prev_page_url}
                                        preserveState
                                        className="inline-flex h-9 items-center gap-1 rounded-md border border-white/20 px-3 text-sm font-medium text-gray-300 transition-colors hover:border-[#1B4F72] hover:text-[#1B4F72]"
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
                                    Page {towerUnits.current_page} of {towerUnits.last_page}
                                </span>
                                {towerUnits.next_page_url ? (
                                    <Link
                                        href={towerUnits.next_page_url}
                                        preserveState
                                        className="inline-flex h-9 items-center gap-1 rounded-md border border-white/20 px-3 text-sm font-medium text-gray-300 transition-colors hover:border-[#1B4F72] hover:text-[#1B4F72]"
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
