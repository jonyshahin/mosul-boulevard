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

interface CreateProps {
    roles: string[];
}

const userSchema = z
    .object({
        name: z.string().min(1, 'Name is required').max(255),
        email: z.string().email('Invalid email').max(255),
        password: z.string().min(8, 'Password must be at least 8 characters'),
        password_confirmation: z.string(),
        role: z.string().min(1, 'Role is required'),
    })
    .refine((data) => data.password === data.password_confirmation, {
        message: 'Passwords do not match',
        path: ['password_confirmation'],
    });

type UserFormData = z.infer<typeof userSchema>;

function preparePayload(data: UserFormData) {
    return {
        name: data.name,
        email: data.email,
        password: data.password,
        password_confirmation: data.password_confirmation,
        role: data.role,
    };
}

const ROLE_LABELS: Record<string, string> = {
    admin: 'Admin',
    engineer: 'Engineer',
    viewer: 'Viewer',
};

export { userSchema, preparePayload, ROLE_LABELS };
export type { UserFormData };

export default function UserCreate({ roles }: CreateProps) {
    const {
        register,
        handleSubmit,
        setValue,
        watch,
        formState: { errors, isSubmitting },
    } = useForm<UserFormData>({
        resolver: zodResolver(userSchema),
        defaultValues: {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            role: '',
        },
    });

    function onSubmit(data: UserFormData) {
        router.post('/dashboard/users', preparePayload(data));
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Users', href: '/dashboard/users' },
                { title: 'Create', href: '/dashboard/users/create' },
            ]}
        >
            <Head title="Create User | Mosul Boulevard" />
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
                            <CardTitle>Create User</CardTitle>
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
                                    <Label htmlFor="password">Password *</Label>
                                    <Input id="password" type="password" {...register('password')} />
                                    {errors.password && (
                                        <p className="text-destructive text-sm">{errors.password.message}</p>
                                    )}
                                </div>

                                {/* Confirm password */}
                                <div className="space-y-2">
                                    <Label htmlFor="password_confirmation">Confirm Password *</Label>
                                    <Input
                                        id="password_confirmation"
                                        type="password"
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
                                    Create User
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
