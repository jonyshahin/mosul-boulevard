import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowLeft,
    CalendarDays,
    ChevronDown,
    ClipboardList,
    Image as ImageIcon,
    MapPin,
    MessageSquare,
    Send,
    User as UserIcon,
} from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { toast, Toaster } from 'sonner';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import { cn } from '@/lib/utils';
import type {
    InspectionRequestDetail,
    InspectionSharedTranslations,
    RequestReplyItem,
    SeverityValue,
    StatusValue,
} from '@/types/inspection-requests';

type DetailTranslations = {
    back: string;
    requester: string;
    assignee: string;
    verified_by: string;
    created: string;
    due_date: string;
    no_due_date: string;
    location: string;
    description: string;
    unassigned: string;
    subject: {
        label: string;
        link_villa: string;
        link_tower_unit: string;
    };
    media_placeholder: string;
    replies: {
        timeline_title: string;
        empty: string;
        by: string;
        status_changed_to: string;
        media_count: string;
    };
    reply_form: {
        title: string;
        placeholder: string;
        submit_button: string;
        submitting: string;
        status_change_optional: string;
        status_change_none: string;
        success_toast: string;
    };
    transitions: {
        button: string;
        modal_title: string;
        modal_description: string;
        note_label: string;
        note_placeholder: string;
        confirm_button: string;
        cancel_button: string;
        success_toast: string;
        error_toast: string;
        no_options: string;
    };
};

type PageProps = {
    request: InspectionRequestDetail;
    translations: {
        title: string;
        detail: DetailTranslations;
        shared: InspectionSharedTranslations;
    };
    auth: {
        id: number | null;
        can_reply: boolean;
        can_update: boolean;
        can_delete: boolean;
        can_transition_to: Partial<Record<StatusValue, boolean>>;
    };
};

const SEVERITY_CLASS: Record<SeverityValue, string> = {
    low: 'bg-slate-100 text-slate-700',
    medium: 'bg-blue-100 text-blue-800',
    high: 'bg-orange-100 text-orange-900',
    critical: 'bg-red-100 text-red-900',
};

const STATUS_CLASS: Record<StatusValue, string> = {
    open: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-amber-100 text-amber-900',
    resolved: 'bg-emerald-100 text-emerald-900',
    verified: 'bg-emerald-200 text-emerald-900',
    closed: 'bg-slate-200 text-slate-700',
    reopened: 'bg-red-100 text-red-900',
};

function formatDate(iso: string | null): string {
    if (!iso) {
        return '—';
    }

    return new Date(iso).toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatRelative(iso: string): string {
    const date = new Date(iso);
    const diffMs = Date.now() - date.getTime();
    const diffMin = Math.round(diffMs / 60000);

    if (diffMin < 1) {
        return 'just now';
    }

    if (diffMin < 60) {
        return `${diffMin}m ago`;
    }

    const diffHr = Math.round(diffMin / 60);

    if (diffHr < 24) {
        return `${diffHr}h ago`;
    }

    const diffDay = Math.round(diffHr / 24);

    if (diffDay < 30) {
        return `${diffDay}d ago`;
    }

    return date.toLocaleDateString();
}

function Initials({ name }: { name: string }) {
    const initials = name
        .split(' ')
        .map((part) => part[0])
        .filter(Boolean)
        .slice(0, 2)
        .join('')
        .toUpperCase();

    return (
        <span className="bg-muted text-muted-foreground inline-flex h-8 w-8 items-center justify-center rounded-full text-xs font-medium">
            {initials || '?'}
        </span>
    );
}

export default function InspectionRequestsShow({ request, translations, auth }: PageProps) {
    const t = translations.detail;
    const shared = translations.shared;
    const { flash } = usePage().props as unknown as {
        flash?: { success?: string; error?: string };
    };

    useEffect(() => {
        if (flash?.success) {
            toast.success(flash.success);
        }

        if (flash?.error) {
            toast.error(flash.error);
        }
    }, [flash?.success, flash?.error]);

    const availableTransitions = useMemo(
        () =>
            (Object.entries(auth.can_transition_to) as [StatusValue, boolean][])
                .filter(([, allowed]) => allowed)
                .map(([status]) => status),
        [auth.can_transition_to],
    );

    const subjectLinkHref = useMemo(() => {
        if (!request.subject) {
            return null;
        }

        if (request.subject.type === 'villa') {
            return `/dashboard/villas/${request.subject.id}`;
        }

        if (request.subject.type === 'tower_unit') {
            return `/dashboard/tower-units/${request.subject.id}`;
        }

        return null;
    }, [request.subject]);

    const subjectLinkLabel =
        request.subject?.type === 'tower_unit' ? t.subject.link_tower_unit : t.subject.link_villa;

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Inspections', href: '/dashboard/inspection-requests' },
                { title: `#${request.id}`, href: `/dashboard/inspection-requests/${request.id}` },
            ]}
        >
            <Head title={`${translations.title} | Mosul Boulevard`} />
            <Toaster position="top-right" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center justify-between">
                    <Link
                        href="/dashboard/inspection-requests"
                        className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                    >
                        <ArrowLeft className="h-4 w-4" />
                        {t.back}
                    </Link>

                    <TransitionMenu
                        available={availableTransitions}
                        requestId={request.id}
                        shared={shared}
                        t={t.transitions}
                    />
                </div>

                {/* Header card */}
                <Card>
                    <CardHeader className="space-y-3">
                        <div className="flex items-start justify-between gap-4">
                            <div className="flex-1 space-y-2">
                                <div className="flex flex-wrap items-center gap-2">
                                    <Badge className={cn('font-medium', SEVERITY_CLASS[request.severity.value])}>
                                        {request.severity.label}
                                    </Badge>
                                    <Badge className={cn('font-medium', STATUS_CLASS[request.status.value])}>
                                        {request.status.label}
                                    </Badge>
                                    {request.request_type && (
                                        <span
                                            className="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            style={{
                                                backgroundColor: `${request.request_type.color}22`,
                                                color: request.request_type.color,
                                            }}
                                        >
                                            {request.request_type.name}
                                        </span>
                                    )}
                                    {request.is_overdue && (
                                        <Badge className="bg-red-100 text-red-800">
                                            <AlertTriangle className="me-1 h-3 w-3" />
                                            Overdue
                                        </Badge>
                                    )}
                                </div>
                                <CardTitle className="text-2xl">{request.title}</CardTitle>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <dl className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <InfoRow icon={<UserIcon className="h-4 w-4" />} label={t.requester}>
                                {request.requester?.name ?? '—'}
                            </InfoRow>
                            <InfoRow icon={<UserIcon className="h-4 w-4" />} label={t.assignee}>
                                {request.assignee?.name ?? t.unassigned}
                            </InfoRow>
                            <InfoRow icon={<CalendarDays className="h-4 w-4" />} label={t.created}>
                                {formatDate(request.created_at)}
                            </InfoRow>
                            <InfoRow icon={<CalendarDays className="h-4 w-4" />} label={t.due_date}>
                                {request.due_date ?? t.no_due_date}
                            </InfoRow>
                            {request.location_detail && (
                                <InfoRow icon={<MapPin className="h-4 w-4" />} label={t.location}>
                                    {request.location_detail}
                                </InfoRow>
                            )}
                            {request.verified_by && (
                                <InfoRow icon={<UserIcon className="h-4 w-4" />} label={t.verified_by}>
                                    {request.verified_by.name}
                                </InfoRow>
                            )}
                        </dl>
                    </CardContent>
                </Card>

                {/* Subject + description */}
                <div className="grid gap-6 lg:grid-cols-3">
                    <Card className="lg:col-span-1">
                        <CardHeader>
                            <CardTitle className="text-base">{t.subject.label}</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {request.subject ? (
                                <>
                                    <div className="flex items-center gap-2">
                                        <ClipboardList className="text-muted-foreground h-5 w-5" />
                                        <span className="font-medium">{request.subject.display_name}</span>
                                    </div>
                                    {subjectLinkHref && (
                                        <Link
                                            href={subjectLinkHref}
                                            className="text-mbp-gold text-sm hover:underline"
                                        >
                                            {subjectLinkLabel} →
                                        </Link>
                                    )}
                                </>
                            ) : (
                                <span className="text-muted-foreground">—</span>
                            )}
                        </CardContent>
                    </Card>

                    <Card className="lg:col-span-2">
                        <CardHeader>
                            <CardTitle className="text-base">{t.description}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm whitespace-pre-wrap">{request.description}</p>
                        </CardContent>
                    </Card>
                </div>

                {/* Media placeholder */}
                <Card className="border-dashed">
                    <CardContent className="flex items-center gap-3 py-4">
                        <ImageIcon className="text-muted-foreground h-5 w-5" />
                        <p className="text-muted-foreground text-sm">
                            {t.media_placeholder.replace(':count', String(request.media_count ?? 0))}
                        </p>
                    </CardContent>
                </Card>

                {/* Replies timeline */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2 text-base">
                            <MessageSquare className="h-5 w-5" />
                            {t.replies.timeline_title} ({request.replies.length})
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        {request.replies.length === 0 ? (
                            <p className="text-muted-foreground py-6 text-center text-sm">{t.replies.empty}</p>
                        ) : (
                            request.replies.map((reply) => (
                                <ReplyItem key={reply.id} reply={reply} t={t} />
                            ))
                        )}

                        {auth.can_reply && (
                            <ReplyForm
                                requestId={request.id}
                                t={t.reply_form}
                                shared={shared}
                                canTransitionTo={auth.can_transition_to}
                            />
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}

function InfoRow({
    icon,
    label,
    children,
}: {
    icon: React.ReactNode;
    label: string;
    children: React.ReactNode;
}) {
    return (
        <div className="flex flex-col gap-1">
            <dt className="text-muted-foreground flex items-center gap-1.5 text-xs font-medium uppercase tracking-wide">
                {icon}
                {label}
            </dt>
            <dd className="text-sm font-medium">{children}</dd>
        </div>
    );
}

function ReplyItem({ reply, t }: { reply: RequestReplyItem; t: DetailTranslations }) {
    return (
        <div className="flex gap-3">
            <Initials name={reply.author?.name ?? '?'} />
            <div className="flex-1 space-y-1">
                <div className="flex flex-wrap items-center gap-2">
                    <span className="text-sm font-medium">{reply.author?.name ?? '—'}</span>
                    <span className="text-muted-foreground text-xs">
                        {formatRelative(reply.created_at)}
                    </span>
                    {reply.triggers_status && (
                        <Badge variant="outline" className="text-xs">
                            {t.replies.status_changed_to.replace(
                                ':status',
                                reply.triggers_status.label,
                            )}
                        </Badge>
                    )}
                </div>
                <p className="text-sm whitespace-pre-wrap">{reply.body}</p>
            </div>
        </div>
    );
}

function ReplyForm({
    requestId,
    t,
    shared,
    canTransitionTo,
}: {
    requestId: number;
    t: DetailTranslations['reply_form'];
    shared: InspectionSharedTranslations;
    canTransitionTo: Partial<Record<StatusValue, boolean>>;
}) {
    const [body, setBody] = useState('');
    const [triggersStatus, setTriggersStatus] = useState<string>('none');
    const [submitting, setSubmitting] = useState(false);

    const transitionOptions = (Object.entries(canTransitionTo) as [StatusValue, boolean][])
        .filter(([, allowed]) => allowed)
        .map(([status]) => status);

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();

        if (!body.trim() || submitting) {
return;
}

        setSubmitting(true);
        const payload: Record<string, string> = { body };

        if (triggersStatus !== 'none') {
payload.triggers_status = triggersStatus;
}

        router.post(`/dashboard/inspection-requests/${requestId}/replies`, payload, {
            preserveScroll: true,
            onSuccess: () => {
                setBody('');
                setTriggersStatus('none');
            },
            onError: () => {
                toast.error(shared.errors.validation_failed);
            },
            onFinish: () => setSubmitting(false),
        });
    }

    return (
        <form onSubmit={handleSubmit} className="border-t pt-4">
            <div className="space-y-3">
                <div>
                    <Label htmlFor="reply-body" className="mb-1.5 block text-sm font-medium">
                        {t.title}
                    </Label>
                    <Textarea
                        id="reply-body"
                        value={body}
                        onChange={(e) => setBody(e.target.value)}
                        placeholder={t.placeholder}
                        rows={3}
                        maxLength={5000}
                        disabled={submitting}
                    />
                </div>

                {transitionOptions.length > 0 && (
                    <div>
                        <Label htmlFor="reply-triggers-status" className="mb-1.5 block text-sm font-medium">
                            {t.status_change_optional}
                        </Label>
                        <Select
                            value={triggersStatus}
                            onValueChange={setTriggersStatus}
                            disabled={submitting}
                        >
                            <SelectTrigger id="reply-triggers-status" className="w-full sm:w-64">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">{t.status_change_none}</SelectItem>
                                {transitionOptions.map((s) => (
                                    <SelectItem key={s} value={s}>
                                        {shared.status[s]}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                )}

                <div className="flex justify-end">
                    <Button type="submit" disabled={!body.trim() || submitting}>
                        <Send className="me-1 h-4 w-4" />
                        {submitting ? t.submitting : t.submit_button}
                    </Button>
                </div>
            </div>
        </form>
    );
}

function TransitionMenu({
    available,
    requestId,
    shared,
    t,
}: {
    available: StatusValue[];
    requestId: number;
    shared: InspectionSharedTranslations;
    t: DetailTranslations['transitions'];
}) {
    const [openStatus, setOpenStatus] = useState<StatusValue | null>(null);
    const [note, setNote] = useState('');
    const [submitting, setSubmitting] = useState(false);

    function handleConfirm() {
        if (!openStatus || submitting) {
            return;
        }

        setSubmitting(true);
        router.post(
            `/dashboard/inspection-requests/${requestId}/transition`,
            { target_status: openStatus, note: note || undefined },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setOpenStatus(null);
                    setNote('');
                },
                onError: () => {
                    toast.error(t.error_toast);
                },
                onFinish: () => setSubmitting(false),
            },
        );
    }

    if (available.length === 0) {
        return null;
    }

    return (
        <>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button size="sm" variant="outline">
                        {t.button}
                        <ChevronDown className="ms-1 h-4 w-4" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    {available.map((status) => (
                        <DropdownMenuItem key={status} onClick={() => setOpenStatus(status)}>
                            {shared.status[status]}
                        </DropdownMenuItem>
                    ))}
                </DropdownMenuContent>
            </DropdownMenu>

            <Dialog open={openStatus !== null} onOpenChange={(open) => !open && setOpenStatus(null)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {openStatus &&
                                t.modal_title.replace(':status', shared.status[openStatus])}
                        </DialogTitle>
                        <DialogDescription>{t.modal_description}</DialogDescription>
                    </DialogHeader>
                    <div className="space-y-2">
                        <Label htmlFor="transition-note" className="text-sm">
                            {t.note_label}
                        </Label>
                        <Textarea
                            id="transition-note"
                            value={note}
                            onChange={(e) => setNote(e.target.value)}
                            placeholder={t.note_placeholder}
                            rows={3}
                            disabled={submitting}
                        />
                    </div>
                    <DialogFooter>
                        <Button
                            variant="outline"
                            onClick={() => setOpenStatus(null)}
                            disabled={submitting}
                        >
                            {t.cancel_button}
                        </Button>
                        <Button onClick={handleConfirm} disabled={submitting}>
                            {t.confirm_button}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}
