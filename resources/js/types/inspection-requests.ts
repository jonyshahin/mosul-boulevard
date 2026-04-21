export type SeverityValue = 'low' | 'medium' | 'high' | 'critical';

export type StatusValue = 'open' | 'in_progress' | 'resolved' | 'verified' | 'closed' | 'reopened';

export type CategoryValue = 'qaqc' | 'safety' | 'materials' | 'other';

export type SubjectTypeToken = 'villa' | 'tower_unit';

export type LabeledColoredValue<V extends string> = {
    value: V;
    label: string;
    color: string;
};

export type LabeledValue<V extends string> = {
    value: V;
    label: string;
};

export type UserRef = {
    id: number;
    name: string;
    email?: string;
    role?: string;
    phone?: string | null;
};

export type SubjectRef = {
    type: SubjectTypeToken | string;
    id: number;
    code: string | null;
    display_name: string;
};

export type RequestTypeRef = {
    id: number;
    name: string;
    category: LabeledValue<CategoryValue | string>;
    color: string;
    is_active: boolean;
    sort_order: number;
};

export type InspectionRequestSummary = {
    id: number;
    title: string;
    location_detail: string | null;
    severity: LabeledColoredValue<SeverityValue>;
    status: LabeledColoredValue<StatusValue>;
    due_date: string | null;
    is_overdue: boolean;
    resolved_at: string | null;
    verified_at: string | null;
    closed_at: string | null;
    created_at: string;
    updated_at: string;
    requester: UserRef | null;
    assignee: UserRef | null;
    verified_by?: UserRef | null;
    request_type: RequestTypeRef | null;
    subject: SubjectRef | null;
    replies_count?: number;
    media_count?: number;
};

export type RequestReplyItem = {
    id: number;
    body: string;
    triggers_status: LabeledValue<StatusValue> | null;
    author: UserRef | null;
    created_at: string;
};

export type InspectionRequestDetail = InspectionRequestSummary & {
    description: string;
    replies: RequestReplyItem[];
};

export type PaginationMeta = {
    current_page: number;
    from: number | null;
    last_page: number;
    per_page: number;
    to: number | null;
    total: number;
};

export type PaginationLinks = {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
};

export type PaginatedInspectionRequests = {
    data: InspectionRequestSummary[];
    meta: PaginationMeta;
    links: PaginationLinks;
};

export type InspectionListFilters = {
    status: StatusValue[];
    severity: SeverityValue[];
    subject_type: SubjectTypeToken | null;
    assigned_to_me: boolean;
    overdue_only: boolean;
    sort: string;
};

export type InspectionSharedTranslations = {
    severity: Record<SeverityValue, string>;
    status: Record<StatusValue, string>;
    category: Record<string, string>;
    errors: Record<string, string>;
};
