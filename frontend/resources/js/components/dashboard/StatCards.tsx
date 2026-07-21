import type { DashboardStat } from '@/types/dashboard';
import {
    AlertTriangle,
    Database,
    Globe,
    LucideIcon,
    Phone,
} from 'lucide-react';

const icons: Record<string, LucideIcon> = {
    records: Database,
    phones: Phone,
    alerts: AlertTriangle,
    countries: Globe,
};

export function StatCards({ stats }: { stats: DashboardStat[] }) {
    return (
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
    );
}
