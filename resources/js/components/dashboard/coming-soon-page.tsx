import { Head } from '@inertiajs/react';
import { Construction } from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

type Props = {
    title: string;
    comingSoon: string;
    breadcrumbs: BreadcrumbItem[];
};

export default function ComingSoonPage({ title, comingSoon, breadcrumbs }: Props) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${title} | Mosul Boulevard`} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center gap-3">
                    <h1 className="text-2xl font-bold tracking-tight">{title}</h1>
                </div>

                <Card className="border-dashed">
                    <CardContent className="flex flex-col items-center justify-center gap-4 py-16 text-center">
                        <div className="rounded-full bg-muted p-4">
                            <Construction className="h-8 w-8 text-muted-foreground" />
                        </div>
                        <p className="max-w-md text-muted-foreground">{comingSoon}</p>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
