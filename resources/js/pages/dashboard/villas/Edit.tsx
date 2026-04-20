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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ArrowLeft, Trash2 } from 'lucide-react';
import { villaSchema, preparePayload, SelectField } from './Create';
import type { VillaFormData, Option } from './Create';

interface Villa {
    id: number;
    code: string;
    villa_type_id: number;
    is_sold: boolean;
    customer_id: number | null;
    customer_name: string | null;
    sale_date: string | null;
    engineer_id: number | null;
    current_stage_id: number | null;
    status_option_id: number | null;
    structural_status_id: number | null;
    finishing_status_id: number | null;
    facade_status_id: number | null;
    completion_pct: number | null;
    planned_start: string | null;
    planned_finish: string | null;
    actual_start: string | null;
    actual_finish: string | null;
    acc_concrete_qty: number | null;
    acc_steel_qty: number | null;
}

interface EditProps {
    villa: Villa;
    villaTypes: Option[];
    customers: Option[];
    engineers: Option[];
    stages: Option[];
    statuses: Option[];
    structuralStatuses: Option[];
    finishingStatuses: Option[];
    facadeStatuses: Option[];
}

function dateToInput(val: string | null): string {
    if (!val) return '';
    return val.substring(0, 10);
}

function numToStr(val: number | null): string {
    return val != null ? String(val) : '';
}

export default function VillaEdit({
    villa,
    villaTypes,
    customers,
    engineers,
    stages,
    statuses,
    structuralStatuses,
    finishingStatuses,
    facadeStatuses,
}: EditProps) {
    const [deleteOpen, setDeleteOpen] = useState(false);

    const {
        register,
        handleSubmit,
        setValue,
        watch,
        formState: { errors, isSubmitting },
    } = useForm<VillaFormData>({
        resolver: zodResolver(villaSchema),
        defaultValues: {
            code: villa.code,
            villa_type_id: villa.villa_type_id,
            is_sold: villa.is_sold,
            customer_id: numToStr(villa.customer_id),
            customer_name: villa.customer_name ?? '',
            sale_date: dateToInput(villa.sale_date),
            engineer_id: numToStr(villa.engineer_id),
            current_stage_id: numToStr(villa.current_stage_id),
            status_option_id: numToStr(villa.status_option_id),
            structural_status_id: numToStr(villa.structural_status_id),
            finishing_status_id: numToStr(villa.finishing_status_id),
            facade_status_id: numToStr(villa.facade_status_id),
            completion_pct: numToStr(villa.completion_pct),
            planned_start: dateToInput(villa.planned_start),
            planned_finish: dateToInput(villa.planned_finish),
            actual_start: dateToInput(villa.actual_start),
            actual_finish: dateToInput(villa.actual_finish),
            acc_concrete_qty: numToStr(villa.acc_concrete_qty),
            acc_steel_qty: numToStr(villa.acc_steel_qty),
        },
    });

    function onSubmit(data: VillaFormData) {
        router.put(`/dashboard/villas/${villa.id}`, preparePayload(data));
    }

    function onDelete() {
        router.delete(`/dashboard/villas/${villa.id}`);
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Villas', href: '/dashboard/villas' },
                { title: villa.code, href: `/dashboard/villas/${villa.id}` },
                { title: 'Edit', href: `/dashboard/villas/${villa.id}/edit` },
            ]}
        >
            <Head title={`Edit Villa ${villa.code} | Mosul Boulevard`} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <Link
                    href={`/dashboard/villas/${villa.id}`}
                    className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Villa {villa.code}
                </Link>

                <form onSubmit={handleSubmit(onSubmit)}>
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle>Edit Villa {villa.code}</CardTitle>
                                <Dialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                                    <DialogTrigger asChild>
                                        <Button type="button" variant="destructive" size="sm">
                                            <Trash2 className="mr-1 h-4 w-4" />
                                            Delete
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <DialogHeader>
                                            <DialogTitle>Delete Villa {villa.code}?</DialogTitle>
                                            <DialogDescription>
                                                This will soft-delete the villa. It can be restored later if needed.
                                            </DialogDescription>
                                        </DialogHeader>
                                        <DialogFooter>
                                            <DialogClose asChild>
                                                <Button variant="outline">Cancel</Button>
                                            </DialogClose>
                                            <Button variant="destructive" onClick={onDelete}>
                                                Delete Villa
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>
                            </div>
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

                                {/* Customer */}
                                <SelectField
                                    label="Customer"
                                    options={customers}
                                    value={watch('customer_id')}
                                    onChange={(v) => setValue('customer_id', v)}
                                    placeholder="Select customer"
                                />

                                {/* Customer Name (legacy / fallback) */}
                                <div className="space-y-2">
                                    <Label htmlFor="customer_name">Customer Name (legacy)</Label>
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
                                    Save Changes
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <Link href={`/dashboard/villas/${villa.id}`}>Cancel</Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </AppLayout>
    );
}
