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

export type DashboardProps = {
    stats: DashboardStat[];
    activity: DashboardActivity[];
    breakdown: DashboardBreakdownItem[];
    regions: DashboardRegion[];
};
