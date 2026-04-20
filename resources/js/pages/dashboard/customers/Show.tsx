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
import { ArrowLeft, Building, Building2, Mail, Pencil, Phone } from 'lucide-react';

interface StatusOption {
    id: number;
    name: string;
    color_code: string | null;
}

interface LinkedVilla {
    id: number;
    code: string;
    is_sold: boolean;
    completion_pct: number | null;
    villa_type: { id: number; name: string } | null;
    status: StatusOption | null;
}

interface LinkedTowerUnit {
    id: number;
    code: string;
    is_sold: boolean;
    completion_pct: number | null;
    tower_definition: { id: number; name: string } | null;
    status: StatusOption | null;
}

interface Customer {
    id: number;
    name: string;
    phone: string | null;
    email: string | null;
    address: string | null;
    notes: string | null;
    is_active: boolean;
    villas: LinkedVilla[];
    tower_units: LinkedTowerUnit[];
}

interface CustomerShowProps {
    customer: Customer;
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

export default function CustomerShow({ customer }: CustomerShowProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Customers', href: '/dashboard/customers' },
                { title: customer.name, href: `/dashboard/customers/${customer.id}` },
            ]}
        >
            <Head title={`${customer.name} | Mosul Boulevard`} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                {/* Back link */}
                <Link
                    href="/dashboard/customers"
                    className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Customers
                </Link>

                {/* Header card */}
                <Card>
                    <CardHeader className="pb-3">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <CardTitle className="text-xl">{customer.name}</CardTitle>
                                {customer.is_active ? (
                                    <Badge className="bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                        Active
                                    </Badge>
                                ) : (
                                    <Badge variant="outline">Inactive</Badge>
                                )}
                            </div>
                            <Button asChild variant="outline" size="sm">
                                <Link href={`/dashboard/customers/${customer.id}/edit`}>
                                    <Pencil className="mr-1 h-4 w-4" />
                                    Edit
                                </Link>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <dl className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <InfoRow label="Phone">
                                {customer.phone ? (
                                    <span className="inline-flex items-center gap-1.5">
                                        <Phone className="text-muted-foreground h-3.5 w-3.5" />
                                        {customer.phone}
                                    </span>
                                ) : (
                                    '-'
                                )}
                            </InfoRow>
                            <InfoRow label="Email">
                                {customer.email ? (
                                    <span className="inline-flex items-center gap-1.5">
                                        <Mail className="text-muted-foreground h-3.5 w-3.5" />
                                        {customer.email}
                                    </span>
                                ) : (
                                    '-'
                                )}
                            </InfoRow>
                            <InfoRow label="Address">
                                {customer.address ? (
                                    <span className="whitespace-pre-line">{customer.address}</span>
                                ) : (
                                    '-'
                                )}
                            </InfoRow>
                            <InfoRow label="Notes">
                                {customer.notes ? (
                                    <span className="whitespace-pre-line">{customer.notes}</span>
                                ) : (
                                    '-'
                                )}
                            </InfoRow>
                        </dl>
                    </CardContent>
                </Card>

                {/* Tabs for linked properties */}
                <Tabs defaultValue="villas">
                    <TabsList>
                        <TabsTrigger value="villas" className="gap-1.5">
                            <Building2 className="h-4 w-4" />
                            Villas
                            <Badge variant="secondary" className="ml-1 text-xs">
                                {customer.villas.length}
                            </Badge>
                        </TabsTrigger>
                        <TabsTrigger value="tower-units" className="gap-1.5">
                            <Building className="h-4 w-4" />
                            Tower Units
                            <Badge variant="secondary" className="ml-1 text-xs">
                                {customer.tower_units.length}
                            </Badge>
                        </TabsTrigger>
                    </TabsList>

                    {/* Villas tab */}
                    <TabsContent value="villas">
                        <Card>
                            <CardContent className="p-0">
                                {customer.villas.length === 0 ? (
                                    <div className="text-muted-foreground flex flex-col items-center gap-2 py-12 text-center">
                                        <Building2 className="h-8 w-8" />
                                        <p>No villas linked to this customer.</p>
                                    </div>
                                ) : (
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Code</TableHead>
                                                <TableHead>Type</TableHead>
                                                <TableHead>Sold</TableHead>
                                                <TableHead>Status</TableHead>
                                                <TableHead className="text-right">Completion %</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {customer.villas.map((villa) => (
                                                <TableRow
                                                    key={villa.id}
                                                    className="cursor-pointer"
                                                    onClick={() =>
                                                        (window.location.href = `/dashboard/villas/${villa.id}`)
                                                    }
                                                >
                                                    <TableCell className="text-mbp-gold font-medium">
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
                                                    <TableCell>
                                                        <StatusBadge status={villa.status} />
                                                    </TableCell>
                                                    <TableCell className="text-right">
                                                        {villa.completion_pct != null
                                                            ? `${villa.completion_pct.toFixed(0)}%`
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

                    {/* Tower units tab */}
                    <TabsContent value="tower-units">
                        <Card>
                            <CardContent className="p-0">
                                {customer.tower_units.length === 0 ? (
                                    <div className="text-muted-foreground flex flex-col items-center gap-2 py-12 text-center">
                                        <Building className="h-8 w-8" />
                                        <p>No tower units linked to this customer.</p>
                                    </div>
                                ) : (
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>Code</TableHead>
                                                <TableHead>Tower</TableHead>
                                                <TableHead>Sold</TableHead>
                                                <TableHead>Status</TableHead>
                                                <TableHead className="text-right">Completion %</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {customer.tower_units.map((unit) => (
                                                <TableRow
                                                    key={unit.id}
                                                    className="cursor-pointer"
                                                    onClick={() =>
                                                        (window.location.href = `/dashboard/tower-units/${unit.id}`)
                                                    }
                                                >
                                                    <TableCell className="text-mbp-blue font-medium">
                                                        {unit.code}
                                                    </TableCell>
                                                    <TableCell>{unit.tower_definition?.name ?? '-'}</TableCell>
                                                    <TableCell>
                                                        {unit.is_sold ? (
                                                            <Badge className="bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                                Sold
                                                            </Badge>
                                                        ) : (
                                                            <Badge variant="outline">Not Sold</Badge>
                                                        )}
                                                    </TableCell>
                                                    <TableCell>
                                                        <StatusBadge status={unit.status} />
                                                    </TableCell>
                                                    <TableCell className="text-right">
                                                        {unit.completion_pct != null
                                                            ? `${unit.completion_pct.toFixed(0)}%`
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
                </Tabs>
            </div>
        </AppLayout>
    );
}
