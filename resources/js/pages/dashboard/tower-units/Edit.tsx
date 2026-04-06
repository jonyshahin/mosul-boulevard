import { Head, Link, router } from '@inertiajs/react';
import { zodResolver } from '@hookform/resolvers/zod';
import { useMemo, useState } from 'react';
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
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Trash2 } from 'lucide-react';
import { towerUnitSchema, preparePayload, SelectField } from './Create';
import type { TowerUnitFormData, Option, FloorDef } from './Create';

interface TowerUnit {
    id: number;
    code: string;
    tower_definition_id: number;
    floor_definition_id: number | null;
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
    remarks: string | null;
}

interface EditProps {
    towerUnit: TowerUnit;
    towerDefinitions: Option[];
    floorDefinitions: FloorDef[];
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

export default function TowerUnitEdit({
    towerUnit,
    towerDefinitions,
    floorDefinitions,
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
    } = useForm<TowerUnitFormData>({
        resolver: zodResolver(towerUnitSchema),
        defaultValues: {
            code: towerUnit.code,
            tower_definition_id: towerUnit.tower_definition_id,
            floor_definition_id: numToStr(towerUnit.floor_definition_id),
            is_sold: towerUnit.is_sold,
            customer_id: numToStr(towerUnit.customer_id),
            customer_name: towerUnit.customer_name ?? '',
            sale_date: dateToInput(towerUnit.sale_date),
            engineer_id: numToStr(towerUnit.engineer_id),
            current_stage_id: numToStr(towerUnit.current_stage_id),
            status_option_id: numToStr(towerUnit.status_option_id),
            structural_status_id: numToStr(towerUnit.structural_status_id),
            finishing_status_id: numToStr(towerUnit.finishing_status_id),
            facade_status_id: numToStr(towerUnit.facade_status_id),
            completion_pct: numToStr(towerUnit.completion_pct),
            planned_start: dateToInput(towerUnit.planned_start),
            planned_finish: dateToInput(towerUnit.planned_finish),
            actual_start: dateToInput(towerUnit.actual_start),
            actual_finish: dateToInput(towerUnit.actual_finish),
            acc_concrete_qty: numToStr(towerUnit.acc_concrete_qty),
            acc_steel_qty: numToStr(towerUnit.acc_steel_qty),
            remarks: towerUnit.remarks ?? '',
        },
    });

    const selectedTowerId = watch('tower_definition_id');
    const filteredFloors = useMemo(
        () => floorDefinitions.filter((f) => f.tower_definition_id === selectedTowerId),
        [floorDefinitions, selectedTowerId],
    );

    function onSubmit(data: TowerUnitFormData) {
        router.put(`/dashboard/tower-units/${towerUnit.id}`, preparePayload(data));
    }

    function onDelete() {
        router.delete(`/dashboard/tower-units/${towerUnit.id}`);
    }

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Tower Units', href: '/dashboard/tower-units' },
                { title: towerUnit.code, href: `/dashboard/tower-units/${towerUnit.id}` },
                { title: 'Edit', href: `/dashboard/tower-units/${towerUnit.id}/edit` },
            ]}
        >
            <Head title={`Edit Unit ${towerUnit.code} | Mosul Boulevard`} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <Link
                    href={`/dashboard/tower-units/${towerUnit.id}`}
                    className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm transition-colors"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Back to Unit {towerUnit.code}
                </Link>

                <form onSubmit={handleSubmit(onSubmit)}>
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle>Edit Unit {towerUnit.code}</CardTitle>
                                <Dialog open={deleteOpen} onOpenChange={setDeleteOpen}>
                                    <DialogTrigger asChild>
                                        <Button type="button" variant="destructive" size="sm">
                                            <Trash2 className="mr-1 h-4 w-4" />
                                            Delete
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <DialogHeader>
                                            <DialogTitle>Delete Unit {towerUnit.code}?</DialogTitle>
                                            <DialogDescription>
                                                This will soft-delete the tower unit. It can be restored later if needed.
                                            </DialogDescription>
                                        </DialogHeader>
                                        <DialogFooter>
                                            <DialogClose asChild>
                                                <Button variant="outline">Cancel</Button>
                                            </DialogClose>
                                            <Button variant="destructive" onClick={onDelete}>
                                                Delete Unit
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

                                {/* Tower Definition */}
                                <div className="space-y-2">
                                    <Label>Tower *</Label>
                                    <Select
                                        value={selectedTowerId ? String(selectedTowerId) : ''}
                                        onValueChange={(v) => {
                                            setValue('tower_definition_id', Number(v));
                                            setValue('floor_definition_id', '');
                                        }}
                                    >
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="Select tower" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {towerDefinitions.map((t) => (
                                                <SelectItem key={t.id} value={String(t.id)}>
                                                    {t.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {errors.tower_definition_id && (
                                        <p className="text-sm text-destructive">{errors.tower_definition_id.message}</p>
                                    )}
                                </div>

                                {/* Floor Definition */}
                                <SelectField
                                    label="Floor"
                                    options={filteredFloors}
                                    value={watch('floor_definition_id')}
                                    onChange={(v) => setValue('floor_definition_id', v)}
                                    placeholder="Select floor"
                                />

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

                                {/* Remarks */}
                                <div className="space-y-2 md:col-span-2">
                                    <Label htmlFor="remarks">Remarks</Label>
                                    <Textarea id="remarks" rows={3} {...register('remarks')} />
                                </div>
                            </div>

                            {/* Actions */}
                            <div className="flex gap-3">
                                <Button type="submit" disabled={isSubmitting} className="bg-mbp-blue hover:bg-mbp-blue/90">
                                    Save Changes
                                </Button>
                                <Button type="button" variant="outline" asChild>
                                    <Link href={`/dashboard/tower-units/${towerUnit.id}`}>Cancel</Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </form>
            </div>
        </AppLayout>
    );
}
