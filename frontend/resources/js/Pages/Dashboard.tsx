import AppLayout from '@/layouts/AppLayout';
import { Head } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowRight,
    Database,
    Globe,
    LucideIcon,
    Phone,
} from 'lucide-react';
import { useMemo, useState } from 'react';

type Status = 'active' | 'paused' | 'failed';
type FilterValue = 'all';

type ActivityRow = {
    name: string;
    phone: string;
    module: string;
    status: Status;
    region: string;
    updated: string;
};

type ActivityFilters = {
    search: string;
    status: Status | FilterValue;
    module: string | FilterValue;
    region: string | FilterValue;
};

type DashboardProps = {
    stats: { key: string; label: string; value: string; hint: string }[];
    activity: ActivityRow[];
    breakdown: {
        label: string;
        count: number;
        percent: number;
        color: string;
    }[];
    regions: { region: string; records: number }[];
};

const icons: Record<string, LucideIcon> = {
    records: Database,
    phones: Phone,
    alerts: AlertTriangle,
    countries: Globe,
};

const badgeClass: Record<Status, string> = {
    active: 'bg-primary text-primary-foreground',
    failed: 'bg-destructive text-white',
    paused: 'bg-secondary text-secondary-foreground',
};

const defaultFilters: ActivityFilters = {
    search: '',
    status: 'all',
    module: 'all',
    region: 'all',
};

const uniqueValues = <T extends string>(values: T[]) => Array.from(new Set(values));

const recordLabel = (count: number) => `${count} ${count === 1 ? 'record' : 'records'}`;

export default function Dashboard({
    stats,
    activity,
    breakdown,
    regions,
}: DashboardProps) {
    const [filters, setFilters] = useState<ActivityFilters>(defaultFilters);

    const statusOptions = useMemo(
        () => uniqueValues(activity.map((row) => row.status)),
        [activity],
    );
    const moduleOptions = useMemo(
        () => uniqueValues(activity.map((row) => row.module)),
        [activity],
    );
    const regionOptions = useMemo(
        () => uniqueValues(activity.map((row) => row.region)),
        [activity],
    );

    const hasActiveFilters =
        filters.search.trim() !== '' ||
        filters.status !== 'all' ||
        filters.module !== 'all' ||
        filters.region !== 'all';

    const filteredActivity = useMemo(() => {
        const searchTerm = filters.search.trim().toLowerCase();

        return activity.filter((row) => {
            const matchesStatus =
                filters.status === 'all' || row.status === filters.status;
            const matchesModule =
                filters.module === 'all' || row.module === filters.module;
            const matchesRegion =
                filters.region === 'all' || row.region === filters.region;
            const searchableValues = [
                row.name,
                row.phone,
                row.module,
                row.region,
                row.status,
            ];
            const matchesSearch =
                searchTerm === '' ||
                searchableValues.some((value) =>
                    value.toLowerCase().includes(searchTerm),
                );

            return matchesStatus && matchesModule && matchesRegion && matchesSearch;
        });
    }, [activity, filters]);

    const updateFilter = <Key extends keyof ActivityFilters>(
        key: Key,
        value: ActivityFilters[Key],
    ) => {
        setFilters((currentFilters) => ({
            ...currentFilters,
            [key]: value,
        }));
    };

    const clearFilters = () => setFilters(defaultFilters);

    const activityCountLabel = hasActiveFilters
        ? `${recordLabel(filteredActivity.length)} of ${recordLabel(activity.length)}`
        : recordLabel(activity.length);

    return (
        <AppLayout>
            <Head title="Dashboard" />
            <div className="flex flex-1 flex-col gap-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div className="space-y-1">
                        <h1 className="text-2xl font-semibold tracking-tight">
                            IVR Testing Platform
                        </h1>
                        <p className="text-muted-foreground max-w-2xl text-sm">
                            Live overview of regression tests, discovery scans,
                            call paths, and alerts across your IVR estate.
                        </p>
                        <p className="text-muted-foreground text-xs">
                            Data from PHP API{' '}
                            <code className="bg-muted rounded px-1 py-0.5">
                                GET /api/dashboard
                            </code>
                        </p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <button
                            type="button"
                            className="border-input bg-background hover:bg-muted inline-flex h-9 items-center rounded-md border px-3 text-sm"
                        >
                            Regression Tests
                        </button>
                        <button
                            type="button"
                            className="bg-primary text-primary-foreground inline-flex h-9 items-center gap-1 rounded-md px-3 text-sm"
                        >
                            Discovery Scans
                            <ArrowRight className="size-4" />
                        </button>
                    </div>
                </div>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {stats.map((stat) => {
                        const Icon = icons[stat.key] ?? Database;
                        return (
                            <div
                                key={stat.key}
                                className="bg-card rounded-xl border p-4 shadow-xs"
                            >
                                <div className="flex items-start justify-between gap-3">
                                    <div>
                                        <p className="text-muted-foreground text-sm">
                                            {stat.label}
                                        </p>
                                        <p className="mt-2 text-3xl font-semibold tracking-tight">
                                            {stat.value}
                                        </p>
                                        <p className="text-muted-foreground mt-1 text-xs">
                                            {stat.hint}
                                        </p>
                                    </div>
                                    <div className="bg-muted text-muted-foreground flex size-9 items-center justify-center rounded-lg">
                                        <Icon className="size-4" />
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>

                <div className="grid gap-4 xl:grid-cols-[minmax(0,1fr)_280px]">
                    <div className="bg-card overflow-hidden rounded-xl border shadow-xs">
                        <div className="flex items-center justify-between gap-3 border-b px-4 py-3">
                            <div>
                                <h2 className="font-semibold">Recent Activity</h2>
                                <p className="text-muted-foreground text-xs">
                                    Latest regression and discovery runs
                                </p>
                            </div>
                            <span className="bg-secondary text-secondary-foreground rounded-full px-2.5 py-0.5 text-xs">
                                {activityCountLabel}
                            </span>
                        </div>
                        <div className="grid gap-3 border-b px-4 py-4 md:grid-cols-[minmax(180px,1fr)_repeat(3,minmax(140px,180px))_auto] md:items-end">
                            <label className="text-muted-foreground flex flex-col gap-1.5 text-xs font-medium">
                                Search activity
                                <input
                                    type="search"
                                    value={filters.search}
                                    onChange={(event) =>
                                        updateFilter('search', event.target.value)
                                    }
                                    placeholder="Name, phone, module, region..."
                                    className="border-input bg-background text-foreground placeholder:text-muted-foreground h-9 rounded-md border px-3 text-sm font-normal outline-none focus:ring-2 focus:ring-ring/40"
                                />
                            </label>
                            <label className="text-muted-foreground flex flex-col gap-1.5 text-xs font-medium">
                                Status
                                <select
                                    value={filters.status}
                                    onChange={(event) =>
                                        updateFilter(
                                            'status',
                                            event.target.value as ActivityFilters['status'],
                                        )
                                    }
                                    className="border-input bg-background text-foreground h-9 rounded-md border px-3 text-sm font-normal capitalize outline-none focus:ring-2 focus:ring-ring/40"
                                >
                                    <option value="all">All statuses</option>
                                    {statusOptions.map((status) => (
                                        <option key={status} value={status}>
                                            {status}
                                        </option>
                                    ))}
                                </select>
                            </label>
                            <label className="text-muted-foreground flex flex-col gap-1.5 text-xs font-medium">
                                Module
                                <select
                                    value={filters.module}
                                    onChange={(event) =>
                                        updateFilter('module', event.target.value)
                                    }
                                    className="border-input bg-background text-foreground h-9 rounded-md border px-3 text-sm font-normal outline-none focus:ring-2 focus:ring-ring/40"
                                >
                                    <option value="all">All modules</option>
                                    {moduleOptions.map((module) => (
                                        <option key={module} value={module}>
                                            {module}
                                        </option>
                                    ))}
                                </select>
                            </label>
                            <label className="text-muted-foreground flex flex-col gap-1.5 text-xs font-medium">
                                Region
                                <select
                                    value={filters.region}
                                    onChange={(event) =>
                                        updateFilter('region', event.target.value)
                                    }
                                    className="border-input bg-background text-foreground h-9 rounded-md border px-3 text-sm font-normal outline-none focus:ring-2 focus:ring-ring/40"
                                >
                                    <option value="all">All regions</option>
                                    {regionOptions.map((region) => (
                                        <option key={region} value={region}>
                                            {region}
                                        </option>
                                    ))}
                                </select>
                            </label>
                            <button
                                type="button"
                                onClick={clearFilters}
                                disabled={!hasActiveFilters}
                                className="border-input bg-background hover:bg-muted disabled:text-muted-foreground inline-flex h-9 items-center justify-center rounded-md border px-3 text-sm disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                Clear filters
                            </button>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[640px] text-left text-sm">
                                <thead className="bg-muted/40 text-muted-foreground">
                                    <tr className="border-b">
                                        <th className="px-4 py-2.5 font-medium">Name</th>
                                        <th className="px-4 py-2.5 font-medium">Module</th>
                                        <th className="px-4 py-2.5 font-medium">Status</th>
                                        <th className="px-4 py-2.5 font-medium">Region</th>
                                        <th className="px-4 py-2.5 font-medium">Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filteredActivity.length > 0 ? (
                                        filteredActivity.map((row) => (
                                            <tr
                                                key={`${row.name}-${row.phone}`}
                                                className="border-b last:border-0"
                                            >
                                                <td className="px-4 py-3">
                                                    <div className="font-medium">
                                                        {row.name}
                                                    </div>
                                                    <div className="text-muted-foreground text-xs">
                                                        {row.phone}
                                                    </div>
                                                </td>
                                                <td className="text-muted-foreground px-4 py-3">
                                                    {row.module}
                                                </td>
                                                <td className="px-4 py-3">
                                                    <span
                                                        className={`inline-flex rounded-full px-2.5 py-0.5 text-xs capitalize ${badgeClass[row.status]}`}
                                                    >
                                                        {row.status}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 font-medium">
                                                    {row.region}
                                                </td>
                                                <td className="text-muted-foreground px-4 py-3">
                                                    {row.updated}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td
                                                colSpan={5}
                                                className="text-muted-foreground px-4 py-8 text-center"
                                            >
                                                No activity matches the selected filters. Adjust
                                                or clear filters to see all records.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div className="flex flex-col gap-4">
                        <div className="bg-card rounded-xl border p-4 shadow-xs">
                            <h2 className="font-semibold">Status Breakdown</h2>
                            <p className="text-muted-foreground mt-1 text-xs">
                                Share of current test records
                            </p>
                            <div className="mt-4 space-y-3">
                                {breakdown.map((item) => (
                                    <div key={item.label} className="space-y-1.5">
                                        <div className="flex items-center justify-between text-sm">
                                            <span>{item.label}</span>
                                            <span className="text-muted-foreground">
                                                {item.count} ({item.percent}%)
                                            </span>
                                        </div>
                                        <div className="bg-muted h-2 overflow-hidden rounded-full">
                                            <div
                                                className="h-full rounded-full"
                                                style={{
                                                    width: `${item.percent}%`,
                                                    backgroundColor: item.color,
                                                }}
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="bg-card rounded-xl border p-4 shadow-xs">
                            <h2 className="font-semibold">Top Regions</h2>
                            <p className="text-muted-foreground mt-1 text-xs">
                                Highest record volume
                            </p>
                            <div className="mt-4 space-y-3">
                                {regions.map((item) => (
                                    <div
                                        key={item.region}
                                        className="flex items-center justify-between text-sm"
                                    >
                                        <span className="font-medium">
                                            {item.region}
                                        </span>
                                        <span className="text-muted-foreground">
                                            {item.records} records
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
