import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { BarChart3 } from 'lucide-react';
import {
    BarChart,
    Bar,
    PieChart,
    Pie,
    Cell,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
    ResponsiveContainer,
} from 'recharts';

const MBP_GOLD = '#B8860B';
const MBP_BLUE = '#1B4F72';
const MBP_DARK = '#1B1B1B';
const GRAY = '#9CA3AF';

const TOWER_COLORS = ['#B8860B', '#1B4F72', '#D4A843', '#2E86C1', '#7D6608', '#5DADE2'];

interface VillaSalesSummary {
    villa_type_id: number;
    villa_type_name: string;
    total_sold: number;
    total_unsold: number;
    total: number;
}

interface TowerSalesSummary {
    tower_definition_id: number;
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

interface ReportsProps {
    salesVillas: VillaSalesSummary[];
    salesTowers: TowerSalesSummary[];
    structuralVillas: VillaStatusRow[];
    structuralTowers: TowerStatusRow[];
    finishingVillas: VillaStatusRow[];
    finishingTowers: TowerStatusRow[];
    facadeVillas: VillaStatusRow[];
    facadeTowers: TowerStatusRow[];
}

function VillaSalesPieChart({ data }: { data: VillaSalesSummary[] }) {
    const pieData = data.flatMap((row) => [
        { name: `${row.villa_type_name} Sold`, value: Number(row.total_sold) },
        { name: `${row.villa_type_name} Unsold`, value: Number(row.total_unsold) },
    ]);

    const colors = data.flatMap(() => [MBP_GOLD, GRAY]);

    return (
        <ResponsiveContainer width="100%" height={300}>
            <PieChart>
                <Pie
                    data={pieData}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={100}
                    paddingAngle={2}
                    dataKey="value"
                    label={({ name, value }) => `${name}: ${value}`}
                >
                    {pieData.map((_, i) => (
                        <Cell key={i} fill={colors[i]} />
                    ))}
                </Pie>
                <Tooltip />
                <Legend />
            </PieChart>
        </ResponsiveContainer>
    );
}

function TowerSalesPieChart({ data }: { data: TowerSalesSummary[] }) {
    const pieData = data.map((row) => ({
        name: `${row.tower_name} Sold`,
        value: Number(row.total_sold),
        unsold: Number(row.total_unsold),
    }));

    const allData = data.flatMap((row) => [
        { name: `${row.tower_name} Sold`, value: Number(row.total_sold) },
        { name: `${row.tower_name} Unsold`, value: Number(row.total_unsold) },
    ]);

    const colors = data.flatMap((_, i) => [TOWER_COLORS[i % TOWER_COLORS.length], GRAY]);

    return (
        <ResponsiveContainer width="100%" height={300}>
            <PieChart>
                <Pie
                    data={allData}
                    cx="50%"
                    cy="50%"
                    innerRadius={60}
                    outerRadius={100}
                    paddingAngle={2}
                    dataKey="value"
                    label={({ name, value }) => value > 0 ? `${name}: ${value}` : ''}
                >
                    {allData.map((_, i) => (
                        <Cell key={i} fill={colors[i]} />
                    ))}
                </Pie>
                <Tooltip />
                <Legend />
            </PieChart>
        </ResponsiveContainer>
    );
}

function VillaStatusChart({ data, title }: { data: VillaStatusRow[]; title: string }) {
    const chartData = data.map((row) => ({
        name: row.status_name,
        'Type A': Number(row.type_a_count),
        'Type B': Number(row.type_b_count),
    }));

    return (
        <Card>
            <CardHeader>
                <CardTitle className="text-base">{title}</CardTitle>
            </CardHeader>
            <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                    <BarChart data={chartData} layout="vertical" margin={{ left: 20 }}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis type="number" />
                        <YAxis type="category" dataKey="name" fontSize={12} width={100} />
                        <Tooltip />
                        <Legend />
                        <Bar dataKey="Type A" fill={MBP_GOLD} radius={[0, 4, 4, 0]} />
                        <Bar dataKey="Type B" fill={MBP_BLUE} radius={[0, 4, 4, 0]} />
                    </BarChart>
                </ResponsiveContainer>
            </CardContent>
        </Card>
    );
}

function TowerStatusChart({ data, title }: { data: TowerStatusRow[]; title: string }) {
    const chartData = data.map((row) => ({
        name: row.status_name,
        'Tower 1': Number(row.tower_1),
        'Tower 2': Number(row.tower_2),
        'Tower 3': Number(row.tower_3),
        'Tower 4': Number(row.tower_4),
        'Tower 5': Number(row.tower_5),
        'Tower 6': Number(row.tower_6),
    }));

    return (
        <Card>
            <CardHeader>
                <CardTitle className="text-base">{title}</CardTitle>
            </CardHeader>
            <CardContent>
                <ResponsiveContainer width="100%" height={300}>
                    <BarChart data={chartData}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="name" fontSize={12} />
                        <YAxis />
                        <Tooltip />
                        <Legend />
                        {['Tower 1', 'Tower 2', 'Tower 3', 'Tower 4', 'Tower 5', 'Tower 6'].map(
                            (key, i) => (
                                <Bar
                                    key={key}
                                    dataKey={key}
                                    fill={TOWER_COLORS[i]}
                                    radius={[4, 4, 0, 0]}
                                />
                            ),
                        )}
                    </BarChart>
                </ResponsiveContainer>
            </CardContent>
        </Card>
    );
}

export default function ReportsIndex({
    salesVillas,
    salesTowers,
    structuralVillas,
    structuralTowers,
    finishingVillas,
    finishingTowers,
    facadeVillas,
    facadeTowers,
}: ReportsProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Reports', href: '/dashboard/reports' },
            ]}
        >
            <Head title="Reports | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                {/* Header */}
                <div className="flex items-center gap-3">
                    <BarChart3 className="h-6 w-6 text-mbp-gold" />
                    <h1 className="text-2xl font-bold tracking-tight">Reports</h1>
                </div>

                {/* Tabs */}
                <Tabs defaultValue="sales">
                    <TabsList>
                        <TabsTrigger value="sales">Sales</TabsTrigger>
                        <TabsTrigger value="structural">Structural</TabsTrigger>
                        <TabsTrigger value="finishing">Finishing</TabsTrigger>
                        <TabsTrigger value="facade">Facade</TabsTrigger>
                    </TabsList>

                    {/* Sales Tab */}
                    <TabsContent value="sales">
                        <div className="grid gap-4 lg:grid-cols-2">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-base">Villas Sales</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <VillaSalesPieChart data={salesVillas} />
                                </CardContent>
                            </Card>
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-base">Towers Sales</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <TowerSalesPieChart data={salesTowers} />
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>

                    {/* Structural Tab */}
                    <TabsContent value="structural">
                        <div className="grid gap-4 lg:grid-cols-2">
                            <VillaStatusChart data={structuralVillas} title="Villas Structural Status" />
                            <TowerStatusChart data={structuralTowers} title="Towers Structural Status" />
                        </div>
                    </TabsContent>

                    {/* Finishing Tab */}
                    <TabsContent value="finishing">
                        <div className="grid gap-4 lg:grid-cols-2">
                            <VillaStatusChart data={finishingVillas} title="Villas Finishing Status" />
                            <TowerStatusChart data={finishingTowers} title="Towers Finishing Status" />
                        </div>
                    </TabsContent>

                    {/* Facade Tab */}
                    <TabsContent value="facade">
                        <div className="grid gap-4 lg:grid-cols-2">
                            <VillaStatusChart data={facadeVillas} title="Villas Facade Status" />
                            <TowerStatusChart data={facadeTowers} title="Towers Facade Status" />
                        </div>
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}
