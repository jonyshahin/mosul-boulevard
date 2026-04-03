import { Head } from '@inertiajs/react';
import { Building, Building2, HardHat } from 'lucide-react';
import WebsiteLayout from '@/layouts/website-layout';

interface VillaSalesSummary {
    villa_type_name: string;
    total_sold: number;
    total_unsold: number;
    total: number;
}

interface TowerSalesSummary {
    tower_name: string;
    total_sold: number;
    total_unsold: number;
    total: number;
}

interface VillaStatusRow {
    status_name: string;
    type_a_count: number;
    type_b_count: number;
    total: number;
}

interface TowerStatusRow {
    status_name: string;
    tower_1: number;
    tower_2: number;
    tower_3: number;
    tower_4: number;
    tower_5: number;
    tower_6: number;
    total: number;
}

interface ProgressProps {
    salesVillas: VillaSalesSummary[];
    salesTowers: TowerSalesSummary[];
    structuralVillas: VillaStatusRow[];
    structuralTowers: TowerStatusRow[];
    finishingVillas: VillaStatusRow[];
    finishingTowers: TowerStatusRow[];
    facadeVillas: VillaStatusRow[];
    facadeTowers: TowerStatusRow[];
}

function ProgressBar({ value, max, color }: { value: number; max: number; color: string }) {
    const pct = max > 0 ? (Number(value) / Number(max)) * 100 : 0;
    return (
        <div className="flex items-center gap-3">
            <div className="h-2 flex-1 overflow-hidden rounded-full bg-white/10">
                <div className="h-full rounded-full transition-all" style={{ width: `${pct}%`, backgroundColor: color }} />
            </div>
            <span className="w-12 text-right text-xs font-medium text-gray-400">
                {Number(value)}/{Number(max)}
            </span>
        </div>
    );
}

function StatusSection({
    title,
    icon: Icon,
    villaData,
    towerData,
}: {
    title: string;
    icon: React.ElementType;
    villaData: VillaStatusRow[];
    towerData: TowerStatusRow[];
}) {
    return (
        <section className="py-12">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 className="mb-6 flex items-center gap-2 text-2xl font-bold">
                    <Icon className="h-6 w-6 text-[#B8860B]" />
                    {title}
                </h2>
                <div className="grid gap-6 lg:grid-cols-2">
                    {/* Villas */}
                    <div className="rounded-xl border border-white/10 bg-white/5 p-6">
                        <h3 className="mb-4 flex items-center gap-2 text-lg font-semibold">
                            <Building2 className="h-5 w-5 text-[#B8860B]" />
                            Villas
                        </h3>
                        {villaData.length === 0 ? (
                            <p className="text-sm text-gray-500">No data available</p>
                        ) : (
                            <div className="space-y-4">
                                {villaData.map((row) => (
                                    <div key={row.status_name}>
                                        <div className="mb-1.5 flex items-center justify-between">
                                            <span className="text-sm text-gray-300">{row.status_name}</span>
                                            <span className="text-xs text-gray-500">
                                                A: {Number(row.type_a_count)} &bull; B: {Number(row.type_b_count)}
                                            </span>
                                        </div>
                                        <ProgressBar
                                            value={row.total}
                                            max={villaData.reduce((s, r) => s + Number(r.total), 0)}
                                            color="#B8860B"
                                        />
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Towers */}
                    <div className="rounded-xl border border-white/10 bg-white/5 p-6">
                        <h3 className="mb-4 flex items-center gap-2 text-lg font-semibold">
                            <Building className="h-5 w-5 text-[#1B4F72]" />
                            Towers
                        </h3>
                        {towerData.length === 0 ? (
                            <p className="text-sm text-gray-500">No data available</p>
                        ) : (
                            <div className="space-y-4">
                                {towerData.map((row) => (
                                    <div key={row.status_name}>
                                        <div className="mb-1.5 flex items-center justify-between">
                                            <span className="text-sm text-gray-300">{row.status_name}</span>
                                            <span className="text-xs text-gray-500">
                                                Total: {Number(row.total)}
                                            </span>
                                        </div>
                                        <ProgressBar
                                            value={row.total}
                                            max={towerData.reduce((s, r) => s + Number(r.total), 0)}
                                            color="#1B4F72"
                                        />
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}

export default function Progress({
    salesVillas,
    salesTowers,
    structuralVillas,
    structuralTowers,
    finishingVillas,
    finishingTowers,
    facadeVillas,
    facadeTowers,
}: ProgressProps) {
    return (
        <WebsiteLayout>
            <Head title="Construction Progress | Mosul Boulevard" />

            {/* Hero */}
            <section className="border-b border-white/10 bg-gradient-to-b from-[#1B1B1B] to-[#1B1B1B]/95 px-4 py-20">
                <div className="mx-auto max-w-7xl text-center">
                    <HardHat className="mx-auto mb-4 h-10 w-10 text-[#B8860B]" />
                    <h1 className="mb-2 text-4xl font-bold">Construction Progress</h1>
                    <p className="text-lg text-gray-400">Real-time updates on our development</p>
                </div>
            </section>

            {/* Sales Overview */}
            <section className="border-b border-white/10 py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h2 className="mb-6 flex items-center gap-2 text-2xl font-bold">
                        <Building2 className="h-6 w-6 text-[#B8860B]" />
                        Sales Overview
                    </h2>
                    <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        {salesVillas.map((row) => (
                            <div key={row.villa_type_name} className="rounded-xl border border-white/10 bg-white/5 p-6">
                                <h3 className="mb-3 text-sm font-medium text-gray-400">{row.villa_type_name}</h3>
                                <div className="mb-3 flex items-baseline gap-2">
                                    <span className="text-3xl font-bold text-[#B8860B]">{Number(row.total_sold)}</span>
                                    <span className="text-sm text-gray-500">/ {Number(row.total)} sold</span>
                                </div>
                                <ProgressBar value={row.total_sold} max={row.total} color="#B8860B" />
                            </div>
                        ))}
                        {salesTowers.map((row) => (
                            <div key={row.tower_name} className="rounded-xl border border-white/10 bg-white/5 p-6">
                                <h3 className="mb-3 text-sm font-medium text-gray-400">{row.tower_name}</h3>
                                <div className="mb-3 flex items-baseline gap-2">
                                    <span className="text-3xl font-bold text-[#1B4F72]">{Number(row.total_sold)}</span>
                                    <span className="text-sm text-gray-500">/ {Number(row.total)} sold</span>
                                </div>
                                <ProgressBar value={row.total_sold} max={row.total} color="#1B4F72" />
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Status Sections */}
            <div className="divide-y divide-white/10">
                <StatusSection
                    title="Structural Status"
                    icon={HardHat}
                    villaData={structuralVillas}
                    towerData={structuralTowers}
                />
                <StatusSection
                    title="Finishing Status"
                    icon={HardHat}
                    villaData={finishingVillas}
                    towerData={finishingTowers}
                />
                <StatusSection
                    title="Facade Status"
                    icon={HardHat}
                    villaData={facadeVillas}
                    towerData={facadeTowers}
                />
            </div>
        </WebsiteLayout>
    );
}
