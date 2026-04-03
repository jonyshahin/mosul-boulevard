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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ArrowLeft } from 'lucide-react';

interface Option {
    id: number;
    name: string;
}

interface CreateProps {
    villaTypes: Option[];
    engineers: Option[];
    stages: Option[];
    statuses: Option[];
    structuralStatuses: Option[];
    finishingStatuses: Option[];
    facadeStatuses: Option[];
}

const villaSchema = z.object({
    code: z.string().min(1, 'Code is required'),
    villa_type_id: z.coerce.number().min(1, 'Villa type is required'),
    is_sold: z.boolean().default(false),
    customer_name: z.string().optional().default(''),
    sale_date: z.string().optional().default(''),
    engineer_id: z.string().optional().default(''),
    current_stage_id: z.string().optional().default(''),
    status_option_id: z.string().optional().default(''),
    structural_status_id: z.string().optional().default(''),
    finishing_status_id: z.string().optional().default(''),
    facade_status_id: z.string().optional().default(''),
    completion_pct: z.string().optional().default(''),
    planned_start: z.string().optional().default(''),
    planned_finish: z.string().optional().default(''),
    actual_start: z.string().optional().default(''),
    actual_finish: z.string().optional().default(''),
    acc_concrete_qty: z.string().optional().default(''),
    acc_steel_qty: z.string().optional().default(''),
});

type VillaFormData = z.infer<typeof villaSchema>;

function preparePayload(data: VillaFormData) {
    return {
        code: data.code,
        villa_type_id: data.villa_type_id,
        is_sold: data.is_sold,
        customer_name: data.customer_name || null,
        sale_date: data.sale_date || null,
        engineer_id: data.engineer_id ? Number(data.engineer_id) : null,
        current_stage_id: data.current_stage_id ? Number(data.current_stage_id) : null,
        status_option_id: data.status_option_id ? Number(data.status_option_id) : null,
        structural_status_id: data.structural_status_id ? Number(data.structural_status_id) : null,
        finishing_status_id: data.finishing_status_id ? Number(data.finishing_status_id) : null,
        facade_status_id: data.facade_status_id ? Number(data.facade_status_id) : null,
        completion_pct: data.completion_pct ? Number(data.completion_pct) : null,
        planned_start: data.planned_start || null,
        planned_finish: data.planned_finish || null,
        actual_start: data.actual_start || null,
        actual_finish: data.actual_finish || null,
        acc_concrete_qty: data.acc_concrete_qty ? Number(data.acc_concrete_qty) : null,
        acc_steel_qty: data.acc_steel_qty ? Number(data.acc_steel_qty) : null,
    };
}

function SelectField({
    label,
    options,
    value,
    onChange,
    placeholder,
}: {
    label: string;
    options: Option[];
    value: string;
    onChange: (v: string) => void;
    placeholder: string;
}) {
    return (
        <div className="space-y-2">
            <Label>{label}</Label>
            <Select value={value || 'none'} onValueChange={(v) => onChange(v === 'none' ? '' : v)}>
                <SelectTrigger className="w-full">
                    <SelectValue placeholder={placeholder} />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="none">None</SelectItem>
                    {options.map((o) => (
                        <SelectItem key={o.id} value={String(o.id)}>
                            {o.name}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
        </div>
    );
}

export { villaSchema, preparePayload, SelectField };
export type { VillaFormData, Option };

export default function VillaCreate({
    villaTypes,
    engineers,
    stages,
    statuses,
    structuralStatuses,
    finishingStatuses,
    facadeStatuses,
}: CreateProps) {
    const {
        register,
        handleSubmit,
        setValue,
        watch,
        formState: { errors, isSubmitting },
    } = useForm<VillaFormData>({
        resolver: zodResolver(villaSchema),
        defaultValues: {
            code: '',
            villa_type_id: 0,
            is_sold: false,
            customer_name: '',
            sale_date: '',
            engineer_id: '',
            current_stage_id: '',
            status_option_id: '',
            structural_status_id: '',
            finishing_status_id: '',
            facade_status_id: '',
            completion_pct: '',
            planned_start: '',
            planned_finish: '',
            actual_start: '',
            actual_finish: '',
            acc_concrete_qty: '',
            acc_steel_qty: '',
        },
    });

    function onSubmit(data: VillaFormData) {
        router.post('/dashboard/villas', preparePayload(data));
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Villas', href: '/dashboard/villas' },
                { title: 'Create', href: '/dashboard/villas/create' },
            ]}
        >
            <Head title="Create Villa | Mosul Boulevard" />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <Link
                    href="/dashboard/villas"
                    className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Villas
                </Link>

                <form onSubmit={handleSubmit(onSubmit)}>
                    <Card>
                        <CardHeader>
                            <CardTitle>Create Villa</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                {/* Code */}
                                <div className="space-y-2">
                                    <Label htmlFor="code">Code *</Label>
                                    <Input id="code" {...register('code')} />
                                    {errors.code && (
                                        <p className="text-sm text-destructive">{errors.code.message}</p>
                                    )}
                                </div>

                                {/* Villa Type */}
                                <div className="space-y-2">
                                    <Label>Villa Type *</Label>
                                    <Select
                                        value={watch('villa_type_id') ? String(watch('villa_type_id')) : ''}
                                        onValueChange={(v) => setValue('villa_type_id', Number(v))}
                                    >
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="Select type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {villaTypes.map((t) => (
                                                <SelectItem key={t.id} value={String(t.id)}>
                                                    {t.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.villa_type_id && (
                                        <p className="text-sm text-destructive">{errors.villa_type_id.message}</p>
                                    )}
                                </div>

                                {/* Customer Name */}
                                <div className="space-y-2">
                                    <Label htmlFor="customer_name">Customer Name</Label>
                                    <Input id="customer_name" {...register('customer_name')} />
                                </div>

                                {/* Sale Date */}
                                <div className="space-y-2">
                                    <Label htmlFor="sale_date">Sale Date</Label>
                                    <Input id="sale_date" type="date" {...register('sale_date')} />
                                </div>

                                {/* Is Sold */}
                                <div className="flex items-center gap-2 pt-6">
                                    <Checkbox
                                        id="is_sold"
                                        checked={watch('is_sold')}
                                        onCheckedChange={(v) => setValue('is_sold', v === true)}
                                    />
                                    <Label htmlFor="is_sold" className="cursor-pointer">
                                        Is Sold
                                    </Label>
                                </div>

                                {/* Engineer */}
                                <SelectField
                                    label="Engineer"
                                    options={engineers}
                                    value={watch('engineer_id')}
                                    onChange={(v) => setValue('engineer_id', v)}
                                    placeholder="Select engineer"
                                />

                                {/* Current Stage */}
                                <SelectField
                                    label="Current Stage"
                                    options={stages}
                                    value={watch('current_stage_id')}
                                    onChange={(v) => setValue('current_stage_id', v)}
                                    placeholder="Select stage"
                                />

                                {/* Status */}
                                <SelectField
                                    label="Status"
                                    options={statuses}
                                    value={watch('status_option_id')}
                                    onChange={(v) => setValue('status_option_id', v)}
                                    placeholder="Select status"
                                />

                                {/* Structural Status */}
                                <SelectField
                                    label="Structural Status"
                                    options={structuralStatuses}
                                    value={watch('structural_status_id')}
                                    onChange={(v) => setValue('structural_status_id', v)}
                                    placeholder="Select structural status"
                                />

                                {/* Finishing Status */}
                                <SelectField
                                    label="Finishing Status"
                                    options={finishingStatuses}
                                    value={watch('finishing_status_id')}
                                    onChange={(v) => setValue('finishing_status_id', v)}
                                    placeholder="Select finishing status"
                                />

                                {/* Facade Status */}
                                <SelectField
                                    label="Facade Status"
                                    options={facadeStatuses}
                                    value={watch('facade_status_id')}
                                    onChange={(v) => setValue('facade_status_id', v)}
                                    placeholder="Select facade status"
                                />

                                {/* Completion % */}
                                <div className="space-y-2">
                                    <Label htmlFor="completion_pct">Completion %</Label>
                                    <Input
                                        id="completion_pct"
                                        type="number"
                                        min={0}
                                        max={100}
                                        step="0.1"
                                        {...register('completion_pct')}
                                    />
                                </div>

                                {/* Planned Start */}
                                <div className="space-y-2">
                                    <Label htmlFor="planned_start">Planned Start</Label>
                                    <Input id="planned_start" type="date" {...register('planned_start')} />
                                </div>

                                {/* Planned Finish */}
                                <div className="space-y-2">
                                    <Label htmlFor="planned_finish">Planned Finish</Label>
                                    <Input id="planned_finish" type="date" {...register('planned_finish')} />
                                </div>

                                {/* Actual Start */}
                                <div className="space-y-2">
                                    <Label htmlFor="actual_start">Actual Start</Label>
                                    <Input id="actual_start" type="date" {...register('actual_start')} />
                                </div>

                                {/* Actual Finish */}
                                <div className="space-y-2">
                                    <Label htmlFor="actual_finish">Actual Finish</Label>
                                    <Input id="actual_finish" type="date" {...register('actual_finish')} />
                                </div>

                                {/* Concrete Qty */}
                                <div className="space-y-2">
                                    <Label htmlFor="acc_concrete_qty">Concrete Qty (m³)</Label>
                                    <Input
                                        id="acc_concrete_qty"
                                        type="number"
                                        min={0}
                                        step="0.01"
                                        {...register('acc_concrete_qty')}
                                    />
                                </div>

                                {/* Steel Qty */}
                                <div className="space-y-2">
                                    <Label htmlFor="acc_steel_qty">Steel Qty (kg)</Label>
                                    <Input
                                        id="acc_steel_qty"
                                        type="number"
                                        min={0}
                                        step="0.01"
                                        {...register('acc_steel_qty')}
                                    />
                                </div>
                            </div>

                            {/* Actions */}
                            <div className="flex gap-3">
                                <Button type="submit" disabled={isSubmitting} className="bg-mbp-gold hover:bg-mbp-gold/90">
                                    Create Villa
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <Link href="/dashboard/villas">Cancel</Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </AppLayout>
    );
}
