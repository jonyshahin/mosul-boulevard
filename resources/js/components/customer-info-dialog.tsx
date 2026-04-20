import { Link } from '@inertiajs/react';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { ExternalLink, Mail, MapPin, Phone, StickyNote } from 'lucide-react';

export interface CustomerSummary {
    id: number;
    name: string;
    phone: string | null;
    email: string | null;
    address: string | null;
    notes: string | null;
}

interface CustomerInfoDialogProps {
    customer: CustomerSummary | null;
    fallbackName?: string | null;
}

export function CustomerInfoDialog({ customer, fallbackName }: CustomerInfoDialogProps) {
    // No linked customer record — render the legacy free-text name (or dash) as plain text.
    if (!customer) {
        return <span>{fallbackName ?? '-'}</span>;
    }

    return (
        <Dialog>
            <DialogTrigger asChild>
                <button
                    type="button"
                    className="text-mbp-gold hover:text-mbp-gold/80 inline-flex items-center gap-1 text-left font-medium underline-offset-4 hover:underline"
                >
                    {customer.name}
                </button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{customer.name}</DialogTitle>
                    <DialogDescription>Customer contact details</DialogDescription>
                </DialogHeader>

                <dl className="space-y-3 text-sm">
                    <div className="flex items-start gap-2">
                        <Phone className="text-muted-foreground mt-0.5 h-4 w-4 shrink-0" />
                        <div className="flex-1">
                            <dt className="text-muted-foreground text-xs">Phone</dt>
                            <dd>
                                {customer.phone ? (
                                    <a
                                        href={`tel:${customer.phone}`}
                                        className="text-mbp-blue font-medium hover:underline"
                                    >
                                        {customer.phone}
                                    </a>
                                ) : (
                                    <span className="text-muted-foreground">-</span>
                                )}
                            </dd>
                        </div>
                    </div>

                    <div className="flex items-start gap-2">
                        <Mail className="text-muted-foreground mt-0.5 h-4 w-4 shrink-0" />
                        <div className="flex-1">
                            <dt className="text-muted-foreground text-xs">Email</dt>
                            <dd>
                                {customer.email ? (
                                    <a
                                        href={`mailto:${customer.email}`}
                                        className="text-mbp-blue font-medium hover:underline"
                                    >
                                        {customer.email}
                                    </a>
                                ) : (
                                    <span className="text-muted-foreground">-</span>
                                )}
                            </dd>
                        </div>
                    </div>

                    <div className="flex items-start gap-2">
                        <MapPin className="text-muted-foreground mt-0.5 h-4 w-4 shrink-0" />
                        <div className="flex-1">
                            <dt className="text-muted-foreground text-xs">Address</dt>
                            <dd>
                                {customer.address ? (
                                    <span className="whitespace-pre-line">{customer.address}</span>
                                ) : (
                                    <span className="text-muted-foreground">-</span>
                                )}
                            </dd>
                        </div>
                    </div>

                    <div className="flex items-start gap-2">
                        <StickyNote className="text-muted-foreground mt-0.5 h-4 w-4 shrink-0" />
                        <div className="flex-1">
                            <dt className="text-muted-foreground text-xs">Notes</dt>
                            <dd>
                                {customer.notes ? (
                                    <span className="whitespace-pre-line">{customer.notes}</span>
                                ) : (
                                    <span className="text-muted-foreground">-</span>
                                )}
                            </dd>
                        </div>
                    </div>
                </dl>

                <DialogFooter>
                    <Link
                        href={`/dashboard/customers/${customer.id}`}
                        className="text-mbp-gold hover:text-mbp-gold/80 inline-flex items-center gap-1 text-sm font-medium hover:underline"
                    >
                        View Full Profile
                        <ExternalLink className="h-3.5 w-3.5" />
                    </Link>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
