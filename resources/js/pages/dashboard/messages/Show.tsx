import { Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { toast, Toaster } from 'sonner';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Calendar, Mail, Reply, Trash2, User } from 'lucide-react';

interface ContactMessage {
    id: number;
    name: string;
    email: string;
    subject: string;
    message: string;
    is_read: boolean;
    replied_at: string | null;
    admin_reply: string | null;
    created_at: string;
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

export default function MessageShow({ message }: { message: ContactMessage }) {
    const { flash } = usePage().props as unknown as { flash: { success?: string } };
    const [reply, setReply] = useState(message.admin_reply ?? '');
    const [submitting, setSubmitting] = useState(false);
    const [deleteOpen, setDeleteOpen] = useState(false);

    useEffect(() => {
        if (flash?.success) {
            toast.success(flash.success);
        }
    }, [flash?.success]);

    function handleReply(e: React.FormEvent) {
        e.preventDefault();
        setSubmitting(true);
        router.post(`/dashboard/messages/${message.id}/reply`, { admin_reply: reply }, {
            onFinish: () => setSubmitting(false),
        });
    }

    function handleDelete() {
        router.delete(`/dashboard/messages/${message.id}`);
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Messages', href: '/dashboard/messages' },
                { title: message.subject, href: `/dashboard/messages/${message.id}` },
            ]}
        >
            <Head title={`${message.subject} | Messages`} />
            <Toaster position="top-right" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <Link
                        href="/dashboard/messages"
                        className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        Back to Messages
                    </Link>

                    <Dialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                        <DialogTrigger asChild>
                            <Button variant="destructive" size="sm">
                                <Trash2 className="mr-1 h-4 w-4" />
                                Delete
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Delete this message?</DialogTitle>
                                <DialogDescription>
                                    This will permanently delete the message from {message.name}.
                                </DialogDescription>
                            </DialogHeader>
                            <DialogFooter>
                                <DialogClose asChild>
                                    <Button variant="outline">Cancel</Button>
                                </DialogClose>
                                <Button variant="destructive" onClick={handleDelete}>
                                    Delete
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>

                {/* Message Card */}
                <Card>
                    <CardHeader>
                        <div className="flex items-start justify-between">
                            <div>
                                <CardTitle className="text-xl">{message.subject}</CardTitle>
                                <div className="mt-2 flex flex-wrap items-center gap-3 text-sm text-muted-foreground">
                                    <span className="inline-flex items-center gap-1">
                                        <User className="h-3.5 w-3.5" />
                                        {message.name}
                                    </span>
                                    <span className="inline-flex items-center gap-1">
                                        <Mail className="h-3.5 w-3.5" />
                                        {message.email}
                                    </span>
                                    <span className="inline-flex items-center gap-1">
                                        <Calendar className="h-3.5 w-3.5" />
                                        {formatDate(message.created_at)}
                                    </span>
                                </div>
                            </div>
                            <div className="flex gap-2">
                                {message.is_read ? (
                                    <Badge variant="secondary">Read</Badge>
                                ) : (
                                    <Badge className="bg-mbp-gold/20 text-mbp-gold">Unread</Badge>
                                )}
                                {message.replied_at && (
                                    <Badge className="bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                        Replied
                                    </Badge>
                                )}
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <p className="whitespace-pre-line">{message.message}</p>
                    </CardContent>
                </Card>

                {/* Existing Reply */}
                {message.replied_at && message.admin_reply && (
                    <Card className="border-emerald-200 dark:border-emerald-900">
                        <CardHeader className="pb-2">
                            <CardTitle className="flex items-center gap-2 text-base">
                                <Reply className="h-4 w-4 text-emerald-600" />
                                Admin Reply
                                <span className="text-muted-foreground text-xs font-normal">
                                    {formatDate(message.replied_at)}
                                </span>
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="whitespace-pre-line">{message.admin_reply}</p>
                        </CardContent>
                    </Card>
                )}

                {/* Reply Form */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2 text-base">
                            <Reply className="h-4 w-4" />
                            {message.replied_at ? 'Update Reply' : 'Write Reply'}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleReply} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="admin_reply">Reply to {message.name}</Label>
                                <Textarea
                                    id="admin_reply"
                                    rows={5}
                                    value={reply}
                                    onChange={(e) => setReply(e.target.value)}
                                    placeholder="Type your reply..."
                                    required
                                />
                            </div>
                            <Button type="submit" disabled={submitting} className="bg-mbp-gold hover:bg-mbp-gold/90">
                                {submitting ? 'Sending...' : message.replied_at ? 'Update Reply' : 'Send Reply'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
