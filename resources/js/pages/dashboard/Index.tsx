import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Building2, Building, Users, CheckSquare, ShoppingCart } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';

interface Stats {
    total_villas: number;
    total_tower_units: number;
    villas_sold: number;
    tower_units_sold: number;
    total_engineers: number;
    total_villa_tasks: number;
    total_tower_tasks: number;
}

interface SalesSummary {
    villa_type?: string;
    tower_name?: string;
    total_sold: number;
    total_unsold: number;
    total: number;
}

interface StructuralStatus {
    status_name: string;
    type_a_count?: number;
    type_b_count?: number;
    total?: number;
}

interface DashboardProps {
    stats: Stats;
    salesChart: {
        villas: SalesSummary[];
        towers: SalesSummary[];
    };
    structuralChart: {
        villas: StructuralStatus[];
    };
}

const MBP_GOLD = '#B8860B';
const MBP_BLUE = '#1B4F72';

const kpiCards = [
    { key: 'total_villas', label: 'Total Villas', icon: Building2, color: 'text-mbp-gold' },
    { key: 'total_tower_units', label: 'Total Tower Units', icon: Building, color: 'text-mbp-blue' },
    { key: 'villas_sold', label: 'Villas Sold', icon: ShoppingCart, color: 'text-mbp-gold' },
    { key: 'tower_units_sold', label: 'Tower Units Sold', icon: ShoppingCart, color: 'text-mbp-blue' },
    { key: 'total_engineers', label: 'Active Engineers', icon: Users, color: 'text-emerald-600' },
    { key: 'total_tasks', label: 'Total Tasks', icon: CheckSquare, color: 'text-amber-600' },
] as const;

export default function DashboardIndex({ stats, salesChart, structuralChart }: DashboardProps) {
    const salesData = [
        ...salesChart.villas.map((v) => ({
            name: v.villa_type ?? 'Unknown',
            Sold: v.total_sold,
            Unsold: v.total_unsold,
        })),
        ...salesChart.towers.map((t) => ({
            name: t.tower_name ?? 'Unknown',
            Sold: t.total_sold,
            Unsold: t.total_unsold,
        })),
    ];

    const structuralData = structuralChart.villas.map((s) => ({
        name: s.status_name,
        'Type A': s.type_a_count ?? 0,
        'Type B': s.type_b_count ?? 0,
    }));

    function getStatValue(key: string): number {
        if (key === 'total_tasks') {
            return stats.total_villa_tasks + stats.total_tower_tasks;
        }
        return stats[key as keyof Stats] ?? 0;
    }

    return (
        <AppLayout breadcrumbs={[{ title: 'Dashboard', href: '/dashboard' }]}>
            <Head title="Dashboard | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                {/* KPI Cards */}
                <div className="grid grid-cols-2 gap-4 md:grid-cols-3">
                    {kpiCards.map(({ key, label, icon: Icon, color }) => (
                        <Card key={key}>
                            <CardHeader className="flex flex-row items-center justify-between pb-2">
                                <CardTitle className="text-muted-foreground text-sm font-medium">
                                    {label}
                                </CardTitle>
                                <Icon className={`h-5 w-5 ${color}`} />
                            </CardHeader>
                            <CardContent>
                                <p className="text-3xl font-bold tracking-tight">
                                    {getStatValue(key).toLocaleString()}
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {/* Charts */}
                <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    {/* Sales Overview */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Sales Overview</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ResponsiveContainer width="100%" height={300}>
                                <BarChart data={salesData}>
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis dataKey="name" fontSize={12} />
                                    <YAxis />
                                    <Tooltip />
                                    <Legend />
                                    <Bar dataKey="Sold" fill={MBP_GOLD} radius={[4, 4, 0, 0]} />
                                    <Bar dataKey="Unsold" fill={MBP_BLUE} radius={[4, 4, 0, 0]} />
                                </BarChart>
                            </ResponsiveContainer>
                        </CardContent>
                    </Card>

                    {/* Structural Progress */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Villa Structural Progress</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ResponsiveContainer width="100%" height={300}>
                                <BarChart data={structuralData}>
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis dataKey="name" fontSize={12} />
                                    <YAxis />
                                    <Tooltip />
                                    <Legend />
                                    <Bar dataKey="Type A" fill={MBP_GOLD} radius={[4, 4, 0, 0]} />
                                    <Bar dataKey="Type B" fill={MBP_BLUE} radius={[4, 4, 0, 0]} />
                                </BarChart>
                            </ResponsiveContainer>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
