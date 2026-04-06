import { Head, Link, router } from '@inertiajs/react';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { z } from 'zod';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft } from 'lucide-react';

const customerSchema = z.object({
    name: z.string().min(1, 'Name is required').max(255),
    phone: z.string().max(50).optional().default(''),
    email: z.string().email('Invalid email').max(255).optional().or(z.literal('')),
    address: z.string().max(1000).optional().default(''),
    notes: z.string().max(2000).optional().default(''),
    is_active: z.boolean().default(true),
});

type CustomerFormData = z.infer<typeof customerSchema>;

function preparePayload(data: CustomerFormData) {
    return {
        name: data.name,
        phone: data.phone || null,
        email: data.email || null,
        address: data.address || null,
        notes: data.notes || null,
        is_active: data.is_active,
    };
}

export { customerSchema, preparePayload };
export type { CustomerFormData };

export default function CustomerCreate() {
    const {
        register,
        handleSubmit,
        setValue,
        watch,
        formState: { errors, isSubmitting },
    } = useForm<CustomerFormData>({
        resolver: zodResolver(customerSchema),
        defaultValues: {
            name: '',
            phone: '',
            email: '',
            address: '',
            notes: '',
            is_active: true,
        },
    });

    function onSubmit(data: CustomerFormData) {
        router.post('/dashboard/customers', preparePayload(data));
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Customers', href: '/dashboard/customers' },
                { title: 'Create', href: '/dashboard/customers/create' },
            ]}
        >
            <Head title="Create Customer | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <Link
                    href="/dashboard/customers"
                    className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Customers
                </Link>

                <form onSubmit={handleSubmit(onSubmit)}>
                    <Card>
                        <CardHeader>
                            <CardTitle>Create Customer</CardTitle>
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
                                    {errors.phone && (
                                        <p className="text-destructive text-sm">{errors.phone.message}</p>
                                    )}
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
                                    {errors.address && (
                                        <p className="text-destructive text-sm">{errors.address.message}</p>
                                    )}
                                </div>

                                {/* Notes */}
                                <div className="space-y-2 md:col-span-2">
                                    <Label htmlFor="notes">Notes</Label>
                                    <Textarea id="notes" rows={4} {...register('notes')} />
                                    {errors.notes && (
                                        <p className="text-destructive text-sm">{errors.notes.message}</p>
                                    )}
                                </div>
                            </div>

                            {/* Actions */}
                            <div className="flex gap-3">
                                <Button
                                    type="submit"
                                    disabled={isSubmitting}
                                    className="bg-mbp-gold hover:bg-mbp-gold/90"
                                >
                                    Create Customer
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <Link href="/dashboard/customers">Cancel</Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </AppLayout>
    );
}
