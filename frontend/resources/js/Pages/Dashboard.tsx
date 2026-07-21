import { RecentActivityTable } from '@/components/dashboard/RecentActivityTable';
import { StatCards } from '@/components/dashboard/StatCards';
import { StatusBreakdownCard } from '@/components/dashboard/StatusBreakdownCard';
import { TopRegionsCard } from '@/components/dashboard/TopRegionsCard';
import AppLayout from '@/layouts/AppLayout';
import type { DashboardProps } from '@/types/dashboard';
import { Head } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';

export default function Dashboard({
    stats,
    activity,
    breakdown,
    regions,
}: DashboardProps) {
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

                <StatCards stats={stats} />

                <div className="grid gap-4 xl:grid-cols-[minmax(0,1fr)_280px]">
                    <RecentActivityTable activity={activity} />

                    <div className="flex flex-col gap-4">
                        <StatusBreakdownCard breakdown={breakdown} />
                        <TopRegionsCard regions={regions} />
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
