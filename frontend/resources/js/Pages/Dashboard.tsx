import AuthenticatedLayout from '@/layouts/AuthenticatedLayout';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Head } from '@inertiajs/react';
import {
    AlertTriangle,
    ArrowRight,
    Database,
    Globe,
    Phone,
} from 'lucide-react';

type ActivityStatus = 'active' | 'paused' | 'failed';

const stats = [
    {
        label: 'Total Test Records',
        value: '23',
        hint: '17 active',
        icon: Database,
    },
    {
        label: 'Phone Numbers',
        value: '5',
        hint: 'Monitored globally',
        icon: Phone,
    },
    {
        label: 'Open Alerts',
        value: '3',
        hint: 'Require attention',
        icon: AlertTriangle,
    },
    {
        label: 'Countries',
        value: '11',
        hint: 'Coverage regions',
        icon: Globe,
    },
];

const recentActivity: {
    name: string;
    phone: string;
    module: string;
    status: ActivityStatus;
    region: string;
    updated: string;
}[] = [
    {
        name: 'US Toll-Free Regression Suite',
        phone: '+1 (800) 555-0101',
        module: 'Regression Tests',
        status: 'active',
        region: 'US',
        updated: '17 Jun, 13:10',
    },
    {
        name: 'UK Retail IVR Discovery',
        phone: '+44 20 7946 0958',
        module: 'Discovery Scans',
        status: 'active',
        region: 'GB',
        updated: '17 Jun, 12:45',
    },
    {
        name: 'APAC Banking Path Map',
        phone: '+65 6123 4567',
        module: 'Discovery Scans',
        status: 'paused',
        region: 'SG',
        updated: '17 Jun, 11:20',
    },
    {
        name: 'DE Support Line Regression',
        phone: '+49 30 123456',
        module: 'Regression Tests',
        status: 'failed',
        region: 'DE',
        updated: '16 Jun, 18:05',
    },
    {
        name: 'India Prepaid Menu Audit',
        phone: '+91 22 4000 1234',
        module: 'Regression Tests',
        status: 'active',
        region: 'IN',
        updated: '16 Jun, 16:40',
    },
    {
        name: 'Brazil Collections Discovery',
        phone: '+55 11 4002 8922',
        module: 'Discovery Scans',
        status: 'paused',
        region: 'BR',
        updated: '16 Jun, 14:15',
    },
    {
        name: 'CA Enterprise IVR Suite',
        phone: '+1 (416) 555-0199',
        module: 'Regression Tests',
        status: 'active',
        region: 'CA',
        updated: '16 Jun, 09:30',
    },
    {
        name: 'FR Hotel Booking Paths',
        phone: '+33 1 42 68 53 00',
        module: 'Discovery Scans',
        status: 'active',
        region: 'FR',
        updated: '15 Jun, 21:10',
    },
    {
        name: 'AU Telco Regression Pack',
        phone: '+61 2 9876 5432',
        module: 'Regression Tests',
        status: 'failed',
        region: 'AU',
        updated: '15 Jun, 17:55',
    },
    {
        name: 'JP Customer Care Scan',
        phone: '+81 3 1234 5678',
        module: 'Discovery Scans',
        status: 'active',
        region: 'JP',
        updated: '15 Jun, 08:20',
    },
];

const statusBreakdown = [
    { label: 'Active', count: 17, percent: 74, color: 'bg-emerald-500' },
    { label: 'Paused', count: 3, percent: 13, color: 'bg-orange-400' },
    { label: 'Failed', count: 2, percent: 9, color: 'bg-red-500' },
    { label: 'Other', count: 1, percent: 4, color: 'bg-zinc-300' },
];

const topRegions = [
    { region: 'US', records: 6 },
    { region: 'GB', records: 4 },
    { region: 'DE', records: 3 },
    { region: 'IN', records: 3 },
    { region: 'FR', records: 2 },
];

function statusBadge(status: ActivityStatus) {
    if (status === 'active') {
        return <Badge className="rounded-full px-2.5 capitalize">active</Badge>;
    }

    if (status === 'failed') {
        return (
            <Badge
                variant="destructive"
                className="rounded-full px-2.5 capitalize"
            >
                failed
            </Badge>
        );
    }

    return (
        <Badge
            variant="secondary"
            className="rounded-full px-2.5 capitalize"
        >
            paused
        </Badge>
    );
}

export default function Dashboard() {
    return (
        <AuthenticatedLayout>
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
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <Button variant="outline">Regression Tests</Button>
                        <Button>
                            Discovery Scans
                            <ArrowRight />
                        </Button>
                    </div>
                </div>

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    {stats.map((stat) => (
                        <div
                            key={stat.label}
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
                                    <stat.icon className="size-4" />
                                </div>
                            </div>
                        </div>
                    ))}
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
                            <Badge variant="secondary" className="rounded-full">
                                10 records
                            </Badge>
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[640px] text-left text-sm">
                                <thead className="bg-muted/40 text-muted-foreground">
                                    <tr className="border-b">
                                        <th className="px-4 py-2.5 font-medium">
                                            Name
                                        </th>
                                        <th className="px-4 py-2.5 font-medium">
                                            Module
                                        </th>
                                        <th className="px-4 py-2.5 font-medium">
                                            Status
                                        </th>
                                        <th className="px-4 py-2.5 font-medium">
                                            Region
                                        </th>
                                        <th className="px-4 py-2.5 font-medium">
                                            Updated
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {recentActivity.map((row) => (
                                        <tr
                                            key={row.name}
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
                                                {statusBadge(row.status)}
                                            </td>
                                            <td className="px-4 py-3 font-medium">
                                                {row.region}
                                            </td>
                                            <td className="text-muted-foreground px-4 py-3">
                                                {row.updated}
                                            </td>
                                        </tr>
                                    ))}
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
                                {statusBreakdown.map((item) => (
                                    <div key={item.label} className="space-y-1.5">
                                        <div className="flex items-center justify-between text-sm">
                                            <span>{item.label}</span>
                                            <span className="text-muted-foreground">
                                                {item.count} ({item.percent}%)
                                            </span>
                                        </div>
                                        <div className="bg-muted h-2 overflow-hidden rounded-full">
                                            <div
                                                className={`h-full rounded-full ${item.color}`}
                                                style={{
                                                    width: `${item.percent}%`,
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
                                {topRegions.map((item) => (
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
        </AuthenticatedLayout>
    );
}
