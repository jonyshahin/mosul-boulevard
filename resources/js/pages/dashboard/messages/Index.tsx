import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { ChevronLeft, ChevronRight, Mail } from 'lucide-react';

interface ContactMessage {
    id: number;
    name: string;
    email: string;
    subject: string;
    is_read: boolean;
    replied_at: string | null;
    created_at: string;
}

interface PaginatedMessages {
    data: ContactMessage[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface MessagesIndexProps {
    messages: PaginatedMessages;
    unreadCount: number;
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

export default function MessagesIndex({ messages, unreadCount }: MessagesIndexProps) {
    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Messages', href: '/dashboard/messages' },
            ]}
        >
            <Head title="Messages | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center gap-3">
                    <Mail className="h-6 w-6 text-mbp-gold" />
                    <h1 className="text-2xl font-bold tracking-tight">Messages</h1>
                    <Badge variant="secondary">{messages.total}</Badge>
                    {unreadCount > 0 && (
                        <Badge className="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            {unreadCount} unread
                        </Badge>
                    )}
                </div>

                <Card>
                    <CardContent className="p-0">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-8" />
                                    <TableHead>Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Subject</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Replied</TableHead>
                                    <TableHead>Date</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {messages.data.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={7} className="text-muted-foreground h-24 text-center">
                                            No messages yet.
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    messages.data.map((msg) => (
                                        <TableRow
                                            key={msg.id}
                                            className={`cursor-pointer ${!msg.is_read ? 'bg-mbp-gold/5 font-medium' : ''}`}
                                            onClick={() => router.get(`/dashboard/messages/${msg.id}`)}
                                        >
                                            <TableCell>
                                                {!msg.is_read && (
                                                    <span className="inline-block h-2 w-2 rounded-full bg-mbp-gold" />
                                                )}
                                            </TableCell>
                                            <TableCell className={!msg.is_read ? 'font-semibold' : ''}>
                                                {msg.name}
                                            </TableCell>
                                            <TableCell className="text-muted-foreground">{msg.email}</TableCell>
                                            <TableCell className={!msg.is_read ? 'font-semibold' : ''}>
                                                {msg.subject}
                                            </TableCell>
                                            <TableCell>
                                                {msg.is_read ? (
                                                    <Badge variant="secondary">Read</Badge>
                                                ) : (
                                                    <Badge className="bg-mbp-gold/20 text-mbp-gold">Unread</Badge>
                                                )}
                                            </TableCell>
                                            <TableCell>
                                                {msg.replied_at ? (
                                                    <Badge className="bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                                        Yes
                                                    </Badge>
                                                ) : (
                                                    <Badge variant="outline">No</Badge>
                                                )}
                                            </TableCell>
                                            <TableCell className="text-muted-foreground text-sm">
                                                {formatDate(msg.created_at)}
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {messages.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-muted-foreground text-sm">
                            Showing {messages.from} to {messages.to} of {messages.total}
                        </p>
                        <div className="flex gap-2">
                            {messages.prev_page_url ? (
                                <Link href={messages.prev_page_url} preserveState className="inline-flex h-9 items-center gap-1 rounded-md border px-3 text-sm font-medium transition-colors hover:bg-accent">
                                    <ChevronLeft className="h-4 w-4" /> Previous
                                </Link>
                            ) : (
                                <span className="text-muted-foreground inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-md border px-3 text-sm font-medium opacity-50">
                                    <ChevronLeft className="h-4 w-4" /> Previous
                                </span>
                            )}
                            <span className="text-muted-foreground inline-flex h-9 items-center px-2 text-sm">
                                Page {messages.current_page} of {messages.last_page}
                            </span>
                            {messages.next_page_url ? (
                                <Link href={messages.next_page_url} preserveState className="inline-flex h-9 items-center gap-1 rounded-md border px-3 text-sm font-medium transition-colors hover:bg-accent">
                                    Next <ChevronRight className="h-4 w-4" />
                                </Link>
                            ) : (
                                <span className="text-muted-foreground inline-flex h-9 cursor-not-allowed items-center gap-1 rounded-md border px-3 text-sm font-medium opacity-50">
                                    Next <ChevronRight className="h-4 w-4" />
                                </span>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
