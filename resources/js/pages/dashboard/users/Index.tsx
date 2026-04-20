import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { ChevronLeft, ChevronRight, Pencil, Plus, Trash2, UserCog } from 'lucide-react';

interface UserRow {
    id: number;
    name: string;
    email: string;
    role: string;
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedUsers {
    data: UserRow[];
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

interface UsersIndexProps {
    users: PaginatedUsers;
    currentUserId: number;
}

const ROLE_VARIANT: Record<string, { className: string; label: string }> = {
    admin: {
        className: 'bg-mbp-gold text-white hover:bg-mbp-gold/90',
        label: 'Admin',
    },
    engineer: {
        className: 'bg-mbp-blue text-white hover:bg-mbp-blue/90',
        label: 'Engineer',
    },
    viewer: {
        className: 'bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200',
        label: 'Viewer',
    },
};

function RoleBadge({ role }: { role: string }) {
    const variant = ROLE_VARIANT[role] ?? { className: '', label: role };
    return <Badge className={variant.className}>{variant.label}</Badge>;
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

export default function UsersIndex({ users, currentUserId }: UsersIndexProps) {
    const [deleteTarget, setDeleteTarget] = useState<UserRow | null>(null);

    function confirmDelete() {
        if (!deleteTarget) return;
        router.delete(`/dashboard/users/${deleteTarget.id}`, {
            onFinish: () => setDeleteTarget(null),
        });
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Users', href: '/dashboard/users' },
            ]}
        >
            <Head title="Users | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <UserCog className="text-mbp-gold h-6 w-6" />
                        <h1 className="text-2xl font-bold tracking-tight">Users</h1>
                        <Badge variant="secondary" className="ml-1">
                            {users.total}
                        </Badge>
                    </div>
                    <Button asChild className="bg-mbp-gold hover:bg-mbp-gold/90">
                        <Link href="/dashboard/users/create">
                            <Plus className="mr-1 h-4 w-4" />
                            Create User
                        </Link>
                    </Button>
                </div>

                {/* Data table */}
                <Card>
                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Role</TableHead>
                                    <TableHead>Created At</TableHead>
                                    <TableHead className="text-right">Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {users.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell
                                            colSpan={5}
                                            className="text-muted-foreground h-24 text-center"
                                        >
                                            No users found.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    users.data.map((user) => {
                                        const isSelf = user.id === currentUserId;
                                        return (
                                            <TableRow key={user.id}>
                                                <TableCell className="font-medium">
                                                    {user.name}
                                                    {isSelf && (
                                                        <span className="text-muted-foreground ml-2 text-xs">
                                                            (you)
                                                        </span>
                                                    )}
                                                </TableCell>
                                                <TableCell>{user.email}</TableCell>
                                                <TableCell>
                                                    <RoleBadge role={user.role} />
                                                </TableCell>
                                                <TableCell>{formatDate(user.created_at)}</TableCell>
                                                <TableCell className="text-right">
                                                    <div className="flex justify-end gap-2">
                                                        <Button asChild variant="outline" size="sm">
                                                            <Link href={`/dashboard/users/${user.id}/edit`}>
                                                                <Pencil className="h-4 w-4" />
                                                            </Link>
                                                        </Button>
                                                        <Button
                                                            type="button"
                                                            variant="outline"
                                                            size="sm"
                                                            disabled={isSelf}
                                                            title={
                                                                isSelf
                                                                    ? 'You cannot delete your own account'
                                                                    : 'Delete user'
                                                            }
                                                            onClick={() => setDeleteTarget(user)}
                                                            className="text-destructive disabled:opacity-40"
                                                        >
                                                            <Trash2 className="h-4 w-4" />
                                                        </Button>
                                                    </div>
                                                </TableCell>
                                            </TableRow>
                                        );
                                    })
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Pagination */}
                {users.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-muted-foreground text-sm">
                            Showing {users.from} to {users.to} of {users.total}
                        </p>
                        <div className="flex gap-2">
                            {users.prev_page_url ? (
                                <Link
                                    href={users.prev_page_url}
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
                                Page {users.current_page} of {users.last_page}
                            </span>
                            {users.next_page_url ? (
                                <Link
                                    href={users.next_page_url}
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

            {/* Delete confirmation dialog */}
            <Dialog open={deleteTarget !== null} onOpenChange={(open) => !open && setDeleteTarget(null)}>
                <DialogTrigger className="hidden" />
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Delete {deleteTarget?.name}?</DialogTitle>
                        <DialogDescription>
                            This permanently deletes the user account. They will no longer be able to sign in.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter>
                        <DialogClose asChild>
                            <Button variant="outline">Cancel</Button>
                        </DialogClose>
                        <Button variant="destructive" onClick={confirmDelete}>
                            Delete User
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
