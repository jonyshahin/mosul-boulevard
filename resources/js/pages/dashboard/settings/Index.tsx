import { Head, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { toast, Toaster } from 'sonner';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Settings2 } from 'lucide-react';

interface SettingsIndexProps {
    settings: Record<string, string>;
}

export default function SettingsIndex({ settings }: SettingsIndexProps) {
    const { flash } = usePage().props as unknown as { flash: { success?: string } };
    const [form, setForm] = useState({
        contact_phone: settings.contact_phone ?? '',
        contact_email: settings.contact_email ?? '',
        contact_address: settings.contact_address ?? '',
        contact_whatsapp: settings.contact_whatsapp ?? '',
        contact_working_hours: settings.contact_working_hours ?? '',
    });
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        if (flash?.success) {
            toast.success(flash.success);
        }
    }, [flash?.success]);

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setSubmitting(true);
        router.put('/dashboard/settings', form, {
            onFinish: () => setSubmitting(false),
        });
    }

    function updateField(key: string, value: string) {
        setForm((prev) => ({ ...prev, [key]: value }));
    }

    return (
        <AppLayout breadcrumbs={[{ title: 'Dashboard', href: '/dashboard' }, { title: 'Settings', href: '/dashboard/settings' }]}>
            <Head title="Settings | Mosul Boulevard" />
            <Toaster position="top-right" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center gap-3">
                    <Settings2 className="h-6 w-6 text-mbp-gold" />
                    <h1 className="text-2xl font-bold tracking-tight">Settings</h1>
                </div>

                <form onSubmit={handleSubmit}>
                    <Card>
                        <CardHeader>
                            <CardTitle>Contact Information</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="contact_phone">Phone</Label>
                                    <Input
                                        id="contact_phone"
                                        value={form.contact_phone}
                                        onChange={(e) => updateField('contact_phone', e.target.value)}
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="contact_email">Email</Label>
                                    <Input
                                        id="contact_email"
                                        type="email"
                                        value={form.contact_email}
                                        onChange={(e) => updateField('contact_email', e.target.value)}
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="contact_address">Address</Label>
                                    <Input
                                        id="contact_address"
                                        value={form.contact_address}
                                        onChange={(e) => updateField('contact_address', e.target.value)}
                                    />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="contact_whatsapp">WhatsApp</Label>
                                    <Input
                                        id="contact_whatsapp"
                                        value={form.contact_whatsapp}
                                        onChange={(e) => updateField('contact_whatsapp', e.target.value)}
                                    />
                                </div>
                                <div className="space-y-2 md:col-span-2">
                                    <Label htmlFor="contact_working_hours">Working Hours</Label>
                                    <Input
                                        id="contact_working_hours"
                                        value={form.contact_working_hours}
                                        onChange={(e) => updateField('contact_working_hours', e.target.value)}
                                    />
                                </div>
                            </div>
                            <Button type="submit" disabled={submitting} className="bg-mbp-gold hover:bg-mbp-gold/90">
                                {submitting ? 'Saving...' : 'Save Settings'}
                            </Button>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </AppLayout>
    );
}
