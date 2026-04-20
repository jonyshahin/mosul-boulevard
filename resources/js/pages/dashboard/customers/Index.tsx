import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { ChevronLeft, ChevronRight, Plus, Search, Users } from 'lucide-react';

interface Customer {
    id: number;
    name: string;
    phone: string | null;
    email: string | null;
    is_active: boolean;
    villas_count: number;
    tower_units_count: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedCustomers {
    data: Customer[];
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

interface Filters {
    search?: string;
    is_active?: string;
}

interface CustomersIndexProps {
    customers: PaginatedCustomers;
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

export default function CustomersIndex({ customers, filters }: CustomersIndexProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Customers', href: '/dashboard/customers' },
            ]}
        >
            <Head title="Customers | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <Users className="h-6 w-6 text-mbp-gold" />
                        <h1 className="text-2xl font-bold tracking-tight">Customers</h1>
                        <Badge variant="secondary" className="ml-1">
                            {customers.total}
                        </Badge>
                    </div>
                    <Button asChild className="bg-mbp-gold hover:bg-mbp-gold/90">
                        <Link href="/dashboard/customers/create">
                            <Plus className="mr-1 h-4 w-4" />
                            New Customer
                        </Link>
                    </Button>
                </div>

                {/* Search */}
                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-sm font-medium">Search</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="relative max-w-md">
                            <Search className="text-muted-foreground absolute top-1/2 left-2.5 h-4 w-4 -translate-y-1/2" />
                            <Input
                                placeholder="Search by name, phone, or email..."
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
                    </CardContent>
                </Card>

                {/* Data Table */}
                <Card>
                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Phone</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead className="text-right">Properties</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {customers.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={5} className="text-muted-foreground h-24 text-center">
                                            No customers found.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    customers.data.map((customer) => (
                                        <TableRow
                                            key={customer.id}
                                            className="cursor-pointer"
                                            onClick={() => router.get(`/dashboard/customers/${customer.id}`)}
                                        >
                                            <TableCell className="font-medium text-mbp-gold">
                                                {customer.name}
                                            </TableCell>
                                            <TableCell>{customer.phone ?? '-'}</TableCell>
                                            <TableCell>{customer.email ?? '-'}</TableCell>
                                            <TableCell className="text-right">
                                                <span className="text-muted-foreground text-xs">
                                                    {customer.villas_count} villas · {customer.tower_units_count} units
                                                </span>
                                            </TableCell>
                                            <TableCell>
                                                {customer.is_active ? (
                                                    <Badge className="bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                        Active
                                                    </Badge>
                                                ) : (
                                                    <Badge variant="outline">Inactive</Badge>
                                                )}
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Pagination */}
                {customers.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-muted-foreground text-sm">
                            Showing {customers.from} to {customers.to} of {customers.total}
                        </p>
                        <div className="flex gap-2">
                            {customers.prev_page_url ? (
                                <Link
                                    href={customers.prev_page_url}
                                    preserveState
                                    className="hover:bg-accent inline-flex h-9 items-center gap-1 rounded-md border px-3 text-sm font-medium transition-colors"
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
                                Page {customers.current_page} of {customers.last_page}
                            </span>
                            {customers.next_page_url ? (
                                <Link
                                    href={customers.next_page_url}
                                    preserveState
                                    className="hover:bg-accent inline-flex h-9 items-center gap-1 rounded-md border px-3 text-sm font-medium transition-colors"
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
