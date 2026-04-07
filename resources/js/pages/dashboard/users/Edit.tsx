import { Head, Link, router } from '@inertiajs/react';
import { zodResolver } from '@hookform/resolvers/zod';
import { useForm } from 'react-hook-form';
import { z } from 'zod';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ArrowLeft } from 'lucide-react';
import { ROLE_LABELS } from './Create';

interface EditUser {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface EditProps {
    user: EditUser;
    roles: string[];
}

// On edit, password is optional. If provided, it must be at least 8 chars and
// match the confirmation. If left blank, both fields are ignored entirely.
const editUserSchema = z
    .object({
        name: z.string().min(1, 'Name is required').max(255),
        email: z.string().email('Invalid email').max(255),
        password: z.string().optional().default(''),
        password_confirmation: z.string().optional().default(''),
        role: z.string().min(1, 'Role is required'),
    })
    .refine(
        (data) => {
            if (!data.password) return true;
            return data.password.length >= 8;
        },
        {
            message: 'Password must be at least 8 characters',
            path: ['password'],
        },
    )
    .refine((data) => data.password === data.password_confirmation, {
        message: 'Passwords do not match',
        path: ['password_confirmation'],
    });

type EditFormData = z.infer<typeof editUserSchema>;

function preparePayload(data: EditFormData) {
    const payload: Record<string, string> = {
        name: data.name,
        email: data.email,
        role: data.role,
    };

    if (data.password) {
        payload.password = data.password;
        payload.password_confirmation = data.password_confirmation;
    }

    return payload;
}

export default function UserEdit({ user, roles }: EditProps) {
    const {
        register,
        handleSubmit,
        setValue,
        watch,
        formState: { errors, isSubmitting },
    } = useForm<EditFormData>({
        resolver: zodResolver(editUserSchema),
        defaultValues: {
            name: user.name,
            email: user.email,
            password: '',
            password_confirmation: '',
            role: user.role,
        },
    });

    function onSubmit(data: EditFormData) {
        router.put(`/dashboard/users/${user.id}`, preparePayload(data));
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Users', href: '/dashboard/users' },
                { title: user.name, href: `/dashboard/users/${user.id}/edit` },
            ]}
        >
            <Head title={`Edit ${user.name} | Mosul Boulevard`} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <Link
                    href="/dashboard/users"
                    className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Users
                </Link>

                <form onSubmit={handleSubmit(onSubmit)}>
                    <Card>
                        <CardHeader>
                            <CardTitle>Edit {user.name}</CardTitle>
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

                                {/* Email */}
                                <div className="space-y-2">
                                    <Label htmlFor="email">Email *</Label>
                                    <Input id="email" type="email" {...register('email')} />
                                    {errors.email && (
                                        <p className="text-destructive text-sm">{errors.email.message}</p>
                                    )}
                                </div>

                                {/* Password */}
                                <div className="space-y-2">
                                    <Label htmlFor="password">New Password</Label>
                                    <Input
                                        id="password"
                                        type="password"
                                        placeholder="Leave blank to keep current"
                                        {...register('password')}
                                    />
                                    {errors.password && (
                                        <p className="text-destructive text-sm">{errors.password.message}</p>
                                    )}
                                </div>

                                {/* Confirm password */}
                                <div className="space-y-2">
                                    <Label htmlFor="password_confirmation">Confirm New Password</Label>
                                    <Input
                                        id="password_confirmation"
                                        type="password"
                                        placeholder="Leave blank to keep current"
                                        {...register('password_confirmation')}
                                    />
                                    {errors.password_confirmation && (
                                        <p className="text-destructive text-sm">
                                            {errors.password_confirmation.message}
                                        </p>
                                    )}
                                </div>

                                {/* Role */}
                                <div className="space-y-2 md:col-span-2">
                                    <Label>Role *</Label>
                                    <Select
                                        value={watch('role')}
                                        onValueChange={(v) => setValue('role', v)}
                                    >
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="Select role" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {roles.map((role) => (
                                                <SelectItem key={role} value={role}>
                                                    {ROLE_LABELS[role] ?? role}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.role && (
                                        <p className="text-destructive text-sm">{errors.role.message}</p>
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
                                    Save Changes
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <Link href="/dashboard/users">Cancel</Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </AppLayout>
    );
}
