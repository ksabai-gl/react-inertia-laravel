export type DashboardStatus = 'active' | 'paused' | 'failed';

export type DashboardStat = {
    key: string;
    label: string;
    value: string;
    hint: string;
};

export type DashboardActivity = {
    name: string;
    phone: string;
    module: string;
    status: DashboardStatus;
    region: string;
    updated: string;
};

export type DashboardBreakdownItem = {
    label: string;
    count: number;
    percent: number;
    color: string;
};

export type DashboardRegion = {
    region: string;
    records: number;
};

export type DashboardActivityMeta = {
    current_page: number;
    per_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

export type DashboardSummary = {
    stats: DashboardStat[];
    activity: DashboardActivity[];
    breakdown: DashboardBreakdownItem[];
    regions: DashboardRegion[];
    activity_meta?: DashboardActivityMeta;
};

export type DashboardProps = DashboardSummary;
