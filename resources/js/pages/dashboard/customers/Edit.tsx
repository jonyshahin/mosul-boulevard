import { Head, Link, router } from '@inertiajs/react';
import { zodResolver } from '@hookform/resolvers/zod';
import { useState } from 'react';
import { useForm } from 'react-hook-form';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Trash2 } from 'lucide-react';
import { customerSchema, preparePayload } from './Create';
import type { CustomerFormData } from './Create';

interface Customer {
    id: number;
    name: string;
    phone: string | null;
    email: string | null;
    address: string | null;
    notes: string | null;
    is_active: boolean;
}

interface EditProps {
    customer: Customer;
}

export default function CustomerEdit({ customer }: EditProps) {
    const [deleteOpen, setDeleteOpen] = useState(false);

    const {
        register,
        handleSubmit,
        setValue,
        watch,
        formState: { errors, isSubmitting },
    } = useForm<CustomerFormData>({
        resolver: zodResolver(customerSchema),
        defaultValues: {
            name: customer.name,
            phone: customer.phone ?? '',
            email: customer.email ?? '',
            address: customer.address ?? '',
            notes: customer.notes ?? '',
            is_active: customer.is_active,
        },
    });

    function onSubmit(data: CustomerFormData) {
        router.put(`/dashboard/customers/${customer.id}`, preparePayload(data));
    }

    function onDelete() {
        router.delete(`/dashboard/customers/${customer.id}`);
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Customers', href: '/dashboard/customers' },
                { title: customer.name, href: `/dashboard/customers/${customer.id}` },
                { title: 'Edit', href: `/dashboard/customers/${customer.id}/edit` },
            ]}
        >
            <Head title={`Edit ${customer.name} | Mosul Boulevard`} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <Link
                    href={`/dashboard/customers/${customer.id}`}
                    className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to {customer.name}
                </Link>

                <form onSubmit={handleSubmit(onSubmit)}>
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle>Edit {customer.name}</CardTitle>
                                <Dialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                                    <DialogTrigger asChild>
                                        <Button type="button" variant="destructive" size="sm">
                                            <Trash2 className="mr-1 h-4 w-4" />
                                            Delete
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <DialogHeader>
                                            <DialogTitle>Delete {customer.name}?</DialogTitle>
                                            <DialogDescription>
                                                This will soft-delete the customer. Linked villas and tower units will
                                                remain but the customer reference will be cleared.
                                            </DialogDescription>
                                        </DialogHeader>
                                        <DialogFooter>
                                            <DialogClose asChild>
                                                <Button variant="outline">Cancel</Button>
                                            </DialogClose>
                                            <Button variant="destructive" onClick={onDelete}>
                                                Delete Customer
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                {/* Name */}
                                <div className="space-y-2">
                                    <Label htmlFor="name">Name *</Label>
                                    <Input id="name" {...register('name')} />
                                    {errors.name && (
                                        <p className="text-destructive text-sm">{errors.name.message}</p>
                                    )}
                                </div>

                                {/* Phone */}
                                <div className="space-y-2">
                                    <Label htmlFor="phone">Phone</Label>
                                    <Input id="phone" {...register('phone')} />
                                </div>

                                {/* Email */}
                                <div className="space-y-2">
                                    <Label htmlFor="email">Email</Label>
                                    <Input id="email" type="email" {...register('email')} />
                                    {errors.email && (
                                        <p className="text-destructive text-sm">{errors.email.message}</p>
                                    )}
                                </div>

                                {/* Active */}
                                <div className="flex items-center gap-2 pt-6">
                                    <Checkbox
                                        id="is_active"
                                        checked={watch('is_active')}
                                        onCheckedChange={(v) => setValue('is_active', v === true)}
                                    />
                                    <Label htmlFor="is_active" className="cursor-pointer">
                                        Active
                                    </Label>
                                </div>

                                {/* Address */}
                                <div className="space-y-2 md:col-span-2">
                                    <Label htmlFor="address">Address</Label>
                                    <Textarea id="address" rows={3} {...register('address')} />
                                </div>

                                {/* Notes */}
                                <div className="space-y-2 md:col-span-2">
                                    <Label htmlFor="notes">Notes</Label>
                                    <Textarea id="notes" rows={4} {...register('notes')} />
                                </div>
                            </div>

                            {/* Actions */}
                            <div className="flex gap-3">
                                <Button
                                    type="submit"
                                    disabled={isSubmitting}
                                    className="bg-mbp-gold hover:bg-mbp-gold/90"
                                >
                                    Save Changes
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <Link href={`/dashboard/customers/${customer.id}`}>Cancel</Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </AppLayout>
    );
}
